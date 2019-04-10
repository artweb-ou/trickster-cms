<?php return [
    'campaignSearch' => [
        'filters' => [
            [
                'fit',
                'width=350, height=350',
            ],
        ],
        'format' => [
            null,
            'jpg',
            '',
            '70',
        ],
    ],
    'campaignShortImage' => [
        'filters' => [
            [
                'reduce',
                'width=512, height=800',
            ],
        ],
        'format' => [
            null,
            'jpg',
            '',
            '80',
        ],
    ],
    'campaignThumbnail' => [
        'filters' => [
            [
                'reduce',
                'width=320, height=375',
            ],
            [
                'crop',
                'width=320, height=375, color=#ffffff',
            ],
        ],
        'format' => [
            null,
            'jpg',
            '',
            '85',
        ],
    ],
    'campaignDetailsImage' => [
        'filters' => [
            [
                'reduce',
                'width=520',
            ],
        ],
        'format' => [
            null,
            'jpg',
        ],
    ],
    'campaignBar' => [
        'filters' => [
            [
                'reduce',
                'height=256, width=256',
            ],
        ],
        'format' => [
            null,
            'png',
        ],
    ],
    'floorMapScheme' => [
        'filters' => [
            [
                'reduce',
                'width=1024, height=1024',
            ],
        ],
        'format' => [
            null,
            'png',
        ],
    ],
    'roomsMapShopLogo' => [
        'filters' => [
            [
                'reduce',
                'height=256, width=256',
            ],
        ],
        'format' => [
            null,
            'png',
        ],
    ],
    'roomsMapShopImage' => [
        'filters' => [
            [
                'reduce',
                'width=280,height=400',
            ],
        ],
        'format' => [
            null,
            'png',
        ],
    ],
    'roomsMapIcon' => [
        'filters' => [
            [
                'reduce',
                'width=200',
            ],
        ],
        'format' => [
            null,
            'png',
        ],
    ],
    'shopThumbnail' => [
        'filters' => [
            [
                'reduce',
                'width=256, height=215',
            ],
            [
                'crop',
                'width=256, height=215',
            ],
        ],
        'format' => [
            null,
            'png',
        ],
    ],
    'shopShortLogo' => [
        'filters' => [
            [
                'reduce',
                'width=256, height=122',
            ],
            [
                'crop',
                'width=256, height=122',
            ],
        ],
        'format' => [
            null,
            'png',
        ],
    ],
    'shopShortPhoto' => [
        'filters' => [
            [
                'reduce',
                'width=280, height=280',
            ],
        ],
        'format' => [
            null,
            'jpg',
            '',
            '80',
        ],
    ],
    'categoryShortIcon' => [
        'filters' => [
            [
                'reduce',
                'height=64, width=64',
            ],
        ],
        'format' => [
            null,
            'png',
        ],
    ],
    'headerGalleryImage' => [
        'filters' => [
            [
                'fit',
                'width=1550, height=430',
            ],
        ],
        'format' => [
            null,
            'jpg',
            '',
            85,
        ],
    ],
    'newsMailSubcontentWide' => [
        'filters' => [
            [
                'reduce',
                'height=272, width=599',
            ],
            [
                'crop',
                'height=272, width=599, color=#ffffff',
            ],
        ],
        'format' => [
            null,
            'jpg',
            '',
            '85',
        ],
    ],
    'newsMailSubcontent3Elements' => [
        'filters' => [
            [
                'reduce',
                'height=176, width=191',
            ],
            [
                'crop',
                'height=176, width=191, color=#ffffff',
            ],
        ],
        'format' => [
            null,
            'jpg',
            '',
            '85',
        ],
    ],
    'newsMailSubcontentSingle' => [
        'filters' => [
            [
                'reduce',
                'height=297, width=338',
            ],
            [
                'crop',
                'height=297, width=338, color=#ffffff',
            ],
        ],
        'format' => [
            null,
            'jpg',
            '',
            '85',
        ],
    ],
    'linklistItemButton' => [
        'filters' => [
            [
                'reduce',
                'width=70,height=70',
            ],
            [
                'crop',
                'width=70,height=70',
            ],
        ],
        'format' => [
            null,
            'png',
        ],
    ],
];