<?php
if (\Bitrix\Main\Config\Option::get('main', 'update_devsrv') == 'Y') {
    define('IS_DEV', true);
} else {
    define('IS_DEV', false);
}

// цвета для статусов
const ORDER_STATUS_COLOR = array(
    'F' => 'gray', //CCCCCC
    'N' => 'gray', //808080
    'DN' => 'green', //008000
    'AC' => 'blue', //0000FF
    'WT' => 'gray', //808080
    'CM' => 'yellow', //FFFF00
    'RT' => 'red', //FF0000
    'DF' => 'green' //008000
);

// Доставка и самовывоз Москва и Питер
const DELIVERY_COAST_MSK = 900;
const DELIVERY_COAST_SPB = 950;
const PICKUP_COAST = 0; // TODO выпилить потом
const PICKUP_COAST_SPB = 500; // терминалы СПБ
const PICKUP_COAST_MSK = 250; // терминалы МСК
const PICKUP_PVZ_COAST_SPB = 549; // ПВЗ СПБ
const PICKUP_PVZ_COAST_MSK = 250; // ПВЗ МСК

const UR_PICKUP_COAST_SPB = 500;
const UR_PICKUP_PVZ_COAST_SPB = 700;
const UR_PICKUP_COAST_MSK = 700;
const UR_PICKUP_PVZ_COAST_MSK = 900;
const DPD_CITY_ID_FROM = 49694102;
const UR_DELIVERY_COEF = 715; // Дополнительный коэффициент доставки
const UR_DELIVERY_COEF_PERCENT = 0.01; // Дополнительный коэффициент доставки процент от корзины

const KGT_WEIGHT = 30;

const LOG_DIRECTORY =  '/_log_orders/';

const NPP_SUMM_LIMIT = 100000;

// Терминалы DPD которые не загружаем по тем или иным причинам
const NO_LOAD_TERMINALS = array(
    '260T', '048L', '195T', '63J', '159O'
);

const CATALOG_IBLOCK_ID = 35;
const DPD_CITIES_HL_ID = 22;
const DPD_UPAKOVKI_HL_ID = 26;
const NULLEDSEARCHREQUESTS = 30;
const SETTINGS_HL_ID = 27;

const TARGET_MY_COM_PRICE_LIST_ID = 1;

// Rating@Mail.ru counter - страницы
const PAGE_TYPES_TARGET_MY_COM = array(
    'view_home' => 'home', // Главная страница
    'view_category' => 'category', // Страница категории
    'view_product' => 'product', // Страница продукта
    'init_checkout' => 'cart', // Страница корзины
    'purchase' => 'purchase', // Страница покупки
    'view_other' => 'other', // Все остальные страницы
    'view_search' => 'searchresults', // результаты поиска
);

// Rating@Mail.ru counter - страницы, на которых счетчик вызывается без параметров
const PAGE_TYPES_TARGET_MY_COM_NO_PARAMS = array(
    'home', // Главная страница
    'category', // Страница категории
    'other', // Все остальные страницы
    'searchresults', // результаты поиска
);

//placeholder для строки поиска
const SEARCH_PLACEHOLDERS = array(
    'Например: Стулья для кафе',
    'Например: Стулья для кухни',
    'Например: Стулья Tolix',
    'Например: Круглый стол',
    'Например: Стеклянный стол',
    'Например: Прозрачные стулья',
    'Например: Металлические стулья',
    'Например: Стулья в стиле лофт'
);

const ADDRESS_1C_SERVICES = 'http://217.195.75.200:48900';

const DELIVERY_PICKUP_ID = array(14,15,22); // Id доставок типа самовывоз
