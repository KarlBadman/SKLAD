<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?if (!empty($arResult['ITEMS'])) : ?>
    <section class="instagram-module default">
        <h2>
            Подписывайтесь на нас в
            <a href="https://www.instagram.com/dsklad.ru/" target="_blank" rel="noopener" class="instagram-icon">
				<img src="<?=SITE_TEMPLATE_PATH?>/images/instagram-logo-img.svg" alt="Инстаграм">
                <img src="<?=SITE_TEMPLATE_PATH?>/images/instagram-logo-big.svg" class="instagram-logo-text" alt="Инстаграм">
            </a>
        </h2>
        <div class="js-slider instagram-slider">
            <?foreach ($arResult['ITEMS'] as $arItem) : ?>
                <div class="instagram-slider__item">
                    <?if ($arItem['UF_LIKES'] > 0) : ?>
                        <div class="instagram-likes js-popup-init">
                            <img alt="like" src="/local/templates/dsklad/images/heart.svg">
                            <span><?=$arItem['UF_LIKES']?></span>
                        </div>
                    <?endif;?>
                    
                    <?if (!empty($arItem['UF_TAG_LINK']) && !empty($arItem['UF_TAG'])) :?>
                        <div class="instagram-author">
                            <a target="_blank" rel="noopener" href="<?=$arItem['UF_TAG_LINK']?>">@<?=$arItem['UF_TAG']?></a>
                        </div>
                    <?endif;?>
                    
                    <?if (!empty($arItem['UF_POPUP_TEXT']) && !empty($arItem['UF_POPUP_LINK']) && !empty($arItem['UF_POPUP_TEXT']) && !empty($arItem['UF_IMG']['SRC'])) : ?>
                        <div class="instagram-popup" style="left: <?=($arItem['UF_POPUP_LEFT_POS'] ? : 0)?>px; top: <?=($arItem['UF_POPUP_TOP_POS'] ? : 0)?>px;">
                            <p><?=$arItem['UF_POPUP_TEXT']?></p>
                            <a href="<?=$arItem['UF_POPUP_LINK']?>">Перейти к товару</a>
                        </div>
                        <div class="instagram-img js-popup-init"><img src="<?=$arItem['UF_IMG']['SRC']?>" alt="<?=$arItem['UF_POPUP_TEXT']?>"></div>
                    <?endif;?>
                </div>
            <?endforeach;?>
            <div class="instagram-slider__item">
                <a href="https://www.instagram.com/dsklad.ru/" class="instagram-slide-last">
                    <div class="instagram-link">
                        <div class="instagram-count"><?=number_format((float)$arResult['FOLLOWERS_COUNT'], 0, ".", ",")?></div>
                        <div class="instagram-info">подписчиков DSKLAD</div>
                        <div class="instagram-profile">
                            <span class="instagram-profile-link">Посмотреть профиль</span>
                            <span class="instagram-arrow"></span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <a href="https://www.instagram.com/dsklad.ru/" class="instagram-slide-last instagram-slide-last--mobile">
            <div class="instagram-link">
                <div class="instagram-count"><?=number_format((float)$arResult['FOLLOWERS_COUNT'], 0, ".", ",")?></div>
                <div class="instagram-info">подписчиков DSKLAD</div>
                <div class="instagram-profile">
                    <span class="instagram-profile-link">Посмотреть профиль</span>
                    <span class="instagram-arrow"></span>
                </div>
            </div>
        </a>
    </section>
<?endif;?>