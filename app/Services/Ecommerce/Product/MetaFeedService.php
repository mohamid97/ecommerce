<?php

namespace App\Services\Ecommerce\Product;

use App\Models\Api\Admin\Product;
use App\Models\Api\Ecommerce\ProductVariant;
use Illuminate\Support\Facades\Storage;

class MetaFeedService
{
    public function buildFeed(): string
    {
        $products = Product::query()
            ->active()
            ->whereNotNull('product_image')
            ->with([
                'brand.translations',
                'translations',
                'variants' => function ($q) {
                    $q->where('status', 'active')
                    //   ->where('stock', '>', 0)
                      ->whereNotNull('sale_price')
                      ->where('sale_price', '>', 0)
                      ->with([
                          'variants.option.translations',
                          'variants.optionValue.translations',
                      ]);
                },
            ])
            ->get();

        $xmlParts = [];

        foreach ($products as $product) {
            if ($product->has_options && $product->variants->isNotEmpty()) {
                // Generate an XML <item> for each active variant
                foreach ($product->variants as $variant) {
                    $xmlParts[] = $this->buildVariantItemXml($product, $variant);
                }
            } else {
                // Simple product without variants
                if (
                    ! empty($product->sale_price)
                    && $product->sale_price > 0
                    && $product->stock > 0
                ) {
                    $xmlParts[] = $this->buildSimpleItemXml($product);
                }
            }
        }

        $items       = implode(PHP_EOL, array_filter($xmlParts));
        $title       = e(config('app.name', 'Store'));
        $link        = e(rtrim(config('app.url', url('/')), '/'));
        $description = e(config('app.name', 'Store').' product catalog');

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
    <channel>
        <title>{$title}</title>
        <link>{$link}</link>
        <description>{$description}</description>
        {$items}
    </channel>
</rss>
XML;
    }

    // ─── 1. Simple Product (No variants) ──────────────────────────────────────

    protected function buildSimpleItemXml(Product $product): string
    {
        $id           = $this->escapeXml('product_' . $product->id);
        $title        = $this->escapeXml($this->productTitle($product));
        $description  = $this->escapeXml($this->productDescription($product));
        $link         = $this->escapeXml($this->buildProductUrl($product));
        $imageLink    = $this->escapeXml($this->buildImageUrl($product->product_image));
        $price        = $this->escapeXml($this->formatPrice($product->sale_price));
        $currency     = $this->escapeXml($this->resolveCurrency());
        $brand        = $this->escapeXml(optional($product->brand)->title ?? '');
        $availability = $this->escapeXml('in stock');
        $condition    = $this->escapeXml('new');
        $categoryTag  = $this->googleCategoryTag($product);

        return <<<XML
        <item>
            <g:id>{$id}</g:id>
            <title>{$title}</title>
            <description>{$description}</description>
            <link>{$link}</link>
            <g:image_link>{$imageLink}</g:image_link>
            <g:availability>{$availability}</g:availability>
            <g:condition>{$condition}</g:condition>
            <g:price>{$price} {$currency}</g:price>
            <g:brand>{$brand}</g:brand>
            {$categoryTag}
        </item>
XML;
    }

    // ─── 2. Variant Product (One <item> per variant) ──────────────────────────

    protected function buildVariantItemXml(Product $product, ProductVariant $variant): string
    {
        // Unique ID per variant + parent item_group_id to group variants together in Meta
        $id          = $this->escapeXml('product_' . $product->id . '_variant_' . $variant->id);
        $itemGroupId = $this->escapeXml('product_' . $product->id);

        // Variant title (e.g. "Red T-Shirt - XL")
        $variantFullName = $variant->variant_full_name ?: $variant->sku;
        $title           = $this->escapeXml(
            $this->productTitle($product) . ($variantFullName ? ' - ' . $variantFullName : '')
        );

        $description  = $this->escapeXml($this->productDescription($product));
        $link         = $this->escapeXml($this->buildProductUrl($product));
        $price        = $this->escapeXml($this->formatPrice($variant->sale_price));
        $currency     = $this->escapeXml($this->resolveCurrency());
        $brand        = $this->escapeXml(optional($product->brand)->title ?? '');
        $availability = $this->escapeXml($variant->stock > 0 ? 'in stock' : 'out of stock');
        $condition    = $this->escapeXml('new');
        $categoryTag  = $this->googleCategoryTag($product);

        // Image: First image of variant array or fall back to main product image
        $imagePath = $this->resolveVariantImage($variant, $product);
        $imageLink = $this->escapeXml($this->buildImageUrl($imagePath));

        // Generate <g:color>, <g:size>, etc. based on variant options
        $optionTags = $this->buildOptionTags($variant);

        return <<<XML
        <item>
            <g:id>{$id}</g:id>
            <g:item_group_id>{$itemGroupId}</g:item_group_id>
            <title>{$title}</title>
            <description>{$description}</description>
            <link>{$link}</link>
            <g:image_link>{$imageLink}</g:image_link>
            <g:availability>{$availability}</g:availability>
            <g:condition>{$condition}</g:condition>
            <g:price>{$price} {$currency}</g:price>
            <g:brand>{$brand}</g:brand>
            {$optionTags}
            {$categoryTag}
        </item>
XML;
    }

    /**
     * Map variant options (Color, Size, Material, Pattern, Gender, Age Group) to Meta XML tags.
     */
    protected function buildOptionTags(ProductVariant $variant): string
    {
        // Standard Meta/Google catalog attribute mapping
        $supportedTags = [
            'color'     => 'color',
            'size'      => 'size',
            'material'  => 'material',
            'pattern'   => 'pattern',
            'gender'    => 'gender',
            'age_group' => 'age_group',
        ];

        $tags = [];

        foreach ($variant->variants as $variantOptionValue) {
            $code  = strtolower(trim($variantOptionValue->option?->code ?? ''));
            $value = $variantOptionValue->optionValue?->title;

            if (empty($code) || empty($value)) {
                continue;
            }

            if (isset($supportedTags[$code])) {
                $tagName = $supportedTags[$code];
                $tags[]  = '<g:' . $tagName . '>' . $this->escapeXml($value) . '</g:' . $tagName . '>';
            }
        }

        return implode(PHP_EOL . '            ', $tags);
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    protected function productTitle(Product $product): string
    {
        return $this->resolveTranslation($product, 'title', $product->sku ?? 'Product');
    }

    protected function productDescription(Product $product): string
    {
        return $this->stripTags($this->resolveTranslation($product, 'des', ''));
    }

    protected function resolveTranslation(Product $product, string $attribute, string $fallback = ''): string
    {
        $translation = $product->translate('ar');

        if ($translation && ! empty($translation->{$attribute})) {
            return (string) $translation->{$attribute};
        }

        return $fallback;
    }

    protected function resolveVariantImage(ProductVariant $variant, Product $product): ?string
    {
        $images = $variant->images;

        if (! empty($images) && is_array($images) && isset($images[0])) {
            return $images[0];
        }

        return $product->product_image;
    }

    protected function buildProductUrl(Product $product): string
    {
        $slug = $this->resolveTranslation($product, 'slug', (string) $product->id);

        return rtrim(config('app.url', url('/')), '/') . '/products/' . rawurlencode($slug);
    }

    protected function buildImageUrl(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        $url = Storage::disk('public')->url($path);

        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            return rtrim(config('app.url', url('/')), '/') . $url;
        }

        return $url;
    }

    protected function googleCategoryTag(Product $product): string
    {
        $value = $product->getAttribute('google_product_category');

        if (empty($value)) {
            return '';
        }

        return '<g:google_product_category>' . $this->escapeXml((string) $value) . '</g:google_product_category>';
    }

    protected function formatPrice($price): string
    {
        return number_format((float) $price, 2, '.', '');
    }

    protected function resolveCurrency(): string
    {
        return config('app.currency', 'EGP');
    }

    protected function stripTags(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', strip_tags($value)));
    }

    protected function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
