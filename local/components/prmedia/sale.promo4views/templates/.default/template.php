<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $templateFile
 * @var string $templateFolder
 * @var CBitrixComponent $component
 * @var string $componentPath
 */

$this->setFrameMode(true);
?>

<div class="p4v__popup">
    <div class="title">Мы рады, что Вы выбрали нас. Мы ценим Ваше время и дарим скидку</div>
    <div class="sub-title">Ваш промокод на единовременную скидку 5%</div>
    <div class="description">Вы можете использовать этот промокод прямо сейчас (скопируйте и введите его на странице оформления заказа)</div>
    <div class="promocode">RETARGET5</div>
    <div class="hidden-s">
        <?
        $APPLICATION->IncludeFile(
            SITE_TEMPLATE_PATH . '/include_areas/order-block3.php',
            array(),
            array(
                'MODE' => 'php'
            )
        );
        ?>
    </div>
</div>
