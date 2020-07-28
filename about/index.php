<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Интернет магазин дизайнерских стульев и столов. Культовая мебель для любого интерьера. Условия доставки по России и варианты оплаты. Адреса пунктов самовывоза.");
$APPLICATION->SetPageProperty("title", "О компании — Интернет-магазин дизайнерских стульев и столов «Дизайн Склад» dsklad.ru");
$APPLICATION->SetTitle("О компании");
?>
    <div class="about__page">
        <div class="ds-wrapper">

            <? $APPLICATION->IncludeComponent(
                "bitrix:breadcrumb",
                "template",
                array(
                    "PATH" => "",
                    "SITE_ID" => "s1",
                    "START_FROM" => "0",
                    "COMPONENT_TEMPLATE" => "template"
                ),
                    false
                );
            ?>

            <div class="ds-about">
                <h1>О компании</h1>

                <div class="ds-about__text">
                    <p>Дизайн Склад&nbsp;&mdash; это интернет-магазин дизайнерских стульев, столов и&nbsp;декора.</p>
                    <p>Наш офис в&nbsp;Санкт-Петербурге, а&nbsp;заказы отправляем из&nbsp;Московской области.</p>
                    <img src="<?=SITE_TEMPLATE_PATH?>/images/about-office.jpg" alt="">
                    <p>Чтобы вы&nbsp;получили доступные цены, мы экономим на&nbsp;ненужном: шоу-румы, торговые площади, собственная служба
                            доставки, посредники.</p>
                    <p>Все модели&nbsp;&mdash; это китайское фабричное производство. Мебель изготавливают на
                            промышленном оборудовании по сертификатам соответствия (аналог
                            ГОСТа). Это гарантирует качество изделий: наши стулья выдерживают 140-200&nbsp;кг.</p>
                    <p>Всегда на&nbsp;связи: проконсультируем и&nbsp;решим все вопросы с&nbsp;9&nbsp;до&nbsp;21&nbsp;по&nbsp;мск ежедневно.</p>

                    <h5>Наши достижения:</h5>
                    <ul class="ds-about-info">
                        <li class="ds-about-info__item">Работаем с&nbsp;2014 года, наш рейтинг&nbsp;&mdash; 5&nbsp;звезд на&nbsp;независимых площадках
                                Яндекс Маркета и&nbsp;Гугла;</li>
                        <li class="ds-about-info__item">Доставляем заказы в&nbsp;250 городов России;</li>
                        <li class="ds-about-info__item">Наша мебель участвовала в&nbsp;съемках &laquo;Квартирного вопроса&raquo;, &laquo;Школы ремонта&raquo; и&nbsp;т.д.;</li>
                        <li class="ds-about-info__item">В <a href="https://www.instagram.com/dsklad.ru/" target="_blank">Инстаграме</a> 150&nbsp;000 живых подписчиков;</li>
                        <li class="ds-about-info__item">43&nbsp;000 доставленных заказов.</li>
                    </ul>
                </div>
            </div>


        </div>
    </div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>