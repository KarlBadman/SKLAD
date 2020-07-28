<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';

use Bitrix\Main\Context;

$obContext = Context::getCurrent();
$obRequest = $obContext->getRequest();

if (!$obRequest->isAjaxRequest()) {
    die;
}
?>
<div class="signup__popup">
    <h2>Нашли дешевле?</h2>
    <p>Получите скидку -5% от цены конкурента<br /><a href="/delivery.php?tab_guarantee">(см. условия)</a></p>
    <form action="" novalidate="novalidate" method="post">
        <input type="hidden" name="component" value="cheaper">
        <input type="hidden" name="id" value="<?= $obRequest->get('id') ?>">
        <div class="field__widget type-block field-name">
            <label for="input-cheaper-name" class="label">Ваше имя:</label>
            <div class="field">
                <div class="input">
                    <input type="text" id="input-cheaper-name" autocomplete="off" name="name"/>
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-email">
            <label for="input-cheaper-phone" class="label">Телефон</label>
            <div class="field">
                <div class="input">
                    <div class="placeholder"></div>
                    <input type="tel" id="input-cheaper-phone" autocomplete="off" name="phone" data-phonemask/>
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-email">
            <label for="input-cheaper-email" class="label">Эл.почта:</label>
            <div class="field">
                <div class="input">
                    <div class="placeholder">name@domain.ru</div>
                    <input type="email" id="input-cheaper-email" autocomplete="off" name="email"/>
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-name">
            <label for="input-cheaper-link" class="label">Ссылка на товар:</label>
            <div class="field">
                <div class="input">
                    <input type="text" id="input-cheaper-link" autocomplete="off" name="link"/>
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-name">
            <label for="input-cheaper-price" class="label">Стоимость товара конкурента (в рублях):</label>
            <div class="field">
                <div class="input">
                    <input type="number" id="input-cheaper-price" autocomplete="off" name="price"/>
                </div>
            </div>
        </div>
        <div class="field__widget type-block field-submit">
            <div class="field">
                <button type="submit" class="button type-blue fill size-41">Отправить</button>
            </div>
        </div>
    </form>
</div>
