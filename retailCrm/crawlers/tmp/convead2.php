<?php
    $_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../..';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';        
    require_once __DIR__ . '/vendor/autoload.php';
    
    use Goutte\Client;
    use GuzzleHttp\Client as Guzzle;
    use GuzzleHttp\Cookie\CookieJar;
    use RetailCrm\Http\Client as RetailCrmClient;

    /* Logging */
    // use GuzzleHttp\HandlerStack;
    // use GuzzleHttp\Middleware;
    // use GuzzleHttp\MessageFormatter;
    // use Monolog\Logger;
    // use Monolog\Handler\StreamHandler;
    
    // $logger = new Logger('MyLog');
    // $logger->pushHandler(new StreamHandler(__DIR__ . '/test.log'), Logger::DEBUG);
    
    // $stack = HandlerStack::create();
    // $stack->push(
        // Middleware::log(
            // $logger,
            // new MessageFormatter('{method} {uri} HTTP/{version} {req_headers} {req_body}')
        // )
    // );
    
    $guzzle = new Guzzle([
        'base_uri' => 'https://convead.io',
        // 'handler' => $stack,
        // 'cookies' => true,
    ]);
    $client = new Client();
    $client->setClient($guzzle);
    
    try {
        $crawler = $client->request('GET', 'https://convead.io/');
    } catch (Exception $e) {
        var_dump($e->getMessage());
    }
    
    try {
        $form = $crawler->filter('form')->form();
        $client->submit($form, array('user[email]' => 'adv@dsklad.ru', 'user[password]' => 'toptradeco'));
    } catch (Exception $e) {
        var_dump($e->getMessage());
    }
    
    // получаем счёт
    $jar = CookieJar::fromArray([
                                '_convead_session' => $client->getCookieJar()->get('_convead_session')->getValue()
                                ], '.convead.io');
        
    try {
        $res = $guzzle->get('https://app.convead.io/accounts/16131/billings/customer.json', array('cookies' => $jar));
    } catch (Exception $e) {
        var_dump($e->getMessage());
    }
    
    $account = json_decode($res->getBody());
    $balance = (float)$account->balance_with_due;
    
    // предыдущий счёт
    $previous_balance = (float)file_get_contents(__DIR__ . '/convead_balance.txt');
    file_put_contents(__DIR__ . '/convead_log.log', date('Y-m-d H:i:s') . ' --- ' . $balance . PHP_EOL, FILE_APPEND);
    if ($balance > $previous_balance) {
        var_dump("счёт увеличился");
        file_put_contents(__DIR__ . '/convead_balance.txt', $balance);
        // получаем инфу, сколько заплатили
        try {
            $paySummRub = $balance - $previous_balance;
            $payDate = date('Y-m-d');
            
            // получаем инфу о предыдущем платеже
            $previousPay = file_get_contents(__DIR__ . '/convead_pay.txt');
            if ($previousPay) {
                $previousPay = explode("\n", $previousPay);
                $previousPay = array_pop($previousPay);
            }
            $currentPay = $payDate . ';' . $paySummRub;
            
            if ($previousPay == $currentPay) {
                file_put_contents(__DIR__ . '/convead_errors.log', sprintf("[%s] ", date('Y-m-d H:i:s')) . "Не удалось получить сумму платежа: данные о текущем платеже совпадают с предыдущим" . PHP_EOL, FILE_APPEND);
                die();
            }
            
            // добавляем расход в retailcrm
            if (!CModule::IncludeModule("intaro.retailcrm")) {
                file_put_contents(__DIR__ . '/convead_errors.log', sprintf("[%s] ", date('Y-m-d H:i:s')) . "Ошибка: не найден модуль retailcrm" . PHP_EOL, FILE_APPEND);
                die();
            }
            
            
            $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
            $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
            
            $url = $api_host . 'api/v5';
            $client = new RetailCrmClient($url, array('apiKey' => $api_key));
            
            $parameters = array();
            
            file_put_contents(__DIR__ . '/convead_log.log', date('Y-m-d H:i:s') . ' --- Пополнение: ' . $paySummRub . PHP_EOL, FILE_APPEND);
            
            // $parameters['cost'] = array(
                // 'sites' => array('dsklad-ru'),
                // 'summ' => $paySummRub,
                // 'comment' => 'convead.io',
                // 'dateFrom' => date('Y-m-d'),
                // 'dateTo' => date('Y-m-d'),
                // 'costItem' => 'services',
            // );
            
            // $res = $client->makeRequest(
                // '/costs',
                // 'POST',
                // $parameters
            // );
            
            if ($res->isSuccessful()) {
                // записываем инфу о текущем платеже в файл
                file_put_contents(__DIR__ . '/convead_pay.txt', $currentPay, FILE_APPEND);
                file_put_contents(__DIR__ . '/convead_success.log', sprintf("[%s] ", date('Y-m-d H:i:s')) . print_r($res, true) . PHP_EOL, FILE_APPEND);
            } else {
                file_put_contents(__DIR__ . '/convead_errors.log', sprintf("[%s] ", date('Y-m-d H:i:s')) . print_r($res, true) . PHP_EOL, FILE_APPEND);
                die();
            }
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/convead_errors.log', sprintf("[%s] ", date('Y-m-d H:i:s')) . $e->getMessage() . PHP_EOL, FILE_APPEND);
        }
    } else {
        var_dump("счёт уменьшился или остался неизменным");
        file_put_contents(__DIR__ . '/convead_balance.txt', $balance);
    }
    var_dump($balance);
    