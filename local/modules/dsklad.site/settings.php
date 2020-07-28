<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** Настройки */
$settings = [
    'autoload' => [
        'lib' => [
            '\Dsklad\Tools\Helpers' => 'lib/Tools/Helpers.php',
            '\Dsklad\Order' => 'lib/Order/Order.php',
            '\Dsklad\Delivery' => 'lib/Delivery/Delivery.php',
            '\Dsklad\DPD_service_my' => 'lib/DPD_service_my/DPD_service_my.php',
            '\Dsklad\Reviews' => 'lib/Reviews/Reviews.php',
            '\Dsklad\Product' => 'lib/Product/Product.php',
            '\Dsklad\PromoCodeFor4Views' => 'lib/PromoCodeFor4Views.php',
        ],
        'modules' => [
            'iblock',
            'sale',
            'highloadblock'
        ]
    ]
];
