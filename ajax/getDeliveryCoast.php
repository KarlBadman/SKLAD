<?
require ($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Context;
use Bitrix\Main\Mail\Event;

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    require_once($_SERVER['DOCUMENT_ROOT'] . '/local/components/swebs/custom_o2k/class.php');

    CModule::IncludeModule('iblock');
    CModule::IncludeModule('sale');
    CModule::IncludeModule('dsklad.site');
    
    $obRequest = Context::getCurrent()->getRequest();

    $arElement = CIBlockElement::GetList(
        false,
        array(
            'ID' => $obRequest->get('productID')
        ),
        false,
        false,
        array('ID', 'XML_ID', 'NAME', 'PROPERTY_RELATED', 'PROPERTY_WITH_THIS')
    )->Fetch();

    $arProductOffers = CCatalogSKU::getOffersList($arElement['ID']);
    if ($arProductOffers) {
        global $arDelivery; global $APPLICATION;
        $arDelivery =  array();

        $arOffer = CIBlockElement::GetByID(current($arProductOffers[$arElement['ID']])['ID'])->Fetch();

        $obOrderHelp = new COrderBasket;
        $obOrderHelp->setSourceTerminals('HL');

        $arOffer['PARENT'] = $arElement['ID'];
        $arOffer['QUANTITY'] = 1;
        if (is_numeric($obRequest->get('quantity'))) {
            $arOffer['QUANTITY'] = $obRequest->get('quantity');
        }

        $arOffer['PACK'] = GetPackData($arOffer['XML_ID']);

        $intLocationID = 0;
        if (!empty($obRequest->get('intLocationID'))) {
            $intLocationID = $obRequest->get('intLocationID');
        }
        $bSelfDelivery = false;
        if ($obRequest->get('door') == 'y') {
            $bSelfDelivery = true;
        }

        if(!empty($arOffer['PACK'])) { // есть весогабариты у позиции
            $arDelivery['~COAST'] = $obOrderHelp->getDPDCoast($arOffer, $bSelfDelivery, $intLocationID);

           // var_dump($arOffer);

            if (!in_array($intLocationID, array(77000000000, 78000000000))) {
                if (!$arDelivery['~COAST']) {
                    $bSelfDelivery = !$bSelfDelivery;
                    $arDelivery['~COAST'] = $obOrderHelp->getDPDCoast($arOffer, $bSelfDelivery, $intLocationID);
                }
                if (!$arDelivery['~COAST']) {
                    $bSelfDelivery = !$bSelfDelivery;
                }
            }

            $arDelivery['COAST'] = number_format($arDelivery['~COAST'], 0, '', ' ') . '.–';

            $arDelivery['DPD'] = \Dsklad\Order::getDPDTerminals([$arOffer]);

            if (in_array($intLocationID, array(77000000000, 78000000000)) &&  $obRequest->get('door') == 'n') { // Костыль для фиксированной стоимости доставки для москвы и питера с учетом терминалов

                if ($intLocationID == 78000000000) {
                    $arDelivery['~COAST'] = $arDelivery['DPD']['TERMINAL'][0]['is_terminal'] == 'Y' ? PICKUP_COAST_SPB : PICKUP_PVZ_COAST_SPB;
                }
                
                if ($intLocationID == 77000000000) {
                    $arDelivery['~COAST'] = $arDelivery['DPD']['TERMINAL'][0]['is_terminal'] == 'Y' ? PICKUP_COAST_MSK : PICKUP_PVZ_COAST_MSK;
                }
                
                $arDelivery['COAST'] = number_format($arDelivery['~COAST'], 0, '', ' ') . '.–';
            }

            if ($arDelivery['DPD']['mapParams']) {
                $arDelivery['DPD']['mapParams']['yandex_lat'] = !is_nan($arDelivery['DPD']['mapParams']['yandex_lat']) ? $arDelivery['DPD']['mapParams']['yandex_lat'] : 0;
                $arDelivery['DPD']['mapParams']['yandex_lon'] = !is_nan($arDelivery['DPD']['mapParams']['yandex_lon']) ? $arDelivery['DPD']['mapParams']['yandex_lon'] : 0;
            }
        } else {
            $exception_type = 'dpd_delivery';
            $exception_entity = 'gabarits_isempty';

            $strSql = 'SELECT id FROM xtra_log WHERE entity_id = ' . $arOffer['ID'] . ' AND exception_type="' . $exception_type . '" AND exception_entity="' . $exception_entity . '"';
            $exist = $DB->Query($strSql, false, $err_mess.__LINE__);
            if(!$exist->Fetch()){
                $arFields = array(
                    "entity_type"             => "'" . 'product' . "'",
                    "entity_id"                    => $arOffer['ID'],
                    "exception_type"                 => "'" . $exception_type . "'",
                    "exception_entity"                  => "'" . $exception_entity . "'"
                );
                $LOG_ID = $DB->Insert("xtra_log", $arFields, $err_mess.__LINE__);

                if(intval($LOG_ID)){
                    $obContext = Context::getCurrent();

                    // goes to email
                    $arFields['ID'] = $LOG_ID;
                    $arFields['COMMENT'] = 'Исключение зафиксировано в getDeliveryCoast. У товара нет габаритов.';
                    $arMailFields = array(
                        'EVENT_NAME' => 'LOGGING',
                        'LID' => $obContext->getSite(),
                        'C_FIELDS' => $arFields
                    );
                    Event::send($arMailFields);
                }
            }
            $arDelivery['~COAST'] = 0;
            $arDelivery['COAST'] = 'Уточните у менеджеров';
            $arDelivery['DPD']['mapParams']['yandex_lat'] = 0;
            $arDelivery['DPD']['mapParams']['yandex_lon'] = 0;
            $arDelivery['DPD']['mapParams']['PLACEMARKS'] = array();
            $arDelivery['DPD']['TERMINAL'] = '';
        }

        $arDelivery['DOOR'] = $bSelfDelivery ? 'y' : 'n';
        $arDelivery['DPD_UNAVAILABLE'] = $APPLICATION->DPDnotAvailable ? 'y' : 'n';
        $arPayment = $obOrderHelp->getPaySystems();
        $arDelivery['PAYMENT'] = array();
        $arPayMap = array(
            2 => 'card2',
            3 => 'wallet',
            4 => 'bank'
        );
        foreach ($arPayment as $i => $arItem) {
            $arDelivery['PAYMENT'][$arItem['SORT']] = array(
                'ID' => $arItem['ID'],
                'NAME' => HTMLToTxt($arItem['~NAME']),
                'CLASS' => $arPayMap[$arItem['ID']],
                'DESCRIPTION' => $arItem['~DESCRIPTION']
            );
        }

        echo json_encode($arDelivery);
    }
}
?>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');?>
