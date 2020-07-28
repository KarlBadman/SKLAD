<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$frame = $this->createFrame()->begin();
?>
<?if(!$arParams['MOBILE']):?>
    <div class="ds-wrapper hidden-s">
        <div class="slider-banners">
<?else:;?>
        <div class="slider-banners hidden-gt-s">
<?endif;?>
        <? foreach ($arResult['BANNERS'] as $arBanner) {
            $fileinfo = pathinfo($arBanner['SRC']);
            $newfile = $fileinfo['dirname'] . '/' . $fileinfo['filename'] . '@1x.' . $fileinfo['extension'];
            ?>
            <div class="slide">
                <div class="bigbanner">
                    <a href="<?= $arBanner['HREF'] ?>">
                        <picture>
                            <? if (file_exists($_SERVER['DOCUMENT_ROOT'] . $newfile)) {?>
                                <img src="<?=$newfile?>" srcset="<?= $arBanner['SRC'] ?> 2x" alt="">
                            <? } else { ?>
                            <img src="<?= $arBanner['SRC'] ?>" alt="">
                            <? } ?>
                        </picture>
                    </a>
                </div>
            </div>
            <?
        }
        ?>
    <?if(!$arParams['MOBILE']):?>
        </div>
    <?endif;?>
</div>
<?
$frame->end();
?>