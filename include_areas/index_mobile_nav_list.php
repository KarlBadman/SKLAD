<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<nav class="add-mobile-nav">
    <div class="add-mobile-nav-row">
        <strong class="cell"><a href="/catalog/stulya/">Стулья:</a></strong>
        <strong class="cell"><a href="/catalog/stoly/">Столы:</a></strong>
    </div>
    <div class="add-mobile-nav-row">
        <span class="cell"><a href="/catalog/stulya/tags/dizajnerskie_stulya/">Дизайнерские</a></span>
        <span class="cell"><a href="/catalog/stoly/tags/kruglye/">Круглые</a></span>
    </div>
    <div class="add-mobile-nav-row">
        <span class="cell"><a href="/catalog/stulya/tags/barnye_stulya/">Барные</a></span>
        <span class="cell"><a href="/catalog/stoly/tags/pryamougolnye/">Прямоугольные</a></span>
    </div>
    <div class="add-mobile-nav-row">
        <span class="cell"><a href="/catalog/stulya/tags/prozrachnye_stulya/">Прозрачные</a></span>
        <span class="cell"><a href="/catalog/stoly/tags/kvadratnye/">Квадратные</a></span>
    </div>
    <div class="add-mobile-nav-row">
        <? if($stockVars = checkTempStockAcitvity() && !empty($stockVars['menuactive'])) { ?>
            <span class="cell <?=$stockVars['class']?>"><a href="<?=$stockVars['link']?>"><span class="text"><span class="<?=$stockVars['icon_class']?>"></span><?=$stockVars['text']?></span></a></span>
        <? } else { ?>
            <span class="cell"><a href="/catalog/stulya/tags/novinki/"><span class="text">Новинки <span class="new">NEW</span></span></a></span>
        <? } ?>
        <span class="cell"><a href="/catalog/stoly/tags/steklyannye/">Стеклянные</a></span>
    </div>
    <div class="add-mobile-nav-row">
        <span class="cell"><a class="text-red" href="/catalog/discounts/">Все скидки %</a></span>
        <span class="cell"><a href="/catalog/stoly/tags/v_stile_eames/">В стиле Eames</a></span>
    </div>
    <div class="add-mobile-nav-row">
        <span class="cell"><a href="/catalog/stulya/">Все стулья</a></span>
        <span class="cell"><a href="/catalog/stoly/">Все столы</a></span>
    </div>
</nav>