<?php

return [
    // 8 hours for admin panel tokens
    'admin_minutes' => env('SANCTUM_ADMIN_TOKEN_EXPIRATION', 480),

    // 30 days for customer mobile/web bearer tokens
    'customer_minutes' => env('SANCTUM_CUSTOMER_TOKEN_EXPIRATION', 43200),
];
