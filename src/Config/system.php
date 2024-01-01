<?php
return [

    /**
     * API.
     */
    [
        'key'  => 'apis',
        'name' => 'admin::app.configuration.index.apis.title',
        'info' => 'admin::app.configuration.index.apis.title',
        'sort' => 7,
    ], [
        'key'  => 'apis.repnet',
        'name' => 'admin::app.configuration.index.apis.repnet.title',
        'info' => 'admin::app.configuration.index.apis.repnet.title-info',
        'icon' => 'settings/repnet.svg',
        'sort' => 1,
    ], [
        'key'    => 'apis.repnet.api_setting',
        'name'   => 'admin::app.configuration.index.apis.repnet.api-setting.title',
        'info'   => 'admin::app.configuration.index.apis.repnet.api-setting.title-info',
        'sort'   => 1,
        'fields' => [
            [
                'name'       => 'api_key',
                'title'      => 'admin::app.configuration.index.apis.repnet.api-setting.api-key',
                'type'       => 'text',
                'default'    => '',
            ], [
                'name'       => 'api_secret',
                'title'      => 'admin::app.configuration.index.apis.repnet.api-setting.api-secret',
                'type'       => 'text',
                'default'    => '',
            ],
        ],
    ], [
        'key'    => 'apis.repnet.template_setting',
        'name'   => 'admin::app.configuration.index.apis.repnet.template-setting.title',
        'info'   => 'admin::app.configuration.index.apis.repnet.template-setting.title-info',
        'sort'   => 1,
        'fields' => [
            [
                'name'       => 'commission_type',
                'title'      => 'admin::app.configuration.index.apis.repnet.template-setting.commission-type',
                'type'       => 'select',
                'default'    => '',
                'options'       => [
                    [
                        'title' => 'Percentage Discount',
                        'value' => 'percent',
                    ], [
                        'title' => 'Fixed Discount',
                        'value' => 'fixed',
                    ],
                ],
            ], [
                'name'       => 'commission_percentage',
                'title'      => 'admin::app.configuration.index.apis.repnet.template-setting.commission-percentage',
                'type'       => 'text',
                'default'    => '',
            ], [
                'name'       => 'add_to_cart_button',
                'title'      => 'admin::app.configuration.index.apis.repnet.template-setting.add-to-cart-button',
                'type'       => 'boolean',
                'default'    => false,
            ], [
                'name'       => 'quick_view',
                'title'      => 'admin::app.configuration.index.apis.repnet.template-setting.quick-view',
                'type'       => 'boolean',
                'default'    => false,
            ], [
                'name'       => 'posts_per_page',
                'title'      => 'admin::app.configuration.index.apis.repnet.template-setting.posts-per-page',
                'type'       => 'text',
                'default'    => '',
            ], [
                'name'       => 'load_more_data_with',
                'title'      => 'admin::app.configuration.index.apis.repnet.template-setting.load-more-data-with',
                'type'       => 'text',
                'default'    => '',
            ], [
                'name'       => 'shop_page_loader',
                'title'      => 'admin::app.configuration.index.apis.repnet.template-setting.shop-page-loader',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'product_column',
                'title'      => 'admin::app.configuration.index.apis.repnet.template-setting.product-column',
                'type'       => 'select',
                'default'    => '',
                'options'       => [
                    [
                        'title' => '4 Column',
                        'value' => '4',
                    ], [
                        'title' => '3 Column',
                        'value' => '3',
                    ], [
                        'title' => '2 Column',
                        'value' => '2',
                    ], [
                        'title' => '1 Column',
                        'value' => '1',
                    ],
                ],
            ], [
                'name'       => 'reorder_fields',
                'title'      => 'admin::app.configuration.index.apis.repnet.template-setting.reorder-fields',
                'type'       => 'multiselect',
                'default'    => '',
                'options'       => [
                    [
                        'title' => '4 Column',
                        'value' => '4',
                    ], [
                        'title' => '3 Column',
                        'value' => '3',
                    ], [
                        'title' => '2 Column',
                        'value' => '2',
                    ], [
                        'title' => '1 Column',
                        'value' => '1',
                    ],
                ],
            ],
        ],
    ], [
        'key'    => 'apis.repnet.placeholder_setting',
        'name'   => 'admin::app.configuration.index.apis.repnet.placeholder-setting.title',
        'info'   => 'admin::app.configuration.index.apis.repnet.placeholder-setting.title-info',
        'sort'   => 1,
        'fields' => [
            [
                'name'       => 'default_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.placeholder-setting.default-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'round_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.placeholder-setting.round-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'princess_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.placeholder-setting.princess-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'marquise_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.placeholder-setting.marquise-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'oval_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.placeholder-setting.oval-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'radiant_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.placeholder-setting.radiant-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'emerald_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.placeholder-setting.emerald-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'heart_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.placeholder-setting.heart-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'cushion_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.placeholder-setting.cushion-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'asscher_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.placeholder-setting.asscher-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ],
        ],
    ], [
        'key'    => 'apis.repnet.shape_setting',
        'name'   => 'admin::app.configuration.index.apis.repnet.shape-setting.title',
        'info'   => 'admin::app.configuration.index.apis.repnet.shape-setting.title-info',
        'sort'   => 1,
        'fields' => [
            [
                'name'       => 'round_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.shape-setting.round-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'princess_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.shape-setting.princess-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'marquise_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.shape-setting.marquise-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'oval_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.shape-setting.oval-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'radiant_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.shape-setting.radiant-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'emerald_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.shape-setting.emerald-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'heart_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.shape-setting.heart-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'cushion_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.shape-setting.cushion-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'       => 'asscher_image',
                'title'      => 'admin::app.configuration.index.apis.repnet.shape-setting.asscher-image',
                'type'       => 'image',
                'validation' => 'mimes:bmp,jpeg,jpg,png,webp',
            ],
        ],
    ],

];