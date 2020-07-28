<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!empty($arResult['REVIEWS']['result']['reviews'])) { ?>
<div class="ds-catalog-detail__opinions device-border">
    <div class="ds-reviews">
        <div class="ds-reviews__header">
            <div class="ds-reviews__left">
                <h4>Отзывы<span class="counter"><?=$arResult['REVIEWS']['result']['user_ratings_total']?></span></h4>
            </div>
            <div class="ds-reviews__right"><a class="ds-btn ds-btn--default" href="https://search.google.com/local/writereview?placeid=<?=$arParams['PLACE_ID']?>" target="_blank">написать отзыв</a></div>
        </div>

        <div class="ds-reviews__list js-expanded">
            <? foreach($arResult['REVIEWS']['result']['reviews'] as $review) {
                if ((int)$review['rating'] < 4)
                    continue; ?>
            <div class="ds-reviews__item">
                <div class="ds-reviews__person">
                    <div class="ds-reviews__img"><img src="<?=$review['profile_photo_url']?>" alt="<?=$review['author_name']?>"></div>
                    <div class="ds-reviews__name"><strong><?=$review['author_name']?></strong></div>
                </div>
                <div class="ds-reviews__rating">
                    <div class="ds-rating-stars" data-rate="<?=$review['rating']?>"><span></span><span></span><span></span><span></span><span></span></div>
                </div>
                <div class="ds-reviews__text">
                    <div class="ds-reviews__text-item">
                        <p><strong>Комментарий:</strong></p>
                        <p><?=$review['text']?></p>
                    </div>
                </div>
                <div class="ds-reviews__geo"><span><?=$review['relative_time_description']?></span></div>
            </div>
            <? }?>
            <a href="https://search.google.com/local/reviews?placeid=<?=$arParams['PLACE_ID']?>" target="_blank" class="ds-btn ds-btn--light">показать ещё</a>
        </div>
        <div class="ds-btn-more">
            <button class="ds-btn ds-btn--light ds-btn--full js-btn-more">Ещё отзывы</button>
        </div>
    </div>
</div>
<? } ?>