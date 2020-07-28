<?php
$arUrlRewrite=array (
  0 => 
  array (
    'CONDITION' => '#^/catalog/([^/]+?)/([^/]+?)(?<!/tags)/([^/]+?)/.*#',
    'RULE' => 'SECTION_CODE=$1&ELEMENT_CODE=$2&offers=$3',
    'ID' => '',
    'PATH' => '/catalog/index.php',
    'SORT' => 100,
  ),
  1 => 
  array (
    'CONDITION' => '#^/online/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#',
    'RULE' => 'alias=$1',
    'ID' => '',
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  3 => 
  array (
    'CONDITION' => '#^/catalog/([^/]+?)/([^/]+?)/.*#',
    'RULE' => 'SECTION_CODE=$1&ELEMENT_CODE=$2',
    'ID' => '',
    'PATH' => '/catalog/index.php',
    'SORT' => 100,
  ),
  4 => 
  array (
    'CONDITION' => '#^/personal/order/([^/]+?)/.*$#',
    'RULE' => 'ORDER_ID=$1',
    'ID' => '',
    'PATH' => '/personal/order.php',
    'SORT' => 100,
  ),
  5 => 
  array (
    'CONDITION' => '#^/personal/pay/([^/]+?)/.*$#',
    'RULE' => 'ORDER_ID=$1',
    'ID' => '',
    'PATH' => '/personal/pay.php',
    'SORT' => 100,
  ),
  6 => 
  array (
    'CONDITION' => '#^/acrit.exportproplus/(.*)#',
    'RULE' => 'path=$1',
    'ID' => '',
    'PATH' => '/acrit.exportproplus/index.php',
    'SORT' => 100,
  ),
  18 => 
  array (
    'CONDITION' => '#^/botrest/([0-9a-zA-Z]+)/#',
    'RULE' => 'METHOD=$1&',
    'ID' => '',
    'PATH' => '/local/dbot/index.php',
    'SORT' => 100,
  ),
  20 => 
  array (
    'CONDITION' => '#^/order/thankyou/([0-9]+)([?^/][0-9a-zA-Z]+)#',
    'RULE' => 'ORDER_ID=$1&ORDER_AUTH=$2',
    'ID' => '',
    'PATH' => '/order/index.php',
    'SORT' => 100,
  ),
  array (
    'CONDITION' => '#^/order/thankyou/([0-9]+)(.*)$#',
    'RULE' => 'ORDER_ID=$1',
    'ID' => '',
    'PATH' => '/order/index.php',
    'SORT' => 100,
  ),
  7 => 
  array (
    'CONDITION' => '#^/catalog/([^/]+?)/[^/]*$#',
    'RULE' => 'SECTION_CODE=$1',
    'ID' => '',
    'PATH' => '/catalog/index.php',
    'SORT' => 100,
  ),
  8 => 
  array (
    'CONDITION' => '#^/acrit.exportpro/(.*)#',
    'RULE' => 'path=$1',
    'ID' => '',
    'PATH' => '/acrit.exportpro/index.php',
    'SORT' => 100,
  ),
  9 => 
  array (
    'CONDITION' => '#^/online/(/?)([^/]*)#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  10 => 
  array (
    'CONDITION' => '#^/public_offer/.*#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/public_offer/index.php',
    'SORT' => 100,
  ),
  11 => 
  array (
    'CONDITION' => '#^/partnership/.*#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/partnership/index.php',
    'SORT' => 100,
  ),
  12 => 
  array (
    'CONDITION' => '#^/contacts/.*#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/contacts/index.php',
    'SORT' => 100,
  ),
  13 => 
  array (
    'CONDITION' => '#^/delivery/.*#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/delivery/index.php',
    'SORT' => 100,
  ),
  14 => 
  array (
    'CONDITION' => '#^/pickup/([^/]*)#',
    'RULE' => 'TERMINAL_CODE=$1',
    'ID' => '',
    'PATH' => '/pickup/index.php',
    'SORT' => 90,
  ),
  array (
    'CONDITION' => '#^/pickup/.*#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/pickup/index.php',
    'SORT' => 100,
  ),
  15 => 
  array (
    'CONDITION' => '#^/about/.*#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/about/index.php',
    'SORT' => 100,
  ),
  19 => 
  array (
    'CONDITION' => '#^/botrest/#',
    'RULE' => 'PARAMS=$1',
    'ID' => '',
    'PATH' => '/local/dbot/index.php',
    'SORT' => 100,
  ),
  16 => 
  array (
    'CONDITION' => '#^/otzyvy/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/otzyvy/index.php',
    'SORT' => 100,
  ),
  17 => 
  array (
    'CONDITION' => '#^\\??(.*)#',
    'RULE' => '&$1',
    'ID' => 'project:catalog.section',
    'PATH' => '/tst.php',
    'SORT' => 100,
  ),
);
