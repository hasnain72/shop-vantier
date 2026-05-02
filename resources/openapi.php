<?php

/**
 * OpenAPI 3.0 specification for the Vantier Storefront API v1.
 * Served by GET /api/v1/docs/spec
 */
return [
    'openapi' => '3.0.3',
    'info' => [
        'title'       => 'Vantier Storefront API',
        'description' => "REST API for the Vantier e-commerce storefront.\n\n**Authentication:** Protected endpoints require `Authorization: Bearer <token>` obtained via `/auth/login` or `/auth/register`.\n\nAll responses include `success: true/false`. Errors return `{ success: false, message: '...', errors: {} }` with an appropriate HTTP status code.",
        'version'     => '1.0.0',
        'contact'     => ['email' => 'admin@example.com'],
    ],
    'servers' => [
        ['url' => url('/api/v1'), 'description' => 'Current server'],
    ],
    'tags' => [
        ['name' => 'General',     'description' => 'Health check and store info'],
        ['name' => 'Auth',        'description' => 'Customer authentication & account management'],
        ['name' => 'Customer',    'description' => 'Authenticated customer profile & addresses'],
        ['name' => 'Products',    'description' => 'Product catalog'],
        ['name' => 'Collections', 'description' => 'Product collections / categories'],
        ['name' => 'Cart',        'description' => 'Shopping cart (guest & authenticated)'],
        ['name' => 'Checkout',    'description' => 'Checkout validation & totals'],
        ['name' => 'Orders',      'description' => 'Order creation & management'],
        ['name' => 'Discounts',   'description' => 'Discount / coupon code validation'],
        ['name' => 'Shipping',    'description' => 'Shipping rate calculation'],
        ['name' => 'Search',      'description' => 'Full-text search across catalog & CMS'],
        ['name' => 'CMS',         'description' => 'Pages, blogs & articles'],
        ['name' => 'Inventory',   'description' => 'Staff-only inventory management'],
    ],
    'components' => [
        'securitySchemes' => [
            'BearerAuth' => [
                'type'         => 'http',
                'scheme'       => 'bearer',
                'bearerFormat' => 'Sanctum',
                'description'  => 'Token returned by /auth/login or /auth/register',
            ],
        ],
        'schemas' => [
            'SuccessResponse' => [
                'type' => 'object',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => true],
                    'message' => ['type' => 'string'],
                    'data'    => ['type' => 'object'],
                ],
            ],
            'ErrorResponse' => [
                'type' => 'object',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => false],
                    'message' => ['type' => 'string', 'example' => 'Validation failed'],
                    'errors'  => ['type' => 'object', 'additionalProperties' => ['type' => 'array', 'items' => ['type' => 'string']]],
                ],
            ],
            'Pagination' => [
                'type' => 'object',
                'properties' => [
                    'total'        => ['type' => 'integer', 'example' => 42],
                    'per_page'     => ['type' => 'integer', 'example' => 20],
                    'current_page' => ['type' => 'integer', 'example' => 1],
                    'last_page'    => ['type' => 'integer', 'example' => 3],
                ],
            ],
            'Address' => [
                'type' => 'object',
                'required' => ['first_name', 'last_name', 'address1', 'city', 'country', 'country_code'],
                'properties' => [
                    'first_name'    => ['type' => 'string', 'example' => 'Jane'],
                    'last_name'     => ['type' => 'string', 'example' => 'Doe'],
                    'company'       => ['type' => 'string', 'nullable' => true],
                    'address1'      => ['type' => 'string', 'example' => '123 Main St'],
                    'address2'      => ['type' => 'string', 'nullable' => true],
                    'city'          => ['type' => 'string', 'example' => 'New York'],
                    'province'      => ['type' => 'string', 'nullable' => true],
                    'province_code' => ['type' => 'string', 'nullable' => true, 'example' => 'NY'],
                    'country'       => ['type' => 'string', 'example' => 'United States'],
                    'country_code'  => ['type' => 'string', 'example' => 'US', 'minLength' => 2, 'maxLength' => 2],
                    'zip'           => ['type' => 'string', 'nullable' => true, 'example' => '10001'],
                    'phone'         => ['type' => 'string', 'nullable' => true],
                ],
            ],
            'CustomerAddress' => [
                'allOf' => [
                    ['$ref' => '#/components/schemas/Address'],
                    [
                        'type' => 'object',
                        'properties' => [
                            'id'         => ['type' => 'integer', 'example' => 5],
                            'is_default' => ['type' => 'boolean'],
                            'created_at' => ['type' => 'string', 'format' => 'date-time'],
                        ],
                    ],
                ],
            ],
            'Customer' => [
                'type' => 'object',
                'properties' => [
                    'id'                 => ['type' => 'integer', 'example' => 1],
                    'first_name'         => ['type' => 'string', 'example' => 'Jane'],
                    'last_name'          => ['type' => 'string', 'example' => 'Doe'],
                    'email'              => ['type' => 'string', 'format' => 'email'],
                    'phone'              => ['type' => 'string', 'nullable' => true],
                    'state'              => ['type' => 'string', 'enum' => ['enabled', 'disabled']],
                    'accepts_marketing'  => ['type' => 'boolean'],
                    'addresses'          => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/CustomerAddress']],
                    'created_at'         => ['type' => 'string', 'format' => 'date-time'],
                ],
            ],
            'ProductVariant' => [
                'type' => 'object',
                'properties' => [
                    'id'                 => ['type' => 'integer'],
                    'product_id'         => ['type' => 'integer'],
                    'title'              => ['type' => 'string', 'example' => 'Black / 42mm'],
                    'sku'                => ['type' => 'string', 'nullable' => true],
                    'price'              => ['type' => 'string', 'example' => '299.99'],
                    'compare_at_price'   => ['type' => 'string', 'nullable' => true],
                    'weight'             => ['type' => 'number'],
                    'weight_unit'        => ['type' => 'string', 'example' => 'kg'],
                    'inventory_quantity' => ['type' => 'integer'],
                    'inventory_policy'   => ['type' => 'string', 'enum' => ['deny', 'continue']],
                    'option1'            => ['type' => 'string', 'nullable' => true],
                    'option2'            => ['type' => 'string', 'nullable' => true],
                    'option3'            => ['type' => 'string', 'nullable' => true],
                    'position'           => ['type' => 'integer'],
                ],
            ],
            'ProductImage' => [
                'type' => 'object',
                'properties' => [
                    'id'         => ['type' => 'integer'],
                    'src'        => ['type' => 'string', 'format' => 'uri'],
                    'alt'        => ['type' => 'string', 'nullable' => true],
                    'position'   => ['type' => 'integer'],
                    'variant_ids'=> ['type' => 'array', 'items' => ['type' => 'integer']],
                ],
            ],
            'Product' => [
                'type' => 'object',
                'properties' => [
                    'id'                 => ['type' => 'integer', 'example' => 1],
                    'title'              => ['type' => 'string', 'example' => 'Chronograph Watch'],
                    'slug'               => ['type' => 'string', 'example' => 'chronograph-watch'],
                    'description'        => ['type' => 'string', 'nullable' => true],
                    'vendor'             => ['type' => 'string', 'nullable' => true, 'example' => 'Vantier'],
                    'product_type'       => ['type' => 'string', 'nullable' => true, 'example' => 'Watch'],
                    'status'             => ['type' => 'string', 'enum' => ['active', 'draft', 'archived']],
                    'tags'               => ['type' => 'string', 'nullable' => true],
                    'featured_image'     => ['type' => 'string', 'format' => 'uri', 'nullable' => true],
                    'price'              => ['type' => 'string', 'description' => 'Lowest variant price', 'example' => '299.99'],
                    'compare_at_price'   => ['type' => 'string', 'nullable' => true],
                    'variants'           => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/ProductVariant']],
                    'images'             => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/ProductImage']],
                    'options'            => ['type' => 'array', 'items' => ['type' => 'object']],
                    'created_at'         => ['type' => 'string', 'format' => 'date-time'],
                    'updated_at'         => ['type' => 'string', 'format' => 'date-time'],
                ],
            ],
            'Collection' => [
                'type' => 'object',
                'properties' => [
                    'id'             => ['type' => 'integer', 'example' => 1],
                    'title'          => ['type' => 'string', 'example' => 'Best Sellers'],
                    'slug'           => ['type' => 'string', 'example' => 'best-sellers'],
                    'description'    => ['type' => 'string', 'nullable' => true],
                    'published'      => ['type' => 'boolean'],
                    'sort_order'     => ['type' => 'string'],
                    'products_count' => ['type' => 'integer'],
                    'image'          => ['type' => 'string', 'format' => 'uri', 'nullable' => true],
                    'created_at'     => ['type' => 'string', 'format' => 'date-time'],
                ],
            ],
            'CartItem' => [
                'type' => 'object',
                'properties' => [
                    'id'             => ['type' => 'integer'],
                    'variant_id'     => ['type' => 'integer'],
                    'product_id'     => ['type' => 'integer'],
                    'title'          => ['type' => 'string', 'example' => 'Chronograph Watch'],
                    'variant_title'  => ['type' => 'string', 'nullable' => true, 'example' => 'Black / 42mm'],
                    'sku'            => ['type' => 'string', 'nullable' => true],
                    'image'          => ['type' => 'string', 'format' => 'uri', 'nullable' => true],
                    'price'          => ['type' => 'number', 'example' => 299.99],
                    'original_price' => ['type' => 'number'],
                    'quantity'       => ['type' => 'integer', 'example' => 1],
                    'total_price'    => ['type' => 'number', 'example' => 299.99],
                    'properties'     => ['type' => 'object'],
                ],
            ],
            'Cart' => [
                'type' => 'object',
                'properties' => [
                    'token'              => ['type' => 'string', 'format' => 'uuid'],
                    'note'               => ['type' => 'string', 'nullable' => true],
                    'attributes'         => ['type' => 'object'],
                    'currency'           => ['type' => 'string', 'example' => 'USD'],
                    'item_count'         => ['type' => 'integer'],
                    'total_price'        => ['type' => 'number'],
                    'total_discount'     => ['type' => 'number'],
                    'requires_shipping'  => ['type' => 'boolean'],
                    'items'              => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/CartItem']],
                ],
            ],
            'LineItemInput' => [
                'type' => 'object',
                'required' => ['variant_id', 'quantity'],
                'properties' => [
                    'variant_id'  => ['type' => 'integer', 'example' => 3],
                    'quantity'    => ['type' => 'integer', 'minimum' => 1, 'example' => 1],
                    'properties'  => ['type' => 'object', 'nullable' => true],
                ],
            ],
            'OrderLineItem' => [
                'type' => 'object',
                'properties' => [
                    'id'          => ['type' => 'integer'],
                    'variant_id'  => ['type' => 'integer'],
                    'product_id'  => ['type' => 'integer'],
                    'title'       => ['type' => 'string'],
                    'variant_title' => ['type' => 'string', 'nullable' => true],
                    'sku'         => ['type' => 'string', 'nullable' => true],
                    'quantity'    => ['type' => 'integer'],
                    'price'       => ['type' => 'string'],
                    'total_price' => ['type' => 'string'],
                    'properties'  => ['type' => 'object'],
                ],
            ],
            'Order' => [
                'type' => 'object',
                'properties' => [
                    'id'                 => ['type' => 'integer', 'example' => 1001],
                    'order_number'       => ['type' => 'string', 'example' => '1001'],
                    'name'               => ['type' => 'string', 'example' => '#1001'],
                    'email'              => ['type' => 'string', 'format' => 'email', 'nullable' => true],
                    'phone'              => ['type' => 'string', 'nullable' => true],
                    'financial_status'   => ['type' => 'string', 'enum' => ['pending', 'paid', 'refunded', 'voided', 'partially_refunded']],
                    'fulfillment_status' => ['type' => 'string', 'enum' => ['unfulfilled', 'fulfilled', 'partial'], 'nullable' => true],
                    'subtotal_price'     => ['type' => 'string', 'example' => '299.99'],
                    'total_discounts'    => ['type' => 'string', 'example' => '0.00'],
                    'total_shipping'     => ['type' => 'string', 'example' => '5.99'],
                    'total_tax'          => ['type' => 'string', 'example' => '30.00'],
                    'total_price'        => ['type' => 'string', 'example' => '335.98'],
                    'currency'           => ['type' => 'string', 'example' => 'USD'],
                    'note'               => ['type' => 'string', 'nullable' => true],
                    'line_items'         => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/OrderLineItem']],
                    'shipping_address'   => ['$ref' => '#/components/schemas/Address'],
                    'billing_address'    => ['$ref' => '#/components/schemas/Address'],
                    'created_at'         => ['type' => 'string', 'format' => 'date-time'],
                ],
            ],
            'ShippingRate' => [
                'type' => 'object',
                'properties' => [
                    'name'              => ['type' => 'string', 'example' => 'Standard Shipping'],
                    'price'             => ['type' => 'number', 'example' => 5.99],
                    'min_delivery_days' => ['type' => 'integer', 'nullable' => true],
                    'max_delivery_days' => ['type' => 'integer', 'nullable' => true],
                ],
            ],
            'Page' => [
                'type' => 'object',
                'properties' => [
                    'id'           => ['type' => 'integer'],
                    'title'        => ['type' => 'string'],
                    'slug'         => ['type' => 'string'],
                    'body_html'    => ['type' => 'string', 'nullable' => true],
                    'published_at' => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                    'created_at'   => ['type' => 'string', 'format' => 'date-time'],
                ],
            ],
            'Blog' => [
                'type' => 'object',
                'properties' => [
                    'id'         => ['type' => 'integer'],
                    'title'      => ['type' => 'string'],
                    'slug'       => ['type' => 'string'],
                    'created_at' => ['type' => 'string', 'format' => 'date-time'],
                ],
            ],
            'Article' => [
                'type' => 'object',
                'properties' => [
                    'id'           => ['type' => 'integer'],
                    'blog_id'      => ['type' => 'integer'],
                    'title'        => ['type' => 'string'],
                    'slug'         => ['type' => 'string'],
                    'body_html'    => ['type' => 'string', 'nullable' => true],
                    'summary_html' => ['type' => 'string', 'nullable' => true],
                    'author'       => ['type' => 'string', 'nullable' => true],
                    'image'        => ['type' => 'string', 'format' => 'uri', 'nullable' => true],
                    'tags'         => ['type' => 'string', 'nullable' => true],
                    'published_at' => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                    'created_at'   => ['type' => 'string', 'format' => 'date-time'],
                ],
            ],
        ],
        'responses' => [
            'Unauthorized' => [
                'description' => 'Authentication required',
                'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ErrorResponse']]],
            ],
            'NotFound' => [
                'description' => 'Resource not found',
                'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ErrorResponse']]],
            ],
            'UnprocessableEntity' => [
                'description' => 'Validation error',
                'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ErrorResponse']]],
            ],
        ],
        'parameters' => [
            'LimitParam' => [
                'name' => 'limit', 'in' => 'query', 'required' => false,
                'schema' => ['type' => 'integer', 'default' => 20, 'maximum' => 250],
                'description' => 'Number of results per page',
            ],
            'PageParam' => [
                'name' => 'page', 'in' => 'query', 'required' => false,
                'schema' => ['type' => 'integer', 'default' => 1],
                'description' => 'Page number',
            ],
        ],
    ],
    'paths' => [

        // ── Health ──────────────────────────────────────────────
        '/health' => [
            'get' => [
                'tags'        => ['General'],
                'summary'     => 'Health check',
                'operationId' => 'health',
                'responses'   => [
                    '200' => [
                        'description' => 'API is healthy',
                        'content' => ['application/json' => [
                            'example' => ['success' => true, 'message' => 'OK'],
                        ]],
                    ],
                ],
            ],
        ],

        // ── Shop ──────────────────────────────────────────────
        '/shop' => [
            'get' => [
                'tags'        => ['General'],
                'summary'     => 'Store information',
                'operationId' => 'shop.show',
                'description' => 'Returns store settings: name, email, currency, timezone, weight unit, logo, favicon, and SEO meta fields.',
                'responses'   => [
                    '200' => [
                        'description' => 'Store settings',
                        'content' => ['application/json' => [
                            'example' => [
                                'success' => true,
                                'data' => ['shop' => [
                                    'name' => 'Vantier', 'email' => 'store@example.com',
                                    'currency' => 'USD', 'currency_symbol' => '$',
                                    'timezone' => 'UTC', 'weight_unit' => 'kg',
                                    'logo' => null, 'favicon' => null,
                                    'meta_title' => 'Vantier', 'meta_description' => null,
                                ]],
                            ],
                        ]],
                    ],
                ],
            ],
        ],

        // ── Auth ──────────────────────────────────────────────
        '/auth/register' => [
            'post' => [
                'tags'        => ['Auth'],
                'summary'     => 'Register a new customer',
                'operationId' => 'auth.register',
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'required' => ['first_name', 'last_name', 'email', 'password', 'password_confirmation'],
                            'properties' => [
                                'first_name'            => ['type' => 'string', 'example' => 'Jane'],
                                'last_name'             => ['type' => 'string', 'example' => 'Doe'],
                                'email'                 => ['type' => 'string', 'format' => 'email', 'example' => 'jane@example.com'],
                                'password'              => ['type' => 'string', 'format' => 'password', 'minLength' => 8, 'example' => 'secret123'],
                                'password_confirmation' => ['type' => 'string', 'format' => 'password', 'example' => 'secret123'],
                                'phone'                 => ['type' => 'string', 'nullable' => true],
                            ],
                        ],
                    ]],
                ],
                'responses' => [
                    '201' => [
                        'description' => 'Registered successfully',
                        'content' => ['application/json' => [
                            'example' => [
                                'success' => true, 'message' => 'Registered',
                                'data' => ['customer' => ['id' => 1, 'email' => 'jane@example.com'], 'token' => '1|aBcD...'],
                            ],
                        ]],
                    ],
                    '422' => ['$ref' => '#/components/responses/UnprocessableEntity'],
                ],
            ],
        ],

        '/auth/login' => [
            'post' => [
                'tags'        => ['Auth'],
                'summary'     => 'Login',
                'operationId' => 'auth.login',
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'required' => ['email', 'password'],
                            'properties' => [
                                'email'    => ['type' => 'string', 'format' => 'email', 'example' => 'admin@example.com'],
                                'password' => ['type' => 'string', 'format' => 'password', 'example' => 'password'],
                            ],
                        ],
                    ]],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Login successful',
                        'content' => ['application/json' => [
                            'example' => [
                                'success' => true, 'message' => 'Logged in',
                                'data' => ['customer' => ['id' => 1, 'email' => 'admin@example.com'], 'token' => '2|xYzA...'],
                            ],
                        ]],
                    ],
                    '422' => ['$ref' => '#/components/responses/UnprocessableEntity'],
                ],
            ],
        ],

        '/auth/logout' => [
            'post' => [
                'tags'        => ['Auth'],
                'summary'     => 'Logout',
                'operationId' => 'auth.logout',
                'security'    => [['BearerAuth' => []]],
                'responses'   => [
                    '200' => ['description' => 'Logged out', 'content' => ['application/json' => ['example' => ['success' => true, 'message' => 'Logged out', 'data' => null]]]],
                    '401' => ['$ref' => '#/components/responses/Unauthorized'],
                ],
            ],
        ],

        '/auth/me' => [
            'get' => [
                'tags'        => ['Auth'],
                'summary'     => 'Get current customer',
                'operationId' => 'auth.me',
                'security'    => [['BearerAuth' => []]],
                'responses'   => [
                    '200' => [
                        'description' => 'Authenticated customer',
                        'content'     => ['application/json' => ['schema' => [
                            'type' => 'object',
                            'properties' => [
                                'success' => ['type' => 'boolean'],
                                'data' => ['type' => 'object', 'properties' => ['customer' => ['$ref' => '#/components/schemas/Customer']]],
                            ],
                        ]]],
                    ],
                    '401' => ['$ref' => '#/components/responses/Unauthorized'],
                ],
            ],
        ],

        '/auth/profile' => [
            'put' => [
                'tags'        => ['Auth'],
                'summary'     => 'Update profile',
                'operationId' => 'auth.profile.update',
                'security'    => [['BearerAuth' => []]],
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'first_name' => ['type' => 'string'],
                                'last_name'  => ['type' => 'string'],
                                'phone'      => ['type' => 'string', 'nullable' => true],
                            ],
                        ],
                    ]],
                ],
                'responses' => [
                    '200' => ['description' => 'Profile updated'],
                    '401' => ['$ref' => '#/components/responses/Unauthorized'],
                    '422' => ['$ref' => '#/components/responses/UnprocessableEntity'],
                ],
            ],
        ],

        '/auth/change-password' => [
            'put' => [
                'tags'        => ['Auth'],
                'summary'     => 'Change password',
                'operationId' => 'auth.change-password',
                'security'    => [['BearerAuth' => []]],
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'required' => ['current_password', 'new_password', 'new_password_confirmation'],
                            'properties' => [
                                'current_password'          => ['type' => 'string', 'format' => 'password'],
                                'new_password'              => ['type' => 'string', 'format' => 'password', 'minLength' => 8],
                                'new_password_confirmation' => ['type' => 'string', 'format' => 'password'],
                            ],
                        ],
                    ]],
                ],
                'responses' => [
                    '200' => ['description' => 'Password updated'],
                    '401' => ['$ref' => '#/components/responses/Unauthorized'],
                    '422' => ['$ref' => '#/components/responses/UnprocessableEntity'],
                ],
            ],
        ],

        '/auth/forgot-password' => [
            'post' => [
                'tags'        => ['Auth'],
                'summary'     => 'Forgot password — send reset link',
                'operationId' => 'auth.forgot-password',
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => [
                        'schema' => ['type' => 'object', 'required' => ['email'], 'properties' => ['email' => ['type' => 'string', 'format' => 'email']]],
                    ]],
                ],
                'responses' => [
                    '200' => ['description' => 'Reset link sent'],
                    '422' => ['$ref' => '#/components/responses/UnprocessableEntity'],
                ],
            ],
        ],

        '/auth/reset-password' => [
            'post' => [
                'tags'        => ['Auth'],
                'summary'     => 'Reset password',
                'operationId' => 'auth.reset-password',
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'required' => ['email', 'token', 'password', 'password_confirmation'],
                            'properties' => [
                                'email'                 => ['type' => 'string', 'format' => 'email'],
                                'token'                 => ['type' => 'string', 'example' => 'abc123...'],
                                'password'              => ['type' => 'string', 'format' => 'password', 'minLength' => 8],
                                'password_confirmation' => ['type' => 'string', 'format' => 'password'],
                            ],
                        ],
                    ]],
                ],
                'responses' => [
                    '200' => ['description' => 'Password reset successful'],
                    '422' => ['$ref' => '#/components/responses/UnprocessableEntity'],
                ],
            ],
        ],

        // ── Customer ──────────────────────────────────────────────
        '/customers/me' => [
            'get' => [
                'tags'        => ['Customer'],
                'summary'     => 'Get profile',
                'operationId' => 'customers.me',
                'security'    => [['BearerAuth' => []]],
                'responses'   => [
                    '200' => ['description' => 'Customer profile', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['$ref' => '#/components/schemas/Customer']]]]]],
                    '401' => ['$ref' => '#/components/responses/Unauthorized'],
                ],
            ],
            'put' => [
                'tags'        => ['Customer'],
                'summary'     => 'Update profile',
                'operationId' => 'customers.me.update',
                'security'    => [['BearerAuth' => []]],
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'first_name'        => ['type' => 'string'],
                                'last_name'         => ['type' => 'string'],
                                'phone'             => ['type' => 'string', 'nullable' => true],
                                'accepts_marketing' => ['type' => 'boolean'],
                            ],
                        ],
                    ]],
                ],
                'responses' => [
                    '200' => ['description' => 'Profile updated'],
                    '401' => ['$ref' => '#/components/responses/Unauthorized'],
                ],
            ],
        ],

        '/customers/me/orders' => [
            'get' => [
                'tags'        => ['Customer'],
                'summary'     => 'List my orders',
                'operationId' => 'customers.me.orders',
                'security'    => [['BearerAuth' => []]],
                'parameters'  => [
                    ['$ref' => '#/components/parameters/LimitParam'],
                    ['$ref' => '#/components/parameters/PageParam'],
                    ['name' => 'status', 'in' => 'query', 'schema' => ['type' => 'string', 'enum' => ['pending', 'paid', 'refunded', 'voided']], 'description' => 'Filter by financial status'],
                ],
                'responses' => [
                    '200' => ['description' => 'Paginated order list'],
                    '401' => ['$ref' => '#/components/responses/Unauthorized'],
                ],
            ],
        ],

        '/customers/me/addresses' => [
            'get' => [
                'tags'        => ['Customer'],
                'summary'     => 'List addresses',
                'operationId' => 'customers.me.addresses',
                'security'    => [['BearerAuth' => []]],
                'responses'   => [
                    '200' => ['description' => 'List of addresses'],
                    '401' => ['$ref' => '#/components/responses/Unauthorized'],
                ],
            ],
            'post' => [
                'tags'        => ['Customer'],
                'summary'     => 'Add address',
                'operationId' => 'customers.me.addresses.store',
                'security'    => [['BearerAuth' => []]],
                'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Address']]]],
                'responses'   => [
                    '201' => ['description' => 'Address created', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/CustomerAddress']]]],
                    '401' => ['$ref' => '#/components/responses/Unauthorized'],
                    '422' => ['$ref' => '#/components/responses/UnprocessableEntity'],
                ],
            ],
        ],

        '/customers/me/addresses/{address_id}' => [
            'put' => [
                'tags'        => ['Customer'],
                'summary'     => 'Update address',
                'operationId' => 'customers.me.addresses.update',
                'security'    => [['BearerAuth' => []]],
                'parameters'  => [['name' => 'address_id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Address']]]],
                'responses'   => ['200' => ['description' => 'Address updated'], '401' => ['$ref' => '#/components/responses/Unauthorized'], '404' => ['$ref' => '#/components/responses/NotFound']],
            ],
            'delete' => [
                'tags'        => ['Customer'],
                'summary'     => 'Delete address',
                'operationId' => 'customers.me.addresses.destroy',
                'security'    => [['BearerAuth' => []]],
                'parameters'  => [['name' => 'address_id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'responses'   => ['200' => ['description' => 'Address deleted'], '401' => ['$ref' => '#/components/responses/Unauthorized'], '404' => ['$ref' => '#/components/responses/NotFound'], '422' => ['$ref' => '#/components/responses/UnprocessableEntity']],
            ],
        ],

        '/customers/me/addresses/{address_id}/default' => [
            'put' => [
                'tags'        => ['Customer'],
                'summary'     => 'Set default address',
                'operationId' => 'customers.me.addresses.default',
                'security'    => [['BearerAuth' => []]],
                'parameters'  => [['name' => 'address_id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'responses'   => ['200' => ['description' => 'Default address updated'], '401' => ['$ref' => '#/components/responses/Unauthorized'], '404' => ['$ref' => '#/components/responses/NotFound']],
            ],
        ],

        // ── Products ──────────────────────────────────────────────
        '/products' => [
            'get' => [
                'tags'        => ['Products'],
                'summary'     => 'List products',
                'operationId' => 'products.index',
                'parameters'  => [
                    ['$ref' => '#/components/parameters/LimitParam'],
                    ['$ref' => '#/components/parameters/PageParam'],
                    ['name' => 'collection_id',  'in' => 'query', 'schema' => ['type' => 'integer'],  'description' => 'Filter by collection ID'],
                    ['name' => 'product_type',   'in' => 'query', 'schema' => ['type' => 'string'],   'description' => 'Filter by product type'],
                    ['name' => 'vendor',         'in' => 'query', 'schema' => ['type' => 'string'],   'description' => 'Filter by vendor'],
                    ['name' => 'title',          'in' => 'query', 'schema' => ['type' => 'string'],   'description' => 'Partial title search'],
                    ['name' => 'sort_by',        'in' => 'query', 'schema' => ['type' => 'string', 'enum' => ['title', 'created_at', 'price'], 'default' => 'created_at']],
                    ['name' => 'sort_direction', 'in' => 'query', 'schema' => ['type' => 'string', 'enum' => ['asc', 'desc'], 'default' => 'desc']],
                    ['name' => 'created_at_min', 'in' => 'query', 'schema' => ['type' => 'string', 'format' => 'date-time']],
                    ['name' => 'created_at_max', 'in' => 'query', 'schema' => ['type' => 'string', 'format' => 'date-time']],
                ],
                'responses' => [
                    '200' => ['description' => 'Paginated product list', 'content' => ['application/json' => ['schema' => [
                        'type' => 'object',
                        'properties' => [
                            'success' => ['type' => 'boolean'],
                            'data' => ['type' => 'object', 'properties' => ['products' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Product']]]],
                            'meta' => ['type' => 'object', 'properties' => ['pagination' => ['$ref' => '#/components/schemas/Pagination']]],
                        ],
                    ]]]],
                ],
            ],
        ],

        '/products/count' => [
            'get' => [
                'tags'        => ['Products'],
                'summary'     => 'Count active products',
                'operationId' => 'products.count',
                'responses'   => ['200' => ['description' => 'Product count', 'content' => ['application/json' => ['example' => ['success' => true, 'data' => ['count' => 42]]]]]],
            ],
        ],

        '/products/handle/{slug}' => [
            'get' => [
                'tags'        => ['Products'],
                'summary'     => 'Get product by handle (slug)',
                'operationId' => 'products.show-by-handle',
                'parameters'  => [['name' => 'slug', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string'], 'example' => 'chronograph-watch']],
                'responses'   => [
                    '200' => ['description' => 'Product', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['properties' => ['product' => ['$ref' => '#/components/schemas/Product']]]]]]]],
                    '404' => ['$ref' => '#/components/responses/NotFound'],
                ],
            ],
        ],

        '/products/{id}' => [
            'get' => [
                'tags'        => ['Products'],
                'summary'     => 'Get product by ID',
                'operationId' => 'products.show',
                'parameters'  => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'example' => 1]],
                'responses'   => [
                    '200' => ['description' => 'Product', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['properties' => ['product' => ['$ref' => '#/components/schemas/Product']]]]]]]],
                    '404' => ['$ref' => '#/components/responses/NotFound'],
                ],
            ],
        ],

        // ── Collections ──────────────────────────────────────────────
        '/collections' => [
            'get' => [
                'tags'        => ['Collections'],
                'summary'     => 'List collections',
                'operationId' => 'collections.index',
                'parameters'  => [['$ref' => '#/components/parameters/LimitParam'], ['$ref' => '#/components/parameters/PageParam']],
                'responses'   => [
                    '200' => ['description' => 'Collection list', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['properties' => ['collections' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Collection']]]]]]]]],
                ],
            ],
        ],

        '/collections/count' => [
            'get' => [
                'tags'        => ['Collections'],
                'summary'     => 'Count published collections',
                'operationId' => 'collections.count',
                'responses'   => ['200' => ['description' => 'Count', 'content' => ['application/json' => ['example' => ['success' => true, 'data' => ['count' => 8]]]]]],
            ],
        ],

        '/collections/handle/{slug}' => [
            'get' => [
                'tags'        => ['Collections'],
                'summary'     => 'Get collection by handle (slug)',
                'operationId' => 'collections.show-by-handle',
                'parameters'  => [['name' => 'slug', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string'], 'example' => 'best-sellers']],
                'responses'   => ['200' => ['description' => 'Collection'], '404' => ['$ref' => '#/components/responses/NotFound']],
            ],
        ],

        '/collections/{id}' => [
            'get' => [
                'tags'        => ['Collections'],
                'summary'     => 'Get collection by ID',
                'operationId' => 'collections.show',
                'parameters'  => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'responses'   => ['200' => ['description' => 'Collection'], '404' => ['$ref' => '#/components/responses/NotFound']],
            ],
        ],

        '/collections/{id}/products' => [
            'get' => [
                'tags'        => ['Collections'],
                'summary'     => 'Products in a collection',
                'operationId' => 'collections.products',
                'parameters'  => [
                    ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']],
                    ['$ref' => '#/components/parameters/LimitParam'],
                    ['$ref' => '#/components/parameters/PageParam'],
                ],
                'responses' => ['200' => ['description' => 'Paginated product list'], '404' => ['$ref' => '#/components/responses/NotFound']],
            ],
        ],

        // ── Cart ──────────────────────────────────────────────
        '/cart' => [
            'get' => [
                'tags'        => ['Cart'],
                'summary'     => 'Get cart',
                'operationId' => 'cart.show',
                'description' => 'Returns the current cart. Guests must send `X-Cart-Token` header. Authenticated customers\' carts are auto-resolved.',
                'security'    => [['BearerAuth' => []], []],
                'parameters'  => [['name' => 'X-Cart-Token', 'in' => 'header', 'required' => false, 'schema' => ['type' => 'string', 'format' => 'uuid'], 'description' => 'Guest cart token']],
                'responses'   => [
                    '200' => ['description' => 'Cart', 'content' => ['application/json' => ['schema' => ['properties' => ['success' => ['type' => 'boolean'], 'data' => ['$ref' => '#/components/schemas/Cart']]]]]],
                    '404' => ['$ref' => '#/components/responses/NotFound'],
                ],
            ],
        ],

        '/cart/add' => [
            'post' => [
                'tags'        => ['Cart'],
                'summary'     => 'Add items to cart',
                'operationId' => 'cart.add',
                'security'    => [['BearerAuth' => []], []],
                'parameters'  => [['name' => 'X-Cart-Token', 'in' => 'header', 'required' => false, 'schema' => ['type' => 'string', 'format' => 'uuid']]],
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'required' => ['items'],
                            'properties' => ['items' => ['type' => 'array', 'minItems' => 1, 'items' => ['$ref' => '#/components/schemas/LineItemInput']]],
                        ],
                        'example' => ['items' => [['variant_id' => 3, 'quantity' => 1]]],
                    ]],
                ],
                'responses' => [
                    '201' => ['description' => 'Items added', 'content' => ['application/json' => ['schema' => ['properties' => ['success' => ['type' => 'boolean'], 'cart_token' => ['type' => 'string'], 'data' => ['$ref' => '#/components/schemas/Cart']]]]]],
                    '422' => ['$ref' => '#/components/responses/UnprocessableEntity'],
                ],
            ],
        ],

        '/cart/update' => [
            'post' => [
                'tags'        => ['Cart'],
                'summary'     => 'Update item quantities',
                'operationId' => 'cart.update',
                'description' => 'Map of line_item_id → new_quantity. Pass 0 to remove an item.',
                'security'    => [['BearerAuth' => []], []],
                'parameters'  => [['name' => 'X-Cart-Token', 'in' => 'header', 'required' => false, 'schema' => ['type' => 'string']]],
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => [
                        'schema'  => ['type' => 'object', 'required' => ['updates'], 'properties' => ['updates' => ['type' => 'object', 'additionalProperties' => ['type' => 'integer', 'minimum' => 0]]]],
                        'example' => ['updates' => ['10' => 3, '11' => 0]],
                    ]],
                ],
                'responses' => ['200' => ['description' => 'Cart updated'], '404' => ['$ref' => '#/components/responses/NotFound']],
            ],
        ],

        '/cart/remove' => [
            'post' => [
                'tags'        => ['Cart'],
                'summary'     => 'Remove a line item',
                'operationId' => 'cart.remove',
                'security'    => [['BearerAuth' => []], []],
                'parameters'  => [['name' => 'X-Cart-Token', 'in' => 'header', 'required' => false, 'schema' => ['type' => 'string']]],
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['line_item_id'], 'properties' => ['line_item_id' => ['type' => 'integer', 'example' => 10]]]]],
                ],
                'responses' => ['200' => ['description' => 'Item removed'], '404' => ['$ref' => '#/components/responses/NotFound']],
            ],
        ],

        '/cart/clear' => [
            'post' => [
                'tags'        => ['Cart'],
                'summary'     => 'Clear all cart items',
                'operationId' => 'cart.clear',
                'security'    => [['BearerAuth' => []], []],
                'parameters'  => [['name' => 'X-Cart-Token', 'in' => 'header', 'required' => false, 'schema' => ['type' => 'string']]],
                'responses'   => ['200' => ['description' => 'Cart cleared']],
            ],
        ],

        '/cart/apply-discount' => [
            'post' => [
                'tags'        => ['Cart'],
                'summary'     => 'Apply discount code',
                'operationId' => 'cart.apply-discount',
                'security'    => [['BearerAuth' => []], []],
                'parameters'  => [['name' => 'X-Cart-Token', 'in' => 'header', 'required' => false, 'schema' => ['type' => 'string']]],
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['discount_code'], 'properties' => ['discount_code' => ['type' => 'string', 'example' => 'SAVE10']]]]],
                ],
                'responses' => [
                    '200' => ['description' => 'Discount applied', 'content' => ['application/json' => ['example' => ['applied' => true, 'discount_amount' => 30.0, 'error' => null]]]],
                    '422' => ['description' => 'Invalid code', 'content' => ['application/json' => ['example' => ['applied' => false, 'discount_amount' => 0, 'error' => 'Discount code not found']]]],
                ],
            ],
        ],

        '/cart/shipping-rates' => [
            'get' => [
                'tags'        => ['Cart'],
                'summary'     => 'Shipping rates for cart',
                'operationId' => 'cart.shipping-rates',
                'security'    => [['BearerAuth' => []], []],
                'parameters'  => [
                    ['name' => 'X-Cart-Token',  'in' => 'header', 'required' => false, 'schema' => ['type' => 'string']],
                    ['name' => 'country_code',  'in' => 'query', 'required' => true,  'schema' => ['type' => 'string', 'example' => 'US']],
                    ['name' => 'province_code', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'string']],
                    ['name' => 'zip',           'in' => 'query', 'required' => false, 'schema' => ['type' => 'string', 'example' => '10001']],
                ],
                'responses' => ['200' => ['description' => 'Shipping rates', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['properties' => ['shipping_rates' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/ShippingRate']]]]]]]]]],
            ],
        ],

        // ── Checkout ──────────────────────────────────────────────
        '/checkout/validate' => [
            'post' => [
                'tags'        => ['Checkout'],
                'summary'     => 'Validate checkout & calculate totals',
                'operationId' => 'checkout.validate',
                'description' => 'Calculates subtotal, discount, shipping, and tax. Use before rendering the order summary.',
                'security'    => [['BearerAuth' => []], []],
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'required' => ['cart_token', 'shipping_address'],
                            'properties' => [
                                'cart_token'        => ['type' => 'string', 'format' => 'uuid'],
                                'shipping_address'  => ['type' => 'object', 'required' => ['country_code'], 'properties' => ['country_code' => ['type' => 'string', 'example' => 'US'], 'province_code' => ['type' => 'string']]],
                                'discount_code'     => ['type' => 'string', 'nullable' => true],
                            ],
                        ],
                        'example' => ['cart_token' => '550e8400-e29b-41d4-a716-446655440000', 'shipping_address' => ['country_code' => 'US'], 'discount_code' => 'SAVE10'],
                    ]],
                ],
                'responses' => [
                    '200' => ['description' => 'Checkout totals', 'content' => ['application/json' => ['example' => ['success' => true, 'data' => ['subtotal' => 299.99, 'total_discounts' => 30.0, 'total_shipping' => 5.99, 'total_tax' => 27.0, 'total' => 302.98, 'shipping_rates_available' => true, 'shipping_rates' => []]]]]],
                    '422' => ['$ref' => '#/components/responses/UnprocessableEntity'],
                ],
            ],
        ],

        // ── Orders ──────────────────────────────────────────────
        '/orders' => [
            'post' => [
                'tags'        => ['Orders'],
                'summary'     => 'Create order',
                'operationId' => 'orders.store',
                'description' => 'Places a new order. Works for guests and authenticated customers. Returns 422 if stock is insufficient.',
                'security'    => [['BearerAuth' => []], []],
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'required' => ['line_items', 'shipping_address'],
                            'properties' => [
                                'customer_id'               => ['type' => 'integer', 'nullable' => true],
                                'email'                     => ['type' => 'string', 'format' => 'email', 'nullable' => true],
                                'phone'                     => ['type' => 'string', 'nullable' => true],
                                'line_items'                => ['type' => 'array', 'minItems' => 1, 'items' => ['$ref' => '#/components/schemas/LineItemInput']],
                                'shipping_address'          => ['$ref' => '#/components/schemas/Address'],
                                'billing_address'           => ['$ref' => '#/components/schemas/Address'],
                                'discount_code'             => ['type' => 'string', 'nullable' => true],
                                'note'                      => ['type' => 'string', 'nullable' => true],
                                'buyer_accepts_marketing'   => ['type' => 'boolean'],
                            ],
                        ],
                        'example' => [
                            'email' => 'jane@example.com',
                            'line_items' => [['variant_id' => 3, 'quantity' => 1]],
                            'shipping_address' => ['first_name' => 'Jane', 'last_name' => 'Doe', 'address1' => '123 Main St', 'city' => 'New York', 'country' => 'United States', 'country_code' => 'US', 'zip' => '10001'],
                        ],
                    ]],
                ],
                'responses' => [
                    '201' => ['description' => 'Order created', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['properties' => ['order' => ['$ref' => '#/components/schemas/Order']]]]]]]],
                    '422' => ['$ref' => '#/components/responses/UnprocessableEntity'],
                ],
            ],
        ],

        '/orders/{id}' => [
            'get' => [
                'tags'        => ['Orders'],
                'summary'     => 'Get order by ID or order number',
                'operationId' => 'orders.show',
                'security'    => [['BearerAuth' => []]],
                'parameters'  => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string'], 'description' => 'Order ID (integer) or order number (string)', 'example' => '1001']],
                'responses'   => [
                    '200' => ['description' => 'Order details', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['properties' => ['order' => ['$ref' => '#/components/schemas/Order']]]]]]]],
                    '401' => ['$ref' => '#/components/responses/Unauthorized'],
                    '404' => ['$ref' => '#/components/responses/NotFound'],
                ],
            ],
        ],

        '/orders/{id}/cancel' => [
            'post' => [
                'tags'        => ['Orders'],
                'summary'     => 'Cancel order',
                'operationId' => 'orders.cancel',
                'description' => 'Only orders with `financial_status = pending` that have not been fulfilled can be cancelled.',
                'security'    => [['BearerAuth' => []]],
                'parameters'  => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'responses'   => [
                    '200' => ['description' => 'Order cancelled'],
                    '401' => ['$ref' => '#/components/responses/Unauthorized'],
                    '422' => ['$ref' => '#/components/responses/UnprocessableEntity'],
                ],
            ],
        ],

        // ── Discounts ──────────────────────────────────────────────
        '/discount_codes/lookup' => [
            'post' => [
                'tags'        => ['Discounts'],
                'summary'     => 'Lookup / validate discount code',
                'operationId' => 'discounts.lookup',
                'description' => 'Validates a code and returns the discount amount. Does NOT apply the discount.',
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'required' => ['code', 'line_items'],
                            'properties' => [
                                'code'                    => ['type' => 'string', 'example' => 'SAVE10'],
                                'line_items'              => ['type' => 'array', 'minItems' => 1, 'items' => ['$ref' => '#/components/schemas/LineItemInput']],
                                'customer_id'             => ['type' => 'integer', 'nullable' => true],
                            ],
                        ],
                    ]],
                ],
                'responses' => [
                    '200' => ['description' => 'Discount info', 'content' => ['application/json' => ['example' => ['discount_code' => ['code' => 'SAVE10', 'amount' => 30.0, 'value_type' => 'percentage', 'value' => 10, 'minimum_amount' => null, 'minimum_quantity' => null], 'error' => null]]]],
                    '422' => ['description' => 'Invalid code', 'content' => ['application/json' => ['example' => ['discount_code' => null, 'error' => 'Discount code not found']]]],
                ],
            ],
        ],

        // ── Shipping ──────────────────────────────────────────────
        '/shipping/rates' => [
            'get' => [
                'tags'        => ['Shipping'],
                'summary'     => 'Get shipping rates',
                'operationId' => 'shipping.rates',
                'parameters'  => [
                    ['name' => 'shipping_address[country_code]',  'in' => 'query', 'required' => true,  'schema' => ['type' => 'string', 'example' => 'US']],
                    ['name' => 'shipping_address[province_code]', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'string']],
                    ['name' => 'line_items[0][variant_id]',       'in' => 'query', 'required' => true,  'schema' => ['type' => 'integer', 'example' => 3]],
                    ['name' => 'line_items[0][quantity]',         'in' => 'query', 'required' => true,  'schema' => ['type' => 'integer', 'example' => 1]],
                ],
                'responses' => ['200' => ['description' => 'Available rates', 'content' => ['application/json' => ['schema' => ['properties' => ['shipping_rates' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/ShippingRate']]]]]]]],
            ],
        ],

        '/shipping/estimate' => [
            'post' => [
                'tags'        => ['Shipping'],
                'summary'     => 'Estimate shipping (POST variant)',
                'operationId' => 'shipping.estimate',
                'description' => 'Same logic as GET /shipping/rates but accepts a JSON body.',
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'required' => ['shipping_address', 'line_items'],
                            'properties' => [
                                'shipping_address' => ['type' => 'object', 'required' => ['country_code'], 'properties' => ['country_code' => ['type' => 'string', 'example' => 'US'], 'province_code' => ['type' => 'string', 'nullable' => true]]],
                                'line_items'       => ['type' => 'array', 'minItems' => 1, 'items' => ['$ref' => '#/components/schemas/LineItemInput']],
                            ],
                        ],
                    ]],
                ],
                'responses' => ['200' => ['description' => 'Available rates']],
            ],
        ],

        // ── Search ──────────────────────────────────────────────
        '/search' => [
            'get' => [
                'tags'        => ['Search'],
                'summary'     => 'Full-text search',
                'operationId' => 'search',
                'description' => 'Searches across products, collections, pages, and articles. Returns a unified results array.',
                'parameters'  => [['name' => 'q', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'string', 'minLength' => 1], 'example' => 'chronograph']],
                'responses'   => [
                    '200' => ['description' => 'Search results', 'content' => ['application/json' => ['example' => ['success' => true, 'data' => ['results' => [['type' => 'product', 'id' => 1, 'title' => 'Chronograph Watch', 'url' => '/products/chronograph-watch'], ['type' => 'collection', 'id' => 2, 'title' => 'Best Sellers', 'url' => '/collections/best-sellers']]]]]]],
                    '422' => ['$ref' => '#/components/responses/UnprocessableEntity'],
                ],
            ],
        ],

        // ── CMS ──────────────────────────────────────────────
        '/pages' => [
            'get' => [
                'tags'        => ['CMS'],
                'summary'     => 'List CMS pages',
                'operationId' => 'pages.index',
                'responses'   => ['200' => ['description' => 'Pages', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['properties' => ['pages' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Page']]]]]]]]]],
            ],
        ],

        '/pages/{handle}' => [
            'get' => [
                'tags'        => ['CMS'],
                'summary'     => 'Get page by slug',
                'operationId' => 'pages.show',
                'parameters'  => [['name' => 'handle', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string'], 'example' => 'about-us']],
                'responses'   => ['200' => ['description' => 'Page', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['properties' => ['page' => ['$ref' => '#/components/schemas/Page']]]]]]]], '404' => ['$ref' => '#/components/responses/NotFound']],
            ],
        ],

        '/blogs' => [
            'get' => [
                'tags'        => ['CMS'],
                'summary'     => 'List blogs',
                'operationId' => 'blogs.index',
                'responses'   => ['200' => ['description' => 'Blogs', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['properties' => ['blogs' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Blog']]]]]]]]]],
            ],
        ],

        '/blogs/{handle}' => [
            'get' => [
                'tags'        => ['CMS'],
                'summary'     => 'Get blog by slug',
                'operationId' => 'blogs.show',
                'parameters'  => [['name' => 'handle', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string'], 'example' => 'news']],
                'responses'   => ['200' => ['description' => 'Blog'], '404' => ['$ref' => '#/components/responses/NotFound']],
            ],
        ],

        '/blogs/{handle}/articles' => [
            'get' => [
                'tags'        => ['CMS'],
                'summary'     => 'Articles in a blog',
                'operationId' => 'blogs.articles',
                'parameters'  => [['name' => 'handle', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string'], 'example' => 'news']],
                'responses'   => ['200' => ['description' => 'Articles', 'content' => ['application/json' => ['schema' => ['properties' => ['data' => ['properties' => ['articles' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Article']]]]]]]]]],
            ],
        ],

        '/articles/{id}' => [
            'get' => [
                'tags'        => ['CMS'],
                'summary'     => 'Get article by ID',
                'operationId' => 'articles.show',
                'parameters'  => [['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]],
                'responses'   => [
                    '200' => [
                        'description' => 'Article',
                        'content'     => [
                            'application/json' => [
                                'schema' => [
                                    'properties' => [
                                        'data' => [
                                            'properties' => [
                                                'article' => ['$ref' => '#/components/schemas/Article'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    '404' => ['$ref' => '#/components/responses/NotFound'],
                ],
            ],
        ],

        // ── Inventory (staff only) ──────────────────────────────────
        '/inventory' => [
            'get' => [
                'tags'        => ['Inventory'],
                'summary'     => 'List inventory items',
                'operationId' => 'inventory.index',
                'description' => '**Staff only** — requires a Sanctum web-guard token.',
                'security'    => [['BearerAuth' => []]],
                'responses'   => ['200' => ['description' => 'Inventory list'], '401' => ['$ref' => '#/components/responses/Unauthorized']],
            ],
        ],

        '/inventory/adjust' => [
            'post' => [
                'tags'        => ['Inventory'],
                'summary'     => 'Adjust inventory quantity',
                'operationId' => 'inventory.adjust',
                'security'    => [['BearerAuth' => []]],
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => [
                        'schema'  => ['type' => 'object', 'required' => ['inventory_item_id', 'location_id', 'adjustment'], 'properties' => ['inventory_item_id' => ['type' => 'integer'], 'location_id' => ['type' => 'integer'], 'adjustment' => ['type' => 'integer', 'description' => 'Positive to add, negative to subtract']]],
                        'example' => ['inventory_item_id' => 5, 'location_id' => 1, 'adjustment' => -3],
                    ]],
                ],
                'responses' => ['200' => ['description' => 'Adjustment applied'], '401' => ['$ref' => '#/components/responses/Unauthorized']],
            ],
        ],

        '/inventory/transfer' => [
            'post' => [
                'tags'        => ['Inventory'],
                'summary'     => 'Transfer inventory between locations',
                'operationId' => 'inventory.transfer',
                'security'    => [['BearerAuth' => []]],
                'requestBody' => [
                    'required' => true,
                    'content'  => ['application/json' => [
                        'schema'  => ['type' => 'object', 'required' => ['inventory_item_id', 'from_location_id', 'to_location_id', 'quantity'], 'properties' => ['inventory_item_id' => ['type' => 'integer'], 'from_location_id' => ['type' => 'integer'], 'to_location_id' => ['type' => 'integer'], 'quantity' => ['type' => 'integer', 'minimum' => 1]]],
                        'example' => ['inventory_item_id' => 5, 'from_location_id' => 1, 'to_location_id' => 2, 'quantity' => 10],
                    ]],
                ],
                'responses' => ['200' => ['description' => 'Transfer complete'], '401' => ['$ref' => '#/components/responses/Unauthorized']],
            ],
        ],

        '/inventory/{item}/history' => [
            'get' => [
                'tags'        => ['Inventory'],
                'summary'     => 'Inventory adjustment history',
                'operationId' => 'inventory.history',
                'security'    => [['BearerAuth' => []]],
                'parameters'  => [['name' => 'item', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'Inventory item ID']],
                'responses'   => ['200' => ['description' => 'History log'], '401' => ['$ref' => '#/components/responses/Unauthorized']],
            ],
        ],
    ],
];
