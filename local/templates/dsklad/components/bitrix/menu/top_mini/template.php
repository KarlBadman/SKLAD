<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
    <div class="footer-menu__col">
        <h5 class="footer-menu__tab"><?=$arParams['TITLE_MENU']?><span class="arrow-down"></span></h5>
        <nav class="footer-menu__links">
            <?foreach($arResult as $arItem):?>
                <a href="<?=$arItem['LINK']?>"><?=$arItem['TEXT']?></a>
            <?endforeach?>
        </nav>
    </div>
<?endif?>
