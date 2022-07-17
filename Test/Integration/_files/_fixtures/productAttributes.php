<?php

$productAttributes = [
    [
        'attribute_code'                => 'size',
        'is_global'                     => 1,
        'is_user_defined'               => 1,
        'frontend_input'                => 'select',
        'is_unique'                     => 0,
        'is_required'                   => 0,
        'is_searchable'                 => 0,
        'is_visible_in_advanced_search' => 0,
        'is_comparable'                 => 0,
        'is_filterable'                 => 0,
        'is_filterable_in_search'       => 0,
        'is_used_for_promo_rules'       => 0,
        'is_html_allowed_on_front'      => 1,
        'is_visible_on_front'           => 0,
        'used_in_product_listing'       => 0,
        'used_for_sort_by'              => 0,
        'frontend_label'                => ['Size'],
        'label'                         => 'Size',
        'backend_type'                  => 'int',
        'option'                        => [
            'value' => [
                'size_80' => ['S'],
                'size_81' => ['XS'],
                'size_79' => ['M'],
                'size_78' => ['L'],
                'size_77' => ['XL']
            ],
            'order' => ['size_80' => 1, 'size_81' => 2, 'size_79' => 3, 'size_78' => 4, 'size_77' => 5],
        ],
    ],
    [
        'attribute_code'                => 'colour',
        'is_global'                     => 1,
        'is_user_defined'               => 1,
        'frontend_input'                => 'select',
        'is_unique'                     => 0,
        'is_required'                   => 0,
        'is_searchable'                 => 0,
        'is_visible_in_advanced_search' => 0,
        'is_comparable'                 => 0,
        'is_filterable'                 => 0,
        'is_filterable_in_search'       => 0,
        'is_used_for_promo_rules'       => 0,
        'is_html_allowed_on_front'      => 1,
        'is_visible_on_front'           => 0,
        'used_in_product_listing'       => 0,
        'used_for_sort_by'              => 0,
        'frontend_label'                => ['Colour'],
        'label'                         => 'Colour',
        'backend_type'                  => 'int',
        'option'                        => [
            'value' => ['colour_26' => ['Indigo'], 'colour_27' => ['Red']],
            'order' => ['colour_26' => 1, 'colour_27' => 2]
        ],
    ]

];
