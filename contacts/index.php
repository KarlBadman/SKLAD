<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
$APPLICATION->SetPageProperty('description', 'Интернет магазин дизайнерских стульев и столов. Культовая мебель для любого интерьера. Условия доставки по России и варианты оплаты. Адреса пунктов самовывоза.');
$APPLICATION->SetPageProperty('title', 'Контакты — Интернет-магазин дизайнерских стульев и столов Дизайн Склад dsklad.ru');
$APPLICATION->SetTitle('Контакты');
$APPLICATION->AddViewContent('page_type', 'data-page-type="other-page"');

?>
<div class="contacts__page">
    <div class="ds-wrapper default">
        <div class="contents">
            <section class="heading">
                <?
                $APPLICATION->IncludeComponent(
                    'bitrix:breadcrumb',
                    'template',
                    array(
                        'PATH' => '',
                        'SITE_ID' => 's1',
                        'START_FROM' => '0',
                        'COMPONENT_TEMPLATE' => 'template'
                    ),
                    false
                );
                $APPLICATION->AddChainItem('Контакты', $_SERVER['REQUEST_URI']);
                ?>
                <div class="title">
                    <h1>Контакты</h1>
                </div>
            </section>
            <section class="text hidden-lte-m">
                <p>Свяжитесь с нами удобным для вас способом:</p>
            </section>
            <section class="info vcard">
                <img class="hidden photo" alt="Дизайн Склад" src="<?= SITE_TEMPLATE_PATH ?>/images/logo_vcard.png" />
                <div class="block">
                    <div class="icon">
                        <span class="icon__about-call">
                            <svg>
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-call"></use>
                            </svg>
                        </span>
                    </div>
                    <div class="block-content">
                        <?
                        $APPLICATION->IncludeFile(
                            SITE_TEMPLATE_PATH . '/include_areas/contacts-block1.html',
                            array(),
                            array(
                                'MODE' => 'html'
                            )
                        );
                        ?>
                    </div>
                </div>
                <div class="block">
                    <div class="icon">
                        <span class="icon__about-email">
                            <svg>
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-email"></use>
                            </svg>
                        </span>
                    </div>
                    <div class="block-content">
                        <?
                        $APPLICATION->IncludeFile(
                            SITE_TEMPLATE_PATH . '/include_areas/contacts-block2.html',
                            array(),
                            array(
                                'MODE' => 'html'
                            )
                        );
                        ?>
                    </div>
                </div>
                <div class="block">
                    <div class="icon">
                        <span class="icon__about-partner">
                            <svg>
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-partner"></use>
                            </svg>
                        </span>
                    </div>
                    <div class="block-content">
                        <?
                        $APPLICATION->IncludeFile(
                            SITE_TEMPLATE_PATH . '/include_areas/contacts-block5.html',
                            array(),
                            array(
                                'MODE' => 'html'
                            )
                        );
                        ?>
                    </div>
                </div>
                <div class="block long">
                    <div class="icon">
                        <span class="icon__about-details">
                            <svg>
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-email"></use>
                            </svg>
                        </span>
                    </div>
                    <div class="block-content">
                        <?
                        $APPLICATION->IncludeFile(
                            SITE_TEMPLATE_PATH . '/include_areas/contacts-block8.html',
                            array(),
                            array(
                                'MODE' => 'html'
                            )
                        );
                        ?>
                    </div>
                </div>
                <div class="block long">
                    <div class="icon">
                        <span class="icon__about-details">
                            <svg>
                                <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#about-details"></use>
                            </svg>
                        </span>
                    </div>
                    <div class="block-content">
                        <?
                        $APPLICATION->IncludeFile(
                            SITE_TEMPLATE_PATH . '/include_areas/contacts-block7.html',
                            array(),
                            array(
                                'MODE' => 'html'
                            )
                        );
                        ?>
                    </div>
                </div>
            </section>
            <?
            /*
            <section class="form">
                <? $APPLICATION->IncludeComponent(
                    "swebs:contact_form",
                    ".default",
                    array(
                        "IBLOCK_TYPE" => "communication",
                        "IBLOCK_ID" => "18",
                        "EVENT_NAME" => "CONTACT",
                        "COMPONENT_TEMPLATE" => ".default"
                    ),
                    false
                ); ?>
            </section>
            */
            ?>
        </div>
    </div>
</div>
<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>
