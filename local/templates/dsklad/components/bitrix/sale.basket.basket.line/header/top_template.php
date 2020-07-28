<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/**
 * @global array $arParams
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global string $cartId
 */

?>
<div class="ds-header__cart <?if($arResult['NUM_PRODUCTS'] > 0 && $arParams["SHOW_PRODUCTS"] ==  "Y"):?>has-popup<?endif?>">
<?if($arResult['NUM_PRODUCTS'] > 0):?>
    <a class="ds-header-notification" href="<?=$arParams['PATH_TO_BASKET']?>"><?=$arResult['NUM_PRODUCTS']?></a>
<?endif;?>
<a class="header-icon header-icon--cart" href="<?=$arParams['PATH_TO_BASKET']?>"></a>