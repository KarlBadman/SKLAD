<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

function favorite($id){ // узнаем в закладках ли товар
    if(array_search($id,$_SESSION['FAVORITES']) !== false && is_array($_SESSION['FAVORITES'])){
        return true;
    }else{
        return false;
    }
}?>

<script>
    var btnFavorite = document.querySelector(window.dsCatalogDetail.selectors.addToFavorite);
    <?if(favorite($arResult['ID'])):?>
        btnFavorite.classList.add('added');
    <?else:?>
        btnFavorite.classList.remove('added');
    <?endif;?>
</script>
