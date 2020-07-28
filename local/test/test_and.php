<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Uri;
Loader::includeModule('highloadblock');
Loader::includeModule('iblock');
Loader::includeModule('catalog');
?>

<!--<div class="zoom-section">
    <div class="zoom-small-image">
        <a href='/upload/resize_cache/uf/112/621_621_1/112c322ae71c56c46b8e05e7078d2df9.jpg' class = 'cloud-zoom' rel="position: 'inside' , showTitle: false, adjustX:-4, adjustY:-4">

            <img src="/upload/resize_cache/uf/112/66_66_1/112c322ae71c56c46b8e05e7078d2df9.jpg" title="Текст заголовка" alt=''/></a>
    </div>
</div>
-->

<?= "Директория с темой: ". SITE_TEMPLATE_PATH ?>

    <img src="<?= SITE_TEMPLATE_PATH ?>/images/logo_christmas.svg">

    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/logo_christmas.svg"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#logo"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-call"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-details"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-email"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-partner"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-questions"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-skype"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-whatsapp"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#bank"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#box"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#card"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#card2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cart"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cheap-arrow"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cheap-buyer"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cheap-factory"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cheap-profit"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cheap-store"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cheap-total"></use>
    </svg>

    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cheap-wholesale"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check3"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cross"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cross2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#darr"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#darr2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delete"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery-back"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery-exchange"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery-legal"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery-lift"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery-lock"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery-pack"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery-time"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery-week"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#"></use>
    </svg>

    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#delivery2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#download"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#expand"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#guarantee"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#help"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#larr"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#larr2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#lock"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#logo.short"></use>
    </svg>

    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#logo"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#logo2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#mastercard"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#menu-armchair"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#menu-chair"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#menu-light"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#menu-sale"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#menu-sofa"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#menu-table"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#moneyback"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-armchairs-office"></use>
    </svg>

    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-armchairs-relax"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-chairs-bar"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-chairs-eames"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-chairs-ghost"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-chairs-kids"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-chairs-masters"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-chairs-navy"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-chairs-panton"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-chairs-tolix"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-left"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-light-ceiling"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-light-floor"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-light-table"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-light-wall"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-others-clock"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-others-dekor"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-others-hangers"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-right"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-sofas-1"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-sofas-2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-sofas-3"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-tables-coffee"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#nav-tables-supper"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#number1"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#number2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#number30"></use>
    </svg>

    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#pickup"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#rarr"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#rarr2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#rarr3"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#search"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#share-facebook"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#share-facebook2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#share-instagram"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#share-like"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#share-twitter"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#share-vk"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#share-vk2"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#star-disabled"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#star-disabled2"></use>
    </svg>

    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#star-enabled"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#uarr"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#visa"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#wallet"></use>
    </svg>
    <svg>
        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#warranty"></use>
    </svg>




<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>