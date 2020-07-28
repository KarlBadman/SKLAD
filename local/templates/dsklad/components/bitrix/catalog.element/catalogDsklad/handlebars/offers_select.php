{{#each PROPERTY_SELECT}}
    <div class="good-info__item {{#iff TYPE '==' IMAGES}}good-info--color{{/iff}} {{#iff ONE '!=' true}}good-info--border{{/iff}}" data-prop-id="{{ID}}">
        <div class="good-info__title" data-prop-id="{{ID}}">{{#replace "Код цвета" "Цвет"}}{{NAME}}{{/replace}}:&nbsp;<span>{{CHECKED_VALUE}}</span></div>
        {{#iff TYPE '==' 'IMAGES'}}
            <div class="goods-color js-color">
                {{#each VALUES}}
                    {{#iff HIDDEN '!=' 'Y'}}
                        <div class="goods-color__item js-offers {{#iff CHECKED '==' 'Y'}}active{{/iff}}" data-prop-id="{{../ID}}" data-prop-code="{{CODE}}" data-value="{{VALUE}}" data-position="{{../POSITION}}"><img src="{{SRC}}" alt=""></div>
                    {{/iff}}
                {{/each}}
            </div>
        {{else}}
            <div class="goods-size js-size">
                {{#each VALUES}}
                    {{#iff HIDDEN '!=' 'Y'}}
                        <span class="goods-size__item js-offers  {{#iff CHECKED '==' 'Y'}}active{{/iff}}" data-prop-id="{{../ID}}" data-prop-code="{{CODE}}" data-value="{{VALUE}}" data-position="{{../POSITION}}">{{VALUE}}</span>
                    {{/iff}}
                {{/each}}
            </div>
        {{/iff}}
    </div>
{{/each}}
