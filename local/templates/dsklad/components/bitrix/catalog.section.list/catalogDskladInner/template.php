<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<? if(count($arResult['SECTIONS']) > 0) :?>
<div class="ds-catalog__menu">
    <div class="ds-catalog-menu js-catalog-menu" data-slide="<?=$arParams['SECTION_CODE']?>">
        <div class="ds-catalog-menu__list">
            <?foreach ($arResult['SECTIONS'] as $key => $item):?>
                <a class="ds-catalog-menu__item <? if ($item['ACTIVE'] == 'Y'): ?>current<? endif ?>"
                   href="<?= $item['SECTION_PAGE_URL'] ?>">
                    <?if(!empty($item['PICTURE'])):?>
                        <img src="<?=$item['PICTURE']['SRC']?>" alt="">
                    <?else:;?>
                        <img src="<?=SITE_TEMPLATE_PATH?>/images/no-img.jpg" alt="">
                    <?endif;?>
                    <p><?=$item['NAME']?></p>
                </a>
            <?endforeach;?>
        </div>
        <div class="ds-catalog-menu__arrow prev" data-slide="<?=$arParams['SECTION_CODE']?>"></div>
        <div class="ds-catalog-menu__arrow next" data-slide="<?=$arParams['SECTION_CODE']?>"></div>
    </div>
</div>
<?endif;?>