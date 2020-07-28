<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>

<? $frame = $this->createFrame()->begin() ?>
    <div class="column-<?= $arParams['COLUMN'] ?>">
        <a href="<?= $arResult['HREF'] ?>" <?=(strlen($arResult['BANNER_PROPERTIES']['URL_TARGET']))?' target="blank"':'';?>>
            <picture>
            		<source srcset="<?= $arResult['SRC'] ?>" media="(min-width: 1366px)">
            		<source srcset="<?= $arResult['SRC_1366'] ?>" media="(min-width: 1024px) and (max-width: 1365px)">
            		<source srcset="<?= $arResult['SRC_1024'] ?>" media="(min-width: 768px) and (max-width: 1023px)">
            		<source srcset="<?= $arResult['SRC_768'] ?>" media="(min-width: 320px) and (max-width: 767px)">
            		<source srcset="<?= $arResult['SRC_320'] ?>" media="(min-width: 319px)">
            		<img src="<?= $arResult['SRC'] ?>">
            </picture>
        </a>
    </div>
<? $frame->end() ?>