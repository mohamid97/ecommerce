# Industries Endpoints

## Dashboard

Dashboard industries use the existing generic admin CRUD endpoints.

### Create Industry

`POST /api/admin/v1/store`

Required auth: admin Sanctum token  
Required body field: `model=industry`

Form-data example:

```http
model: industry
industry_image: file
order: 1
title[en]: Medical
title[ar]: طبي
small_des[en]: Short description
small_des[ar]: وصف قصير
des[en]: Full description
des[ar]: الوصف الكامل
meta_title[en]: Medical products
meta_title[ar]: منتجات طبية
meta_des[en]: Medical industry products
meta_des[ar]: منتجات الصناعة الطبية
```

### Update Industry

`POST /api/admin/v1/update`

Required body fields: `model=industry`, `id`

### Delete Industry

`POST /api/admin/v1/delete`

Required body fields: `model=industry`, `id`

### List Industries

`POST /api/admin/v1/all`

Body:

```json
{
  "model": "industry",
  "paginate": 10,
  "search": "medical"
}
```

### View Industry

`POST /api/admin/v1/view`

Body:

```json
{
  "model": "industry",
  "id": 1
}
```

### Create Product With Industries

`POST /api/admin/v1/store`

Required body field: `model=product`

Add industries as an array:

```http
industries[0]: 1
industries[1]: 2
```

### Update Product Industries

`POST /api/admin/v1/update`

Required body fields: `model=product`, `id`

Send the full industry list to sync:

```http
industries[0]: 1
industries[1]: 3
```

Send no `industries` or an empty array to remove all industries from the product.

## Front

### Get All Industries

`GET /api/front/v1/industries/get`

Also available as:

`GET /api/front/v1/products/industries`

Optional query params:

```http
paginate=10
search=medical
sort=desc
```

### Get Products For One Industry

`GET /api/front/v1/industries/products?industry_id=1`

Also available as:

`GET /api/front/v1/products/industry-products?industry_id=1`

Optional query params:

```http
paginate=10
sort=desc
```

### Filter Product List By Industry

`GET /api/front/v1/products/get?industry_id=1`

