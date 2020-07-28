<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);

if (!empty($arResult['ERROR'])) {
    ShowError($arResult['ERROR']);
}
?>
<form action="/personal/?tab_settings" method="post">
    <input type="hidden" name="action" value="personal">
    <fieldset>
        <div class="legend">
            <div class="title">О себе</div>
            <label class="label__widget">
                <span class="row">
                    <span class="control">
                        <input
                            type="checkbox"
                            name="legal"
                            value="1"
                            autocomplete="off"
                            data-checked=".switcher-legal"
                            data-unchecked=".switcher-private"
                            class="switcher"
                            <?= ($arResult['USER']['UF_LEGAL']) ? 'checked' : '' ?>>
                        <u class="square">
                            <span class="icon__check">
                                <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use></svg>
                            </span>
                        </u>
                    </span>
                    <span class="label">Юридическое лицо</span>
                </span>
            </label>
        </div>
        <div class="field__widget type-inline">
            <label for="input-about-name" class="label">
                <span class="switcher-legal">Контактное лицо:</span>
                <span class="switcher-private">Ваше имя:</span>
            </label>
            <div class="field">
                <div class="input">
                    <input
                        id="input-about-name"
                        type="text"
                        name="name"
                        placeholder="Ваше имя"
                        autocomplete="off"
                        value="<?= $arResult['USER']['NAME'] ?>">
                </div>
            </div>
        </div>
        <div class="field__widget type-inline field-email">
            <label for="input-about-email" class="label">Электронная почта:</label>
            <div class="field">
                <div class="input">
                    <input
                        id="input-about-email"
                        type="email"
                        name="email"
                        placeholder="Электронная почта"
                        autocomplete="off"
                        data-personal-page="email"
                        value="<?= $arResult['USER']['EMAIL'] ?>">
                </div>
            </div>
        </div>
        <div class="field__widget type-inline">
            <label for="input-about-phone" class="label">Мобильный телефон:</label>
            <div class="field">
                <div class="input">
                    <input
                        id="input-about-phone"
                        type="tel"
                        name="phone"
                        placeholder="+79001234567"
                        autocomplete="off"
                        data-phonemask
                        value="<?= $arResult['USER']['PERSONAL_PHONE'] ?>">
                </div>
            </div>
        </div>
        <div class="field__widget type-inline switcher-legal">
            <label for="input-about-company" class="label">Название компании:</label>
            <div class="field">
                <div class="input">
                    <input
                        id="input-about-company"
                        type="text"
                        name="company"
                        placeholder="Название компании"
                        autocomplete="off"
                        value="<?= $arResult['USER']['WORK_COMPANY'] ?>">
                </div>
            </div>
        </div>
        <div class="field__widget type-inline switcher-legal">
            <label for="input-about-vat" class="label">ИНН:</label>
            <div class="field">
                <div class="input">
                    <input
                        id="input-about-vat"
                        type="text"
                        name="vat"
                        placeholder="000000000000"
                        autocomplete="off"
                        value="<?= $arResult['USER']['UF_INN'] ?>">
                </div>
            </div>
        </div>
        <div class="field__widget type-inline field-subscrible">
            <div class="label"></div>
            <div class="field">
                <label class="label__widget">
                    <span class="row">
                        <span class="control">
                            <input
                                type="checkbox"
                                name="subscrible"
                                value="1"
                                autocomplete="off"
                                data-personal-page="subscrible"
                                <?= ($arResult['USER']['UF_SPAM']) ? 'checked' : '' ?>/>
                            <u class="square">
                                <span class="icon__check">
                                    <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#check"></use></svg>
                                </span>
                            </u>
                        </span>
                        <span class="label">Подписаться на акции магазина</span>
                    </span>
                </label>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <div class="legend">
            <div class="title">Доставка</div>
        </div>
        <div class="field__widget type-inline">
            <label for="input-about-city" class="label">Ваш город:</label>
            <div class="field">
                <div id="input-about-city" placeholder="Выберите город" class="input">
                    <?
                    $APPLICATION->IncludeComponent(
                        'swebs:dpd.cities',
                        '',
                        array(
                            'DPD_HL_ID' => 22,
                            'COMPONENT_TEMPLATE' => '.default'
                        ),
                        false
                    );
                    ?>
                    <?
                    /*
                    <select name="city">
                        <option value="">Санкт-Петербург (автоматически)</option>
                        <option value="">Москва</option>
                    </select>
                    <div class="fallback"><span></span>
                        <div>
                            <span class="icon__darr">
                                <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#darr"></use></svg>
                            </span>
                        </div>
                    </div>
                    */
                    ?>
                </div>
            </div>
        </div>
        <div class="field__widget type-inline">
            <label for="input-about-address" class="label">Адрес:</label>
            <div class="field">
                <?
                /*
                <span class="icon__help">
                    <svg><use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#help"></use></svg>
                </span>
                */
                ?>
                <div class="input">
                    <input
                        id="input-about-address"
                        type="text"
                        name="address"
                        autocomplete="off"
                        value="<?= $arResult['USER']['PERSONAL_STREET'] ?>">
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <div class="field__widget type-inline field-submit">
            <div class="label"></div>
            <div class="field">
                <button data-element="save_personal" class="button type-blue fill size-41">Сохранить</button>
            </div>
        </div>
    </fieldset>
</form>