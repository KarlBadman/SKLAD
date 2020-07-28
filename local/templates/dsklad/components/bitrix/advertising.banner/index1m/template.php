<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$frame = $this->createFrame()->begin();
?>
<div class="slider-banners hidden-gt-s">
    <?
    foreach ($arResult['BANNERS'] as $arBanner) {
        ?>
        <div class="slide">
            <div class="bigbanner">
                <a href="<?= $arBanner['HREF'] ?>">
                    <picture>
                        <img src="<?= $arBanner['SRC'] ?>" alt="">
                    </picture>
                </a>
            </div>
        </div>
        <?
    }
    ?>
</div>
<?
$frame->end();
?>