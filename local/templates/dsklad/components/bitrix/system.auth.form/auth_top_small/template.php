<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

    <div class="field wrap_socserv_icons">
        <?
        $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "flat",
            array(
                "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
                "AUTH_URL" => $arResult["AUTH_URL"],
                "POST" => $arResult["POST"],
                "POPUP" => "N",
                "SUFFIX" => "form",
            ),
            $component,
            array("HIDE_ICONS" => "Y")
        );
        ?>
    </div>


