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

<div class="ds-catalog">
    <h1>Каталог</h1>
    <?foreach ($arResult as $key => $section):?>
        <div class="ds-catalog__menu">
            <div class="ds-catalog-header">
                <h2><a href="<?= $section['SECTION_PAGE_URL'] ?>"><?= $section['NAME'] ?></a></h2>
                <a href="<?= $section['SECTION_PAGE_URL'] ?>"
                   class="ds-catalog-header__quantity">показать <?= $section['COUNT'] ?> <?= $section['PRODUCTS'] ?></a>
            </div>
            <div class="ds-catalog-menu js-catalog-menu" data-slide="<?=$section['ID']?>">
                <div class="ds-catalog-menu__list">
                    <?foreach ($section['CHILDREN'] as $item):?>
                        <a class="ds-catalog-menu__item" href="<?=$item['SECTION_PAGE_URL']?>">
                            <?if(!empty($item['PICTURE'])):?>
                                <img src="<?=$item['PICTURE']['SRC']?>" alt="">
                            <?else:;?>
                                <?if($item['NAME'] == 'Смотреть все'):?>
                                    <span></span>
                                <?else:?>
                                    <img src="<?=SITE_TEMPLATE_PATH?>/images/no-img.jpg" alt="">
                                <?endif;?>
                            <?endif;?>
                            <p><?=$item['NAME']?></p>
                        </a>
                    <?endforeach;?>
                </div>
                <div class="ds-catalog-menu__arrow prev" data-slide="<?=$section['ID']?>"></div>
                <div class="ds-catalog-menu__arrow next" data-slide="<?=$section['ID']?>"></div>
            </div>
        </div>
    <?endforeach;?>
</div>