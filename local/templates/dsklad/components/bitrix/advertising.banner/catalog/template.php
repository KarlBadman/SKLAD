<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>

<? $frame = $this->createFrame()->begin() ?>
<?/*
    <div class="default">
        <div class="bigbanner hidden-s">
            <a href="<?= $arResult['HREF'] ?>">
                <img src="<?= $arResult['SRC'] ?>" alt=""/>
            </a>
        </div>
    </div>
*/?>

<div style="background: url('<?= $arResult['SRC'] ?>') 50% 50% no-repeat" <?/*, linear-gradient(to top, #de0303, #f86e6e)*/?>
     class="banner hidden-s banner-catalog">
          <a href="<?= $arResult['HREF'] ?>" class=""></a>
</div>


<? $frame->end() ?>

<?/*

*/?>
