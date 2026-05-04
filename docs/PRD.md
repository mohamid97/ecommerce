# PRD: Cangrow E-commerce API Platform

## Document Status

Status: Draft  
Owner: Product / Engineering  
Last updated: 2026-05-04  
Project type: Laravel 10 API backend  
Primary users: Store customers, authenticated members, admin operators, content managers  

## 1. Summary

Cangrow E-commerce API Platform is a Laravel-based backend for a multilingual e-commerce and CMS experience. It provides public storefront APIs, authenticated member APIs, admin management APIs, product catalog management, variants, stock batches, carts, orders, bundles, promotions, wishlists, shipping zones, points settings, and reusable CMS modules such as sliders, pages, blogs, media, FAQs, locations, contacts, and team content.

The product should enable the business to manage a full online catalog and operational workflow from content setup through checkout, while giving customers a localized shopping journey with product discovery, cart management, wishlist, account authentication, and order creation.

## 2. Goals

- Provide a stable API foundation for an e-commerce storefront and admin dashboard.
- Support multilingual content in Arabic and English across catalog and CMS entities.
- Allow admins to manage products, categories, brands, options, variants, galleries, stock, bundles, promotions, shipping data, orders, carts, wishlists, and points settings.
- Allow customers to register, verify OTP, log in, update profile, browse products, view product details, manage carts, create wishlists, and place orders.
- Support both simple products and variant-based products with stock validation.
- Support bundle products with selected products or variants.
- Preserve accurate order snapshots, totals, shipping cost, discounts, and stock allocations.

## 3. Non-Goals

- Building the customer-facing frontend UI.
- Building the admin dashboard UI.
- Payment gateway integration, unless added as a future milestone.
- Real-time inventory sync with external ERP systems.
- Advanced recommendation engine or AI-powered search.
- Marketplace seller management.

## 4. Success Metrics

- API success rate for customer and admin endpoints is 99% or higher.
- Authenticated customers can complete registration, cart, and order flows without manual support.
- Admins can create and maintain products with variants, stock, galleries, and translations from API consumers.
- Cart and order total calculations match expected pricing, discount, bundle, shipping, and points rules.
- Stock validation prevents overselling for simple products, variants, and bundle components.
- Content APIs return localized data based on the request language middleware.

## 5. User Personas

### Customer

Wants to browse products, search/filter, view details, add items or bundles to cart, save wishlist items, and place an order.

### Member

An authenticated customer who can manage profile data, maintain cart state, use wishlist, and create orders.

### Admin Operator

Manages daily e-commerce operations such as products, variants, stock batches, bundles, promotions, carts, orders, points tiers, and shipment settings.

### Content Manager

Maintains CMS content such as sliders, pages, blogs, events, media, FAQs, team, branches, contact data, and SEO/meta settings.

## 6. Core Features

### 6.1 Authentication and Authorization

Customer auth:

- Send verification OTP.
- Verify OTP.
- Register member.
- Log in member.
- Get authenticated member data.
- Update member profile.

Admin auth:

- Admin login.
- Get authenticated admin data.
- Logout.
- Protect admin APIs with Laravel Sanctum.
- Enforce module permissions using Spatie permissions and custom permission middleware.

Acceptance criteria:

- Invalid credentials return a standardized error response.
- Authenticated APIs reject unauthenticated requests.
- Admin CRUD actions require correct create, update, delete, or view permissions.
- Customer auth and admin auth remain separated.

### 6.2 Multilingual Content

The platform supports translated fields through `astrotomic/laravel-translatable` and language middleware.

Supported content:

- Product names/descriptions.
- Categories and brands.
- Options and option values.
- Pages, blogs, events, media, FAQs, sliders, services, branches, locations, contacts, and meta settings.
- Shipment zones and cities.
- Bundles and promotions.

Acceptance criteria:

- API responses return localized fields based on selected language.
- Admin APIs can store and update Arabic and English translation values.
- Missing translations should fail gracefully or return fallback behavior according to the model configuration.

### 6.3 Product Catalog

Admins can manage products and associated catalog data.

Capabilities:

- Create, update, delete, view, and list products through generic CRUD and product-specific endpoints.
- Assign products to categories and brands.
- Manage product galleries.
- Manage product specifications.
- Mark products as featured.
- Add or remove products from newest products.
- Add or remove products from last-piece products.
- Store related products.
- Filter products in admin APIs.
- Expose public product lists, newest products, last-piece products, product details, and variant details.

Acceptance criteria:

- Products can be created with multilingual content.
- Product status can be updated independently for product or variant availability.
- Related products can be stored and retrieved.
- Public product details include pricing, discount price, gallery, options/variants when applicable, and localized data.

### 6.4 Product Options and Variants

Admins can define product options and option values, then create product variants from combinations.

Capabilities:

- Create product options and option values.
- Attach valid option values to products.
- Generate variant combinations.
- Store, update, view, list, delete, and make default variants.
- Store general variant gallery images.
- Store special variant gallery images.
- Filter products by variants.

Acceptance criteria:

- Variant creation validates that selected option values belong to the selected option.
- A default variant can be assigned for variant-based products.
- Variant galleries can be added and removed.
- Variant-specific price, sale price, stock, and image data are returned where available.

### 6.5 Stock and Inventory

The platform supports stock management for products and variants through stock batches.

Capabilities:

- Add stock batches.
- Update stock batches.
- View batch details.
- List batches.
- Update batch status.
- Delete stock batches.
- Validate stock before adding to cart, updating cart quantity, and creating orders.
- Track stock movements during order creation.

Acceptance criteria:

- Cart and order flows fail when requested quantity exceeds available stock.
- Bundle stock validation checks each underlying product or variant.
- Order creation allocates stock using batch data and stores order item batch links.

### 6.6 Cart

Customers can manage authenticated carts, and guests can preview cart data.

Capabilities:

- Add simple products, variant products, or bundles to cart.
- Update cart item quantity.
- Delete one item from cart.
- Delete all items from cart.
- View authenticated cart.
- View guest cart from posted product and bundle data.
- Admin can list carts, view a cart, delete items, clear a cart, or clear by user.

Acceptance criteria:

- Cart strategy resolver chooses the correct behavior for simple products, variant products, and bundles.
- Cart totals include before-discount and after-discount values.
- Guest cart view validates posted items without creating a persisted cart.
- Authenticated cart is deleted or closed after successful order creation.

### 6.7 Orders

Authenticated customers can create orders from their cart.

Capabilities:

- Create order from open user cart.
- Capture shipping and customer order fields from request data.
- Calculate total before discount.
- Calculate total after discount.
- Add shipping cost.
- Apply points amount when points usage is enabled.
- Persist order items and stock batch allocations.
- Admin can list and view orders.

Acceptance criteria:

- Order creation runs inside a database transaction.
- Any stock or validation failure rolls back the order.
- Order response includes items, products, variants, bundles, bundle details, and stock movements.
- Admin order list and view endpoints require order view permission.

### 6.8 Bundles

Admins can create product bundles that customers can browse and add to cart.

Capabilities:

- Store bundle with translated fields.
- Update bundle.
- Delete bundle.
- List bundles with category and brand.
- View bundle details.
- Publicly list bundles.
- Publicly view bundle details.
- Add bundle to cart with selected underlying products or variants.

Acceptance criteria:

- Bundle price calculation accounts for bundle item selections.
- Bundle cart quantity multiplies underlying product quantities for stock checks.
- Bundle details include required products, variants, category, brand, and localized content.

### 6.9 Promotions

Admins can manage promotions and connect them to products, categories, and brands.

Capabilities:

- Store promotion.
- Update promotion.
- Delete promotion.
- View promotion details.
- List promotions.
- Store translated promotion content.
- Associate promotions with categories and brands.

Acceptance criteria:

- Promotion lifecycle supports create, update, delete, detail, and list APIs.
- Promotion data supports multilingual display.
- Promotion associations are persisted consistently.

### 6.10 Wishlist

Authenticated members can manage wishlists.

Capabilities:

- Add wishlist item.
- Toggle wishlist item.
- Remove wishlist item.
- View wishlist.
- Admin can view wishlist records.

Acceptance criteria:

- Wishlist actions require authentication.
- Toggle adds item when missing and removes item when present.
- Wishlist view returns product data needed by storefront consumers.

### 6.11 Points

Admins can manage points tiers/settings, and order creation reserves fields for points usage.

Capabilities:

- Get points tiers.
- Store points tier.
- Delete points tier.
- Customers can submit `use_points` and `points_to_use` during order creation.

Acceptance criteria:

- Points settings are permission-protected in admin APIs.
- Order totals account for `points_amount` when implemented.
- Open issue: points conversion logic currently appears stubbed and should be completed before production use.

### 6.12 Shipping

Admins can manage shipment zones and cities.

Capabilities:

- Create and update shipment zones.
- Create and update shipment cities.
- Store translated city and zone names.
- Store product shipment dimensions and estimated delivery fields.
- Apply shipping cost during order total calculation.

Acceptance criteria:

- Shipping zone and city APIs enforce admin permissions.
- Product shipment data can include length, width, height, weight, and delivery range.
- Order total includes shipping cost.

### 6.13 CMS and General Content

The platform includes generic admin CRUD support and frontend dynamic data endpoints.

CMS modules include:

- Sliders.
- Categories.
- Brands.
- Services.
- Blogs.
- Events.
- Our works.
- Feedback.
- Achievements.
- Clients.
- Pages.
- Branches.
- Locations.
- Contact us.
- Basic contacts.
- Social links.
- Media images and videos.
- FAQs.
- Certificates.
- Team.
- Meta settings.
- Applicants.

Acceptance criteria:

- Generic CRUD endpoint can store, update, delete, list, and view supported modules.
- Gallery endpoints support module gallery upload/view where applicable.
- Frontend dynamic endpoint supports content retrieval, filtering, galleries, and search.

## 7. API Surface

### Public and Member APIs

Base prefix: `/api/front/v1` or configured API route prefix.

Key groups:

- `auth/send-verification`
- `auth/verfiy-otp`
- `auth/register`
- `auth/login`
- `auth/user`
- `auth/update-user`
- `products/get`
- `products/last-piece`
- `products/newest`
- `products/details`
- `products/varaint-details`
- `bundles/get`
- `bundles/details`
- `carts/guest/view`
- `carts/add`
- `carts/update-quantity`
- `carts/delete-all`
- `carts/delete-item`
- `carts/view`
- `wishlists/add`
- `wishlists/toggle`
- `wishlists/delete-item`
- `wishlists/view`
- `orders/store`
- `send-message`
- `data/get`
- `data/dynamic/filter`
- `data/gallery`
- `data/search`
- `applicant/store`

### Admin APIs

Base prefix: `/api/admin/v1` or configured API route prefix.

Key groups:

- `login`
- `get-user`
- `logout`
- Generic CRUD: `store`, `update`, `delete`, `all`, `view`
- Gallery: `gallery/store`, `gallery/all`
- Specifications: `specification/store`, `specification/all`
- Category brands: `category/brands`
- Home summary: `home`
- Product operations, variants, stock, galleries, related products, filters
- Bundle operations
- Promotion operations
- Cart operations
- Wishlist operations
- Points operations
- Order operations

## 8. Data Model Overview

Core entities:

- User/member.
- OTP.
- Product.
- Product translation.
- Product gallery.
- Category and category translation.
- Brand and brand translation.
- Option and option translation.
- Option value and option value translation.
- Product option and product option value.
- Product variant and product variant translation.
- Variant option values.
- Variant galleries.
- General variant galleries.
- No-option stock.
- Stock movement.
- Cart.
- Cart item.
- Cart bundle item.
- Order.
- Order item.
- Order item batch.
- Bundle, bundle translation, bundle details.
- Wishlist.
- Promotion and promotion translation.
- Promotion category and promotion brand.
- Shipment zone/city and translations.
- Points settings.
- CMS entities and translations.

## 9. Permissions

Admin permission system should support:

- Generic module permissions: create, update, delete, view.
- E-commerce module permissions: cart, order, points, and related actions.
- Role-based access through Spatie permission tables.

Acceptance criteria:

- Users without permission cannot access restricted endpoints.
- Role seeding should create required permissions for all active modules.
- Permission middleware names and route assignments should be audited for typos before production.

## 10. Functional Requirements

### Customer Requirements

- As a customer, I can browse localized product and bundle lists.
- As a customer, I can view product and variant details.
- As a guest, I can preview cart totals by submitting cart data.
- As a member, I can register, log in, update profile, manage cart, manage wishlist, and create orders.
- As a member, I can submit an order using my current cart.

### Admin Requirements

- As an admin, I can manage catalog content and translations.
- As an admin, I can manage variants, images, stock batches, and status.
- As an admin, I can create bundles and promotions.
- As an admin, I can inspect carts and orders.
- As an admin, I can manage shipping zones/cities and points settings.
- As a content manager, I can manage CMS modules and galleries.

## 11. Non-Functional Requirements

- Security: All admin and member-only routes must use Sanctum authentication.
- Authorization: Admin routes must enforce role and permission middleware.
- Localization: Responses must honor request language.
- Reliability: Order creation must be transactional.
- Performance: Product list, filter, cart, and order endpoints should eager-load required relationships to avoid N+1 queries.
- Maintainability: Continue using DTOs, service classes, repositories, resources, and strategy resolvers for complex cart/order/product behavior.
- Observability: Laravel Telescope can support debugging in non-production environments.
- Validation: Request classes should validate payloads for all customer and admin write operations.

## 12. Risks and Open Issues

- Points conversion is partially stubbed during order creation and needs finalized business rules.
- Payment gateway flow is absent and should be scoped before production checkout.
- Some spelling inconsistencies exist in code and route names, such as `bundel`, `varaint`, `verfiy`, and `ckeckLang`. These may be kept for compatibility but should be documented for API consumers.
- README is still the default Laravel README and should be replaced with project-specific setup and API documentation.
- Automated tests are currently minimal and should be expanded around cart, order, stock, permissions, and translations.
- Migrations include future-dated filenames; deployment order should be validated carefully.

## 13. Milestones

### Milestone 1: Product Documentation and Stabilization

- Replace default README with setup instructions.
- Document API base URLs, auth headers, language headers, and common response format.
- Audit route names and permission middleware.
- Add Postman collection coverage for all e-commerce flows.

### Milestone 2: Checkout Readiness

- Finalize points conversion rules.
- Decide payment gateway requirements.
- Confirm shipping cost source and calculation rules.
- Add tests for cart to order conversion and stock allocation.

### Milestone 3: Admin Dashboard Readiness

- Confirm all admin modules required by dashboard.
- Ensure role seeder includes all module permissions.
- Add list/detail resources needed by dashboard tables and forms.
- Add pagination/filtering where large lists are expected.

### Milestone 4: Storefront Readiness

- Confirm product list filters, search fields, sorting, and pagination.
- Validate localized public responses.
- Confirm wishlist, cart, and order responses match frontend UI needs.
- Add guest-to-authenticated cart merge rules if required.

## 14. Launch Checklist

- Environment variables documented.
- Database migrations run cleanly from empty database.
- Seeders complete roles, permissions, settings, languages, and sample e-commerce data.
- Sanctum auth verified for admin and member flows.
- Language middleware verified for Arabic and English.
- Product creation with variants tested.
- Product creation without variants tested.
- Bundle creation and cart flow tested.
- Cart update/delete flow tested.
- Order creation and stock allocation tested.
- Admin order view tested.
- Promotions tested.
- Wishlist tested.
- Points settings tested.
- Error responses standardized.
- API docs or Postman collection updated.

## 15. Future Enhancements

- Payment gateway integration.
- Coupon and promotion stacking rules.
- Order status workflow.
- Shipment tracking integration.
- Inventory low-stock alerts.
- Admin analytics dashboard.
- Product reviews and ratings.
- Guest cart persistence and merge on login.
- Advanced product search with faceting.
- Audit logs for admin changes.

