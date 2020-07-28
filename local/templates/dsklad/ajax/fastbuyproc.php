<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
// ppp($_REQUEST);

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;

Loader::includeModule('swebs.helper');

use Swebs\Helper\Sale\Order as OrderHelper;

$obContext = Context::getCurrent();
$obRequest = $obContext->getRequest();
if (!$obRequest->isAjaxRequest()) {
    echo 'error';
    die;
}
$strName = $obRequest->get('name');
$strPhone = $obRequest->get('phone');
$strEmail = $obRequest->get('email');
// антиспам
if (strlen($strName) > 100 || strip_tags($strName) != $strName || !filter_var($strEmail, FILTER_VALIDATE_EMAIL))
    die();

$strProductId = $obRequest->get('productID');
$strProductCnt = $obRequest->get('productCNT');

$buyOptions['PRODUCT_ID']= $strProductId;
$buyOptions['QUANTITY']= $strProductCnt;
$buyOptions['DELIVERY_ID']= 7;
$buyOptions['PAYMENT_ID']= 3;
$buyOptions['PERSONAL_ID']= 1;

$buyOptions['ORDER_FIELDS']['USER_DESCRIPTION'] = 'Заказ в 1 клик';
$buyOptions['ORDER_PROPERTIES']['F_PHONE'] = $strPhone;
$buyOptions['ORDER_PROPERTIES']['F_NAME'] = $strName;
$buyOptions['ORDER_PROPERTIES']['F_EMAIL'] = $strEmail;
$buyOptions['ORDER_PROPERTIES']['F_CITY'] = $_SESSION['DPD_CITY_NAME'];

/* makcrx: делаем максимум 1 заказ в 2 минуты для одного пользователя */
$buyData = json_encode($buyOptions);
$currentDate = date('Y-m-d h:i:s');

$file = file($_SERVER['DOCUMENT_ROOT'] . '/tmp/last-orders.db', FILE_IGNORE_NEW_LINES);
$rows = array_unique($file);
unset($file);

$newRows = array();
foreach ($rows as $row) {
    $tmp = explode('::', $row);
    $orderData = $tmp[0];
    $created = $tmp[1];
    
    if (strtotime($created) + strtotime('00:05:00') < strtotime($currentDate)) {
        array_push($newRows, $row);
    }
    
    if ($orderData == $buyData) {
        $error = 'Duplicate order';
        continue;
    }
}

array_push($newRows, implode('::', array($buyData, $currentDate)));
file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/tmp/last-orders.db', implode("\n", $newRows));

if ($error) {
    $arResponse = array(
        "status" => "success",
    );

    print \Bitrix\Main\Web\Json::encode($arResponse);
    die();
}


global $USER;
$USER_ID = NULL;

$arResponse = array(
	"status" => "error",
	"fields" => array() // Поля в которых ошибки.
);

if ($USER->IsAuthorized()) {
	$USER_ID = $USER->GetId();
} else {
	$res = UserTable::getList(array('filter' => array('EMAIL' => $strEmail)));

	$arUser = array();
	while ($arRes = $res->fetch()) {
		$arUser = $arRes;
		break;
	}
    
    // makcrx:begin проверка по телефону
    if (empty($arUser)) {
        if ($strPhone != '')
        {
            $arUser = Bitrix\Main\UserTable::getRow(array(
                'filter' => array(
                    "=PERSONAL_PHONE" => $strPhone,
                    "EXTERNAL_AUTH_ID" => ''
                ),
                'select' => array('ID')
            ));
        }
    }
    // makcrx:end

	if (!empty($arUser)) {
		$USER_ID = $arUser["ID"];
	} else {
		if (check_email($strEmail)) {
			$arFields = array(
				"NAME" => $strName,
				// "LAST_NAME" => "",
				"EMAIL" => $strEmail,
				"LOGIN" => $strEmail,
				"LID" => "s1",
				"ACTIVE" => "Y",
				"GROUP_ID" => array(3,4),
				"PERSONAL_PHONE" => $strPhone,
				"PASSWORD" => $pas = randString(),
				"CONFIRM_PASSWORD" => $pas
			);
			$user = new CUser;
			if ($newUserID = $user->Add($arFields)) {
				$USER_ID = $newUserID;
			}
		} else {
			$arResponse["fields"]["js-fastbuy-email"] = "";
		}
	}
}

if ($USER_ID) {
	$res=OrderHelper::byOneClick($USER_ID,$buyOptions);
	$arResponse["status"] = "success";
}

$arResponse["fields"]['idzakaza'] = $res;//id zakaza
$arResponse["fields"]['emailkli'] = $strEmail;//id zakaza
$arResponse["fields"]['idtovara'] = $strProductId;//id tovara

$APPLICATION->RestartBuffer();
print \Bitrix\Main\Web\Json::encode($arResponse);
// ppp($res);
?>