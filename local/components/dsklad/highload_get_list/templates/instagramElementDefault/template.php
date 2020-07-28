<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if (!empty($arResult['ITEMS'])) : ?>
    <div class="good-info__item good-info--instagram">
        <div class="good-info__title"><span>Фото товаров</span><a class="good-info__title-link" href="https://www.instagram.com/dsklad.ru/" target="_blank"> @dsklad.ru</a></div>
        <div class="goods-insta js-ds-modal " data-href="<?=SITE_TEMPLATE_PATH?>/components/bitrix/catalog.element/catalogDsklad/ajax/instagramModal.php" data-ds-modal-width="1100">
            <div class="goods-insta__content">
                 <?foreach ($arResult['ITEMS'] as $arItem) : ?>
                    <div class="goods-insta__item"><img src="<?=$arItem['UF_IMG']['src']?>" alt="<?=$arItem['UF_POPUP_TEXT']?>"></div>
                 <?endforeach;?>
            </div>
            <div class="goods-insta__content-last">
                <div class="goods-insta__item goods-insta__item--last"><span class="quantity">Ещё</span><img src="<?=end($arResult['ITEMS'])['UF_IMG']['src']?>" alt=""></div>
            </div>
        </div>
    </div>
<?endif;?>