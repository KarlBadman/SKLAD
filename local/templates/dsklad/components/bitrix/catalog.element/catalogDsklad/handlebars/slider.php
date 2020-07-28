<div class="ds-double-slider-for-arrows js-double-slider-for-arrows"></div>
<div class="ds-double-slider-for js-double-slider-for">
    {{# each OFFERS_ACTIVE.IMAGES.PICTURE}}
        <div class="ds-double-slider-for__item"><img src="{{src}}" alt=""></div>
    {{/each}}
</div>
{{#iff (objectLength OFFERS_ACTIVE.IMAGES.ICON) '>' 1}}
    <div class="ds-double-slider-nav-arrows js-double-slider-nav-arrows"></div>
    <div class="ds-double-slider-nav js-double-slider-nav">
        {{# each OFFERS_ACTIVE.IMAGES.ICON}}
            <div class="ds-double-slider-nav__item"><img src="{{src}}" alt=""></div>
        {{/each}}
    </div>
{{/iff}}