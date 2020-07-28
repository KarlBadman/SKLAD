<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();?>

<?if (!empty($arResult['ELEMENTS'])):?>
    <?if (!empty($arResult['SECTION_CATALOG'])):?>
        <h4>Разделы</h4>
        <div class="header-search-result__list">
            <?foreach ($arResult['SECTION_CATALOG'] as $section):?>
                 <? if ($arItem['ID'] !== '157'):?>
                    <a class="header-search-result__item" href="<?=$section['SECTION_PAGE_URL']?>"><?=$section['NAME']?></a>
                <?endif;?>
            <?endforeach;?>
        </div>
    <?endif;?>
    <div class="header-search-result__list">
        <div class="mini-goods mini-goods--search">
            <?foreach ($arResult['ELEMENTS'] as $arItem):?>
                <? if ($arItem['IBLOCK_SECTION_ID'] !== '157'):?>
                    <a class="mini-goods__item" href="<?= $arItem['URL'] ?>">
                        <div class="mini-goods__img">
                            <img src="<? echo !empty($arItem['PICTURE']['src'])?  $arItem['PICTURE']['src'] :  SITE_TEMPLATE_PATH."/images/no_photo.jpg";?>" alt="">
                        </div>
                        <div class="mini-goods__content">
                            <p><?= $arItem['NAME'] ?></p>
                            <div class="mini-goods__info"><span class="ds-price"><?=$arItem['PRICE']?></span></div>
                        </div>
                    </a>
                <?endif;?>
            <?endforeach;?>
        </div>
    </div>
    <div class="header-search-result__search-btn"><button class="ds-btn ds-btn--invisible" name="search_btn" type="submit"><span class="icon-svg ic-search-result"></span>Все результаты поиска</button></div>

<?else:?>
    <?if(iconv_strlen($_REQUEST['q']) > 2):?>
    <h4>Ничего не найдено</h4>
    <div class="header-search-result__empty">
        <p>Воспользуйтесь<a href="/catalog/"> каталогом</a> или свяжитесь с нами по телефону<a href="tel:88007771274"> 8 800 777-12-74</a></p>
    </div>
    <?else:?>
        <h4>Популярно сейчас</h4>
        <div class="header-search-result__list">
            <?foreach ($arResult['LINKS'] as $link):?>
                <a class="header-search-result__item" href="<?=$link['URL']?>"><?=$link['TEXT']?></a>
            <?endforeach;?>
        </div>
    <?endif?>
<?endif;?>
