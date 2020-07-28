<?
    $hash = trim(strip_tags($_GET['hash']));
	$orderIdCrm = abs((int)$_GET['orderId']);
	$phone = $_GET['phone'];

	if ('XLHbLjFXH2trQx' != $hash || 0 == $orderIdCrm || !$phone) return;
    if (preg_match('/^\7 \d{3} \d{3}-\d{2}-\d{2}$/', $phone) === 1) return;
    if (preg_match('/^\d{3} \d{2} \d{3}-\d{2}-\d{2}$/', $phone) === 1) return;

    function format_phone($phone = '', $trim = true)
{
        // If we have not entered a phone number just return empty
        if (empty($phone)) {
            return '';
        }

        // Strip out any extra characters that we do not need only keep letters and numbers
        $phone = preg_replace("/[^0-9A-Za-z]/", "", $phone);
     
            
        // If we have a number longer than 11 digits cut the string down to only 11
        // This is also only ran if we want to limit only to 11 characters
        if ($trim == true && strlen($phone)>11) {
            $phone = substr($phone,  0, 11);
        }

        // Perform phone number formatting here
        if (strlen($phone) == 7) {
            return preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "$1-$2", $phone);
        } elseif (strlen($phone) == 10) {
            return preg_replace("/([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{2})([0-9a-zA-Z]{2})/", "7 $1 $2-$3-$4", $phone);
        } elseif (strlen($phone) == 11) {
            return preg_replace("/([0-9a-zA-Z]{1})([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{2})([0-9a-zA-Z]{2})/", "7 $2 $3-$4-$5", $phone);
        } elseif (strlen($phone) == 12) {
            return preg_replace("/^(\d{3})(\d{2})(\d{3})(\d{2})(\d{2})$/", "$1 $2 $3-$4-$5", $phone);
        }
     
        // Return original phone if not 7, 10 or 11 digits long
        return $phone;
    }

    $phone = format_phone($phone, false);


	require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
	
	
	
	if (!CModule::IncludeModule("intaro.retailcrm")) return 0;
	

	$api_by = 'id';
    $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
    $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
    $client = new \RetailCrm\ApiClient($api_host, $api_key);
    
    $order = array(
        'id' => $orderIdCrm,
        'phone' => $phone
    );
    
    $response = $client->ordersEdit($order, $api_by);
    