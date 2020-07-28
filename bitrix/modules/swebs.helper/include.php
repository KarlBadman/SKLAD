<?php
CModule::AddAutoloadClasses(
    'swebs.helper',
    array(
        'Swebs\Helper\Highload\Element' => 'lib/highload/element.php',
        'Swebs\Helper\IO\Serialize' => 'lib/io/serialize.php',
        'Swebs\Helper\Iblock\Element' => 'lib/iblock/element.php',
        'Swebs\Helper\Iblock\Section' => 'lib/iblock/section.php',
        'Swebs\Helper\Sale\Order' => 'lib/sale/order.php',
        'Swebs\Helper\Sale\Price' => 'lib/sale/price.php',
        'Swebs\Helper\Others\Strings' => 'lib/others/string.php',
        'Swebs\Helper\Others\Cookie' => 'lib/others/cookie.php',
        'Swebs\Helper\Main\User' => 'lib/main/user.php'
    )
);