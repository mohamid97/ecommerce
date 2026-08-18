# Plan: Fix Missing `getEffectiveDiscount` Method on `Bundel` Model

## Problem

`getEffectiveDiscount()` is called on `Bundel` instances in 7 resource files but is never defined on the model. This causes `BadMethodCallException` (HTTP 500) in cart/order/bundle endpoints.

Additionally, 4 of those resources (BundelResource/BundeDetailsResource in both front and admin) access `$this->bundle` — a non-existent property — because they wrap a `Bundel` model directly, so `$this` (proxied) IS the bundle.

## Root Cause

1. **Missing method**: No `getEffectiveDiscount` defined on `App\Models\Api\Ecommerce\Bundel`.
2. **Equivalent logic exists**: `PromotionPriceService::getBundleEffectiveDiscount(Bundel $bundle, float $basePrice, float $componentDiscountPrice): array` at `app/Services/Ecommerce/Product/PromotionPriceService.php:100` already computes the effective discount and returns `['discount', 'discount_type', 'source']`.
3. **Wrong property in 4 resources**: BundelResource / BundeDetailsResource (front + admin) call `$this->bundle->getEffectiveDiscount(...)` but should call `$this->getEffectiveDiscount(...)` since `$this` proxies to the `Bundel` model.

## Affected Files

| File | Issue |
|------|-------|
| `app/Models/Api/Ecommerce/Bundel.php` | Missing `getEffectiveDiscount` method |
| `app/Http/Resources/Api/Front/Ecommerce/BundelResource.php:25` | `$this->bundle->...` → `$this->...` |
| `app/Http/Resources/Api/Front/Ecommerce/BundelDetailsResourc.php:25` | `$this->bundle->...` → `$this->...` |
| `app/Http/Resources/Api/Admin/Ecommerce/Bundel/BundelResource.php:18` | `$this->bundle->...` → `$this->...` |
| `app/Http/Resources/Api/Admin/Ecommerce/Bundel/BundeDetailsResource.php:18` | `$this->bundle->...` → `$this->...` |

### Files that call correctly (only the missing method blocks them)

- `app/Http/Resources/Api/Front/Ecommerce/CartItemResource.php:23`
- `app/Http/Resources/Api/Front/Ecommerce/GuestCartItemResource.php:25`
- `app/Http/Resources/Api/Admin/Ecommerce/CartItemResource.php:20`

## Implementation Steps

### Step 1: Add `getEffectiveDiscount` to `Bundel` model

File: `app/Models/Api/Ecommerce/Bundel.php`

Add a method that delegates to the existing service method:

```php
public function getEffectiveDiscount(float $basePrice, float $componentDiscountPrice): array
{
    return app(\App\Services\Ecommerce\Product\PromotionPriceService::class)
        ->getBundleEffectiveDiscount($this, $basePrice, $componentDiscountPrice);
}
```

- `$this` is the `Bundel` instance (the `bundle` / `bundel` itself).
- `$basePrice` → total price before any discount (mapped to service's `$totalPrice`).
- `$componentDiscountPrice` → sum of component prices after individual discounts (mapped to service's `$totalDiscountPrice`).
- Returns `['discount' => float, 'discount_type' => 'percent', 'source' => string]`.

### Step 2: Fix `$this->bundle` → `$this` in 4 Bundel detail/list resources

In each of these files, change line 25 / 18:

```diff
- $effectiveDiscount = $this->bundle->getEffectiveDiscount(
+ $effectiveDiscount = $this->getEffectiveDiscount(
```

Files:
1. `app/Http/Resources/Api/Front/Ecommerce/BundelResource.php`
2. `app/Http/Resources/Api/Front/Ecommerce/BundelDetailsResourc.php`
3. `app/Http/Resources/Api/Admin/Ecommerce/Bundel/BundelResource.php`
4. `app/Http/Resources/Api/Admin/Ecommerce/Bundel/BundeDetailsResource.php`

### Step 3: Verify cart/order resources use correct signature

- Cart resources pass `(float) $this->total_before_discount, (float) $this->total_after_discount` — this matches the 2-parameter signature. No changes needed.
- OrderResource files do not call `getEffectiveDiscount` — no changes needed.

### Step 4: Validate

Run PHP lint on modified files and check for test infrastructure:

```bash
php -l app/Models/Api/Ecommerce/Bundel.php
php -l app/Http/Resources/Api/Front/Ecommerce/BundelResource.php
php -l app/Http/Resources/Api/Front/Ecommerce/BundelDetailsResourc.php
php -l app/Http/Resources/Api/Admin/Ecommerce/Bundel/BundelResource.php
php -l app/Http/Resources/Api/Admin/Ecommerce/Bundel/BundeDetailsResource.php
```

Check for test framework:
```bash
cat composer.json | grep -A5 '"scripts"'
```

If tests exist for bundles/carts:
```bash
php artisan test --filter=Bundle
php artisan test --filter=Cart
```

## Notes / Edge Cases

- The cart resources pass `total_after_discount` (final discounted price) as `componentDiscountPrice`. The service uses this as the initial `$bestPrice`. In cases where the bundle has its own discount, the service will re-evaluate and may attribute the discount to `bundle_own` or `component` depending on which gives the lower price. The reported `discount` percentage will be accurate; only the `source` attribution may differ in edge cases. This is pre-existing behavior in the service call signature.
- `getBundleEffectiveDiscount` requires `total_before_discount > 0` for percentage calc; guard exists (`$basePrice > 0`).
