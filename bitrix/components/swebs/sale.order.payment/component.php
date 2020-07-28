<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("sale")) {
    ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
    return;
}

global $APPLICATION;

//$APPLICATION->RestartBuffer();

if (!empty($_REQUEST['ORDER_ID'])) {
    $ORDER_ID = $_REQUEST['ORDER_ID'];
} else {
    $ORDER_ID = $_SESSION['ORDER_ID'];
}

$arOrder = CSaleOrder::GetById($ORDER_ID);

if ($arOrder) {
    $dbPaySysAction = CSalePaySystemAction::GetList(
        array(),
        array(
            "PAY_SYSTEM_ID" => $arOrder["PAY_SYSTEM_ID"],
            "PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"]
        ),
        false,
        false,
        array("ACTION_FILE", "PARAMS", "ENCODING")
    );

    if ($arPaySysAction = $dbPaySysAction->Fetch()) {
        if (strlen($arPaySysAction["ACTION_FILE"]) > 0) {
            CSalePaySystemAction::InitParamArrays($arOrder, $ID, $arPaySysAction["PARAMS"]);

            $pathToAction = $_SERVER["DOCUMENT_ROOT"] . $arPaySysAction["ACTION_FILE"];
            $pathToAction = rtrim(str_replace("\\", "/", $pathToAction), "/");

            try {
                if (file_exists($pathToAction)) {
                    if (is_dir($pathToAction)) {
                        if (file_exists($pathToAction . "/payment.php"))
                            include($pathToAction . "/payment.php");
                    } else {
                        include($pathToAction);
                    }
                }
            } catch (\Bitrix\Main\SystemException $e) {
                if ($e->getCode() == CSalePaySystemAction::GET_PARAM_VALUE)
                    $message = GetMessage("SOA_TEMPL_ORDER_PS_ERROR");
                else
                    $message = $e->getMessage();

                ShowError($message);
            }

            if (strlen($arPaySysAction["ENCODING"]) > 0) {
                define("BX_SALE_ENCODING", $arPaySysAction["ENCODING"]);
                AddEventHandler("main", "OnEndBufferContent", "ChangeEncoding");
                function ChangeEncoding($content)
                {
                    global $APPLICATION;
                    header("Content-Type: text/html; charset=" . BX_SALE_ENCODING);
                    $content = $APPLICATION->ConvertCharset($content, SITE_CHARSET, BX_SALE_ENCODING);
                    $content = str_replace("charset=" . SITE_CHARSET, "charset=" . BX_SALE_ENCODING, $content);
                }
            }

        }
    }
}
?>