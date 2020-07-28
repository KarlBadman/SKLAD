<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Event;

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

Loader::includeModule('iblock');

$IBLOCK_ID = 40; // ИБ Предзаказ

$arStatus = array(
	'status' => false
);

$arResult = array(
    'FIELDS' => array(
        'NAME' => 'name',
        'PHONE' => 'phone',
        'EMAIL' => 'email'
    )
);

$obContext = Context::getCurrent();
$obRequest = $obContext->getRequest();
$strName = $obRequest->get($arResult['FIELDS']['NAME']);
$strPhone = $obRequest->get($arResult['FIELDS']['PHONE']);
$strEmail = $obRequest->get($arResult['FIELDS']['EMAIL']);
$strComponent = $obRequest->get('component');
$strTemplate = $obRequest->get('template');
$goodID = $obRequest->get('good-id');
$gRecaptchaResponse=$obRequest->get('g-recaptcha-response');

//__($strName);
//__($strPhone);
//__($strComponent);
//__($strTemplate);
//__($goodID);
//__($this->__templateName);
$success=true;

$secret = '6LfhxiEUAAAAAIWJ7Jf3ffZcg-Hp4cd1O_8r_PNi';
include($_SERVER["DOCUMENT_ROOT"].'/bitrix/local/php_lib/autoload.php');
$recaptcha = new \ReCaptcha\ReCaptcha($secret);
$ReCaptchaRes = $recaptcha->verify($gRecaptchaResponse, $_SERVER['REMOTE_ADDR']);
if($strTemplate!='preorder') {
    $success = $ReCaptchaRes->isSuccess();
}

if ($strComponent == 'callback' && $strTemplate == 'preorder' && !empty($strPhone)  and $success && !empty($strEmail)) {
//    __("ITS oK");

//slanes::begin
//send order to retailCrm
	CModule::IncludeModule("intaro.retailcrm");
	$api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
	$api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
	// $api_key = 'QN7aRE1zAqv9ARcNhsWnzRMQQuWq8MOP';
	$client = new \RetailCrm\ApiClient($api_host, $api_key);
	
	global $USER;
	
	$orderFields = array(
		'firstName' => $strName,
		'orderMethod' => 'supply-request',
		'phone' => $strPhone,
		'email' => $strEmail,
		'items' => array(
			array(
				'offer' => array(
					'quantity' => 1,
					'externalId' => intval($goodID)
				)
			)
		),
		'customFields' => array(
			'roistat' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : '',
            'no_upload' => true
		)
	);
	if ($USER->IsAuthorized()) {
		$orderFields['customer']['externalId'] = $USER->GetID();
	} else {
		if (check_email($strEmail)) {
			$rsUsers = CUser::GetList(($by="id"), ($order="desc"), array("EMAIL" => $strEmail));
			if($rsUsers->NavNext(true, "f_"))
				$orderFields['customer']['externalId'] = $f_ID;
		}
        
        if (!$orderFields['customer']['externalId']) {
            if ($strPhone != '') {
                $rsUsers = CUser::GetList(($by="id"), ($order="desc"), array("PERSONAL_PHONE" => $strPhone));
                if($rsUsers->NavNext(true, "f_"))
                    $orderFields['customer']['externalId'] = $f_ID;
            }
        }
	}

	$response = $client->ordersCreate($orderFields);

    // makcrx: begin
    // передаём в GA событие order:success
    if ($response->isSuccessful()) {
        ob_start();

        $orderId = (string)$response->id . 'A';
        Makcrx::sendOrderSuccessEvent2ua($orderId);

        ob_end_clean();
    }
    // makcrx: end

//slanes::end

    $strFieldName = (!empty($strName) ? $strName : 'Без имени').' (' . $strPhone . ')';

    $good = array();
    if(intval($goodID)>0){
        $arSelect = Array("ID","NAME","PROPERTY_CML2_ARTICLE","PROPERTY_KOD_TSVETA","PROPERTY_CML2_LINK");
        $arFilter = Array("IBLOCK_ID"=>IntVal(36), "ID"=>intval($goodID));
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        if($arFields = $res->GetNext())
        {
            $good['NAME']=$arFields['NAME'];
            $good['PARENT_ID']=$arFields['PROPERTY_CML2_LINK_VALUE'];
            $good['COLOR']=$arFields['PROPERTY_KOD_TSVETA_VALUE'];
            $good['COLOR'] = explode('#',$good['COLOR'])[0];
            $arSelect = Array("ID","NAME","PROPERTY_CML2_ARTICLE");
            $arFilter = Array("IBLOCK_ID"=>IntVal(35), "ID"=>intval($good['PARENT_ID']));
            $resParent = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            if($arFieldsParent = $resParent->GetNext()){
                $good['NAME'] = $arFieldsParent['NAME'];
                $good['ART'] = $arFieldsParent['PROPERTY_CML2_ARTICLE_VALUE'];
            }
        }

    }

    $obElement = new CIBlockElement;
    $arFields = array(
        'IBLOCK_ID' => $IBLOCK_ID,
        'NAME' => $strFieldName
    );
    $detail_text='';
    if(count($good)){
        $detail_text = str_replace(
            array('#NAME#','#COLOR#','#ART#'),
            array($good['NAME'], $good['COLOR'], $good['ART']),
            'Модель: #NAME#, Цвет: #COLOR#, Артикул: #ART#'
        );
        $arFields['DETAIL_TEXT'] = $detail_text;
    }

    if($strTemplate!='preorder' or ($strTemplate=='preorder' and count($good))) {
        if ($obResult = $obElement->Add($arFields)) {
			$arStatus['status'] = true;
		}
    }

    // goes to email
    $arMailFields = array(
        'EVENT_NAME' => 'PREORDER',
        'LID' => $obContext->getSite(),
        'C_FIELDS' => array(
            'NAME' => $strName,
            'PHONE' => $strPhone,
            "GOOD_INFO" =>$detail_text
        )
    );
    if($strTemplate!='preorder' or ($strTemplate=='preorder' and count($good))) {
        Event::send($arMailFields);
    }
}

echo json_encode($arStatus);

}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>