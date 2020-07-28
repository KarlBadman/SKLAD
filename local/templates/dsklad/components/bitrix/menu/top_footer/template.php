<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
    <nav class="about-links">
        <?foreach($arResult as $arItem):?>
            <a href="<?=$arItem['LINK']?>"><?=$arItem['TEXT']?></a>
        <?endforeach?>
    </nav>
<?endif?>
