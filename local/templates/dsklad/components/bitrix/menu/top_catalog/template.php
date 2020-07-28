<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>
<div class="header-menu__content">
    <div class="js-submenu">
    <?
    $previousLevel = 0;
    foreach($arResult as $arItem): ?>
        <?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
                </ul>
            </nav>
         </div>
        <?endif?>

        <?if($arItem["DEPTH_LEVEL"] == '1' && !$arItem["IS_PARENT"]):?>
            <div class="header-menu__item<?if($arItem['SELECTED']):?> active<?endif?>">
                <a class="menu-item" href="<?=$arItem['LINK']?>">
                    <? if(!empty($arItem['MENUCLASS'])){?>
                        <span class="icon-svg <?=$arItem['MENUCLASS'];?>"></span>
                    <? }?>
                    <span class="menu-item__title"><?=$arItem['TEXT']?></span>
                </a>
            </div>
        <?elseif ($arItem["DEPTH_LEVEL"] == '1' && $arItem["IS_PARENT"] && ($arItem["DEPTH_LEVEL"] == $previousLevel || $previousLevel == 0 || $arItem["DEPTH_LEVEL"] < $previousLevel)):?>
            <div class="header-menu__item<?if($arItem['SELECTED']):?> active<?endif?>">
                <a class="menu-item has-submenu" href="<?=$arItem['LINK']?>">
                    <? if(!empty($arItem['MENUCLASS'])){?>
                        <span class="icon-svg <?=$arItem['MENUCLASS'];?>"></span>
                    <? }?>
                    <span class="menu-item__title"><?=$arItem['TEXT']?></span>
                </a>
                <nav class="sub-menu">
                    <ul>
                        <li><h3><?=$arItem['TEXT']?></h3></li>
                        <li>
                            <a class="submenu-title" href="<?=$arItem['LINK']?>">Смотреть все</a>
                        </li>
        <?elseif ($arItem["DEPTH_LEVEL"] != '1'):?>
                        <li>
                            <a class="submenu-title<?if($arItem['SELECTED']):?> active<?endif?>" href="<?=$arItem['LINK']?>"><?=$arItem['TEXT']?></a>
                            <?if(!empty($arItem['TOP_ELEMENT'])): ?>
                                <div class="sub-menu__offers">
                                    <div class="sub-menu-offer">
                                        <div class="sub-menu-offer__img"><a href="<?=$arItem['TOP_ELEMENT']['DETAIL_PAGE_URL']?>"><img src="<?=$arItem['TOP_ELEMENT']['IMAGES']?>" alt=""></a></div>
                                        <div class="sub-menu-offer__title"><a href="<?=$arItem['TOP_ELEMENT']['DETAIL_PAGE_URL']?>"><?=$arItem['TOP_ELEMENT']['NAME']?></a></div>
                                        <div class="sub-menu-offer__price">
                                            <div class="ds-price"><?=number_format($arItem['TOP_ELEMENT']['CATALOG_PRICE_2'],0,'.',' ')?></div>
                                        </div>
                                        <div class="sub-menu-offer__btn"><a class="ds-btn ds-btn--light" href="<?=$arItem['TOP_ELEMENT']['DETAIL_PAGE_URL']?>">К товару</a></div>
                                    </div>
                                </div>
                            <?endif;?>
                        </li>
        <?endif?>
        <?$previousLevel = $arItem["DEPTH_LEVEL"];?>
    <?endforeach?>
    </div>
</div>
<?endif?>
