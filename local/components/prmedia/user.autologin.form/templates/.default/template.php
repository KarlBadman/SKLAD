<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->setFrameMode(true);
?>

<div class="item__page">
    <div class="ds-wrapper default">
        <section class="section-login">
            <h2><?= $APPLICATION->ShowTitle(false) ?></h2>

            <?if(!$arResult['AUTHORIZED']):?>

                <form novalidate="" method="POST">
                    <div class="fields-block clearfix">
                        <div class="field__widget">
                            <div class="field">
                                <div class="login_page_input">
                                    <input required="" data-name="is_phone" class="inp-big" type="tel" id="tel" value="7" name="ORDER_PROP_<?=$arResult['CUSTOM_PROPS']['PHONE']['ID']?>"
                                           placeholder="Номер телефона"
                                           value="<?=$arResult['CUSTOM_PROPS']['PHONE']['VALUE']?>"
                                           data-parsley-error-message="Сначала введите телефон"
                                           data-parsley-errors-container="#phone-number">
                                </div>
                            </div>
                        </div>
                        <div class="field__widget field-submit">
                            <div class="field">
                                <button type="submit" data-count="2" class="button type-blue fill" data-name="phoneButton">Получить код</button>
                            </div>
                        </div>
                    </div>
                </form>

                    <p>Вам будет отправлен код подтверждения</p>

                <?include($_SERVER["DOCUMENT_ROOT"] . "/local/templates/dsklad/ajax/phone_modal.php");?>
            <?endif;?>

            <p>Укажите свой номер телефона и мы вышлем вам сообщение с кодом доступа к личной странице.</p>

            <span class="agreement_field">
                <span class="row">
                    <span class="label">
                        <p>Нажимая на кнопку "получить код", Вы соглашаетесь с условием
                            <a class="border-link" href="/public_offer/">публичной оферты</a>
                            и даете согласие на обработку своих
                            <a class="border-link" href="/public_offer/">персональных данных</a>.
                        </p>
                    </span>
                </span>
            </span>
            <div id="error_agr">Для продолжения вы должны
                согласиться на обработку
                персональных данных
            </div>
            <?
            //Пустой div для вереноса строки после отправки данных
            ?>
            <div></div>
            <div class="loader-block">
                <img src="<?= $templateFolder ?>/images/loader.svg" alt=""/>
            </div>
            <div class="ready-block">
                <img class="ico-mail" src="<?= SITE_TEMPLATE_PATH ?>/images/ico-mail.svg" alt=""/>
                На почту <strong></strong> была отправлена ссылка.
            </div>
            <div class="error-block">
                <div class="title">Временные неполадки в работе сервиса!</div>
                <div class="text">Наши специалисты уже работают над этой проблемой.</div>
                <div class="decision-text">Пожалуйста, вернитесь немного позже.</div>
            </div>
        </section>
    </div>
</div>