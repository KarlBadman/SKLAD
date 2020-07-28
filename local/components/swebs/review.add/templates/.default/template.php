<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);

global $USER;
?>

<div class="review__popup">
    <h2>Мой отзыв</h2>
    <span class="icon-svg ic-close ds-modal-close js-ds-modal-close" onclick="purepopup.closePopup();"></span>
    <p><?= $arResult['NAME'] ?></p>
    <form action="#" novalidate="novalidate" method="post" enctype="multipart/form-data" id="reviewsForm">
        <div class="field__widget type-inline field-text">
            <div class="input">
                <textarea name="text" rows="5" autocomplete="off" id="reviewsText" placeholder="Текст отзыва"><?= $_POST['text'] ?></textarea>
            </div>
        </div>
        <div class="d-flex">
            <div class="field__widget type-inline field-name">
                <div class="input">
                    <input type="text" id="input-review-name" autocomplete="off" name="name" placeholder="Как Вас зовут" value="<?=$USER->getFirstName()?>" <? if(!empty($USER->getFirstName())){ echo 'disabled';}?>/>
                </div>
            </div>
            <? if ($USER->IsAdmin()) { ?>
                <input type="hidden" value="admin" id="isadmin">
            <? } ?>
            <div class="field__widget type-inline field-order">
                <div class="input">
                    <input type="text" placeholder="Номер заказа" id="input-review-order" autocomplete="off" name="orderid"/>
                </div>
            </div>
        </div>
        <div class="d-flex">
            <div class="col-6">
                <div class="field__widget type-inline field-rate">
                    <label for="input-review-rating" class="label">Дайте оценку</label>
                    <div class="field">
                        <div class="stars">
                            <input id="rating-1" type="radio" name="rating" value="1"/>
                            <input id="rating-2" type="radio" name="rating" value="2"/>
                            <input id="rating-3" type="radio" name="rating" value="3"/>
                            <input id="rating-4" type="radio" name="rating" value="4"/>
                            <input id="rating-5" type="radio" name="rating" value="5"/>
                            <label for="rating-1">
                                <span class="icon__star-disabled2">
                                    <svg>
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#star-disabled2"></use>
                                    </svg>
                                </span>
                            </label>
                            <label for="rating-2">
                                <span class="icon__star-disabled2">
                                    <svg>
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#star-disabled2"></use>
                                    </svg>
                                </span>
                            </label>
                            <label for="rating-3">
                                <span class="icon__star-disabled2">
                                    <svg>
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#star-disabled2"></use>
                                </svg>
                                </span>
                            </label>
                            <label for="rating-4">
                                <span class="icon__star-disabled2">
                                    <svg>
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#star-disabled2"></use>
                                    </svg>
                                </span>
                            </label>
                            <label for="rating-5">
                                <span class="icon__star-disabled2">
                                    <svg>
                                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#star-disabled2"></use>
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="field__widget type-inline field-file">
                    <label class="label"></label>
                    <div class="button type-blue size-41">
                        <input type="file" name="file[]" autocomplete="off" multiple accept="image/*" id="reviewsFile"/> Загрузить файл
                    </div>
                </div>
            </div>
            <?
            if (!$USER->IsAdmin()) {
                ?>
                <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
                <div id="reviewsRecaptch"></div>
                <?
            }
            ?>
            <input type="hidden" name="shipment" value="<?= $arParams['ELEMENT_ID'] ?>">
            <input type="hidden" name="type" value="review">
        </div>
        <div class="field__widget type-inline field-submit">
            <label class="label"></label>
            <button type="button" class="button type-blue fill size-41 review_submit">Отправить</button>
            <a href="javascript:purepopup.closePopup();" class="cancel">Отменить</a>
        </div>
    </form>
</div>