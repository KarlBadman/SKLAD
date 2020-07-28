<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
?>
<div class="region-popup">
    <div class="ds-modal__header">
        <h5>Выберите свой регион</h5>
        <span class="icon-svg ic-close ds-modal-close js-ds-modal-close" onclick="purepopup.closePopup();"></span>
    </div>
    <div class="ds-modal__body search">
        <div class="input">
            <?
            $APPLICATION->IncludeComponent(
                'swebs:dpd.cities',
                'header',
                array(
                    'DPD_HL_ID' => 22,
                    'COMPONENT_TEMPLATE' => '.default',
                    'EMPTY' => $_REQUEST['action'] == 'no' ? 'N' : 'Y'
                ),
                false
            );
            ?>
        </div>
        <button class="icon-search">
            <svg>
                <use xmlns:xlink="http://www.w3.org/1999/xlink"
                     xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#search"></use>
            </svg>
        </button>
    </div>
</div>
<script type="text/javascript">
    $('#autocomplete_head').autocomplete({
        serviceUrl: '/ajax/getSuggestation.php?HL_ID=22',
        minChars:3,
        onSelect: function (suggestion) {
            $('input[name="city_id"]#city_id_head').val(suggestion.data);
        }
    });

    // change city
    window.cityValOld = $('#city_id_head').val();
    $('.region-popup').on('focusout', '#autocomplete_head', function () {
        setTimeout(function () {
            var cityValNew = $('#city_id_head').val();
            if (cityValOld != cityValNew) {
                window.cityValOld = cityValNew;
                $('.wrap_container_spinner').show();
                var obj = {};
                obj.intLocationID = cityValNew;
                $.ajax({
                    url: '/local/templates/order/ajax/recity.php',
                    dataType: 'text',
                    data: obj,
                    async: false,
                    type: 'post',
                    success: function (ans) {
                      window.location.reload();
                    }
                });
            }
        }, 300);
    });
</script>