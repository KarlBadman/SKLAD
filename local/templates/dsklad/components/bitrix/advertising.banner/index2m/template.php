<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>

<? if(!empty($arResult['SRC'])) { ?>
    <? $frame = $this->createFrame()->begin() ?>
        <div class="column-<?= $arParams['COLUMN'] ?>">
            <a href="<?= $arResult['HREF'] ?>">
                <img src="<?= $arResult['SRC'] ?>" alt=""/>
            </a>
        </div>
    <? $frame->end() ?>
<? }?>
