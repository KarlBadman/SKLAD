<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arParams
 * @var array $arResult
 */

foreach ($arResult['RELATED'] as $key => $item) {
    $image = \CFile::ResizeImageGet(
        $item['IMAGE'],
        [
            'width' => 100,
            'height' => 100
        ],
        BX_RESIZE_IMAGE_PROPORTIONAL,
        true
    );

    $arResult['RELATED'][$key]['IMAGE'] = $image['src'];
}
