<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
$APPLICATION->SetPageProperty('keywords', 'купить мебель оптом, стулья и столы по оптовым ценам');
$APPLICATION->SetPageProperty('title', 'Оптовым покупателям — Дизайн Склад dsklad.ru');
$APPLICATION->SetTitle('Оптовым покупателям');
$APPLICATION->AddChainItem('Оптовым покупателям');
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
                ?>
                <div class="title">
                    <h1>Программа лояльности</h1>
                </div>
            </section>
            <section class="text">
                <p>
                    <?
                    $APPLICATION->IncludeFile(
                        SITE_TEMPLATE_PATH . '/include_areas/partnership-title.html',
                        array(),
                        array(
                            'MODE' => 'html'
                        )
                    );
                    ?>
                </p>
            </section>
            <section class="info">
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
                            SITE_TEMPLATE_PATH . '/include_areas/partnership-block1.html',
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
                            SITE_TEMPLATE_PATH . '/include_areas/partnership-block2.html',
                            array(),
                            array(
                                'MODE' => 'html'
                            )
                        );
                        ?>
                    </div>
                </div>
                <div class="block long no_marg">
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
            <? /*
            <section class="form">
                <?
                $APPLICATION->IncludeComponent(
                    'swebs:contact_form',
                    '.default',
                    array(
                        'IBLOCK_TYPE' => 'communication',
                        'IBLOCK_ID' => '19',
                        'EVENT_NAME' => 'PARTNERSHIP',
                        'COMPONENT_TEMPLATE' => '.default'
                    ),
                    false
                );
                ?>
            </section>
            */?>
        </div>
    </div>
</div>
<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>