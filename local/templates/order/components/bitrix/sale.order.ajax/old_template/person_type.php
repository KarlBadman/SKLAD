<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
    <?if($arResult["USER_VALS"]["PERSON_TYPE_ID"] == 1):?>
        <label class="label_ur" for="PERSON_TYPE_2">
            <span class="row">
                <span class="control">
                    <input class="switcher" type="radio" id="PERSON_TYPE_2" name="PERSON_TYPE" value="2" onClick="submitForm()">
                    <u class="square">
                        <span class="icon__check">
                            <svg><use xlink:href="/local/templates/dsklad/images/sprite.svg#check"></use></svg>
                        </span>
                    </u>
                </span>
                <span class="label">Юридическое лицо</span>
            </span>
        </label>
    <?endif;?>
