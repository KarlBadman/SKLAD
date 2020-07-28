<?

    if ($_GET['zabbix-get-data'] != '2c16c854f0b6beac7a3c6b24834cce2d') {

        exit;

    } else {

        $xmlRequest = "<?xml version=\"1.0\" encoding=\"utf-8\" ?><request><security><login value=\"dsklad\" /><password value=\"CvcCthdbc\" /></security></request>";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/xml; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CRLF, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
        curl_setopt($ch, CURLOPT_URL, "https://kabinet.b2b-sms.ru/xml/balance.php");
        $result = curl_exec($ch);
        curl_close($ch);

        function object2array($object) { return @json_decode(@json_encode($object), 1); }

        try {

            $xmlResponse = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
            echo floatVal(object2array($xmlResponse)['money']);

        } catch (Exception $e) {

            echo $e->getMessage();

        }

    }

?>
