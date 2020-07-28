<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Swebs\Helper;

Loader::includeModule('sale');
Loader::includeModule('swebs.helper');

function bla()
{
    $arElements = Helper\Highload\Element::getElement(22, array(), array('ID'));

    foreach ($arElements as $arElement) {
        Helper\Highload\Element::update(22, $arElement['ID'], array('UF_SORT' => 500));
    }
}

/*Debug::startTimeLabel("bla");
bla();
Debug::endTimeLabel("bla");

print_r(Debug::getTimeLabels());*/