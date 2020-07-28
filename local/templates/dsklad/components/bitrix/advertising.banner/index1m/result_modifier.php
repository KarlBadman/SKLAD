<?php
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

foreach ($arResult['BANNERS'] as $key => $arBanner) {
    preg_match("/href=\"(.+?)\"/", $arBanner, $arMatch);
    $href = $arMatch[1];

    preg_match("/src=\"(.+?)\"/", $arBanner, $arMatch);
    $image = $arMatch[1];

    $arResult['BANNERS'][$key] = [
        'HREF' => $href,
        'SRC' => $image
    ];
}
