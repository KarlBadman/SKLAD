<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("404 Not Found"); ?>

    <div class="ds-wrapper code404-page">
        <div class="code404">
            <h1>Ошибка 404</h1>
            <span>К сожалению, страница, которую вы ищете, не найдена.</span>
            <span>Но это нестрашно! Вы по-прежнему можете найти нужные товары в нашем каталоге:</span>
            <div class="code404__btn">
                <a href="/catalog/" class="ds-btn ds-btn--default">Перейти в каталог</a>
            </div>
        </div>

        <div class="retail-rocket-block">
            <div data-retailrocket-markup-block="5d5ce8e997a52817280bcff3" data-stock-id="4"></div>
        </div>

    </div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>