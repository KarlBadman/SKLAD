<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

global $APPLICATION;

//delayed function must return a string
if (empty($arResult)) {
    return '';
}

$strReturn = '';
$strReturn .= '<ul itemscope itemtype="http://schema.org/BreadcrumbList" class="ds-breadcrumbs">';

$itemSize = count($arResult);
for ($index = 0; $index < $itemSize; $index++) {
    $title = htmlspecialcharsex($arResult[$index]['TITLE']);
    if ($index != $itemSize - 1) {
        $strReturn .= '
        <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <a itemprop="item" href="' . $arResult[$index]['LINK'] . '">
                <meta itemprop="name" content="' . $title . '">
                ' . $title . '
            </a>
            <meta itemprop="position" content="'. ($index + 1) .'" />
        </li>';
    }
}

$strReturn .= '</ul>';

return $strReturn;