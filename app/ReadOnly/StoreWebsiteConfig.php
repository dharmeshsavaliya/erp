<?php

namespace App\ReadOnly;

use App\ReadOnlyBase;

class StoreWebsiteConfig extends ReadOnlyBase
{

    protected $data = [
        'store_id'     => [
            'veralusso.com'     => [
                14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,3,4,5,1,6,7,11,9,10,12,8,
            ],
            'avoirchic.com'     => [
                15,16,17,18,19,20,21,22,23,24,25,26,27,46,47,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,2,3,4,1,5,6,7,8,9,10,11,
            ],
            'brands-labels.com' => [
                42,43,44,45,46,47,48,49,50,51,52,53,54,73,74,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,71,72,3,4,5,2,6,7,8,9,10,11,1,
            ],
        ],
        'website_id'   => [
            'veralusso.com'     => [
                2,3,4,5,6,7,8,9,
            ],
            'avoirchic.com'     => [
                3,4,5,6,7,8,9,10,1,
            ],
            'brands-labels.com' => [
                6,7,8,9,10,11,12,13,1,
            ],
        ],
        'size_id'   => [
            'veralusso.com'     => 202,
            'avoirchic.com'     => 265,
            'brands-labels.com' => 176
        ],
        'attribute_set_id'   => [
            'veralusso.com'     => 16,
            'avoirchic.com'     => 16,
            'brands-labels.com' => 9
        ],
        'simple_attribute_set_id'   => [
            'veralusso.com'     => 4,
            'avoirchic.com'     => 4,
            'brands-labels.com' => 4
        ]
    ];
}
