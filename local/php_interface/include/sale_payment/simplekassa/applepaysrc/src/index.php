<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

    define('PRODUCTION_CERTIFICATE_KEY', './ApplePay.key.pem');
    define('PRODUCTION_CERTIFICATE_PATH', './ApplePay.crt.pem');
    define('PRODUCTION_CERTIFICATE_KEY_PASS', '7bn8vrt'); 
    define('PRODUCTION_MERCHANTIDENTIFIER', openssl_x509_parse(file_get_contents(PRODUCTION_CERTIFICATE_PATH))['subject']['UID']);
    define('PRODUCTION_DOMAINNAME', 'www.dsklad.ru'); 
    define('PRODUCTION_CURRENCYCODE', 'RUB');
    define('PRODUCTION_COUNTRYCODE', 'RU');
    define('PRODUCTION_DISPLAYNAME', 'TopTrade');
    define('DEBUG', 'true');

    $validation_url = $_GET['validationURL'];
    if("https" == parse_url($validation_url, PHP_URL_SCHEME) && substr(parse_url($validation_url, PHP_URL_HOST), -10)  == ".apple.com") {
	
    	$ch = curl_init();
    	$data = '{"merchantIdentifier":"'.PRODUCTION_MERCHANTIDENTIFIER.'", "domainName":"'.PRODUCTION_DOMAINNAME.'", "displayName":"'.PRODUCTION_DISPLAYNAME.'"}';
    	curl_setopt($ch, CURLOPT_URL, $validation_url);
    	curl_setopt($ch, CURLOPT_SSLCERT, PRODUCTION_CERTIFICATE_PATH);
    	curl_setopt($ch, CURLOPT_SSLKEY, PRODUCTION_CERTIFICATE_KEY);
    	curl_setopt($ch, CURLOPT_SSLKEYPASSWD, PRODUCTION_CERTIFICATE_KEY_PASS);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    	
    	if(curl_exec($ch) === false) {
    		echo '{"curlError":"' . curl_error($ch) . '"}';
    	}
    	curl_close($ch);
    }
