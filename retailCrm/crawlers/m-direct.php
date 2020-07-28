<?php
    $_SERVER['DOCUMENT_ROOT'] = '/home/bitrix/www';
    
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';        
    require_once __DIR__ . '/../local/libs/vendor/autoload.php';
    
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
        'base_uri' => 'https://m-direct.ru',
        // 'handler' => $stack,
        // 'cookies' => true,
    ]);
    $client = new Client();
    $client->setClient($guzzle);
    
    try {
        $crawler = $client->request('GET', 'https://m-direct.ru/sheet/login');
    } catch (Exception $e) {
        file_put_contents(__DIR__ . '/mdirect_errors.log', sprintf("[%s] ", date('Y-m-d H:i:s')) . $e->getMessage() . PHP_EOL, FILE_APPEND);
    }
    
    try {
        $form = $crawler->filter('form')->form();
        $client->submit($form, array('LoginForm[email]' => 'anton.dsklad@yandex.ru', 'LoginForm[password]' => '123456'));
    } catch (Exception $e) {
        file_put_contents(__DIR__ . '/mdirect_errors.log', sprintf("[%s] ", date('Y-m-d H:i:s')) . $e->getMessage() . PHP_EOL, FILE_APPEND);
    }
    
    try {
        $crawler = $client->request("GET", 'https://m-direct.ru/billing/accounts');
        $form = $crawler->filter('form')->form();
        $formValues = $form->getValues();
    } catch (Exception $e) {
        file_put_contents(__DIR__ . '/mdirect_errors.log', sprintf("[%s] ", date('Y-m-d H:i:s')) . $e->getMessage() . PHP_EOL, FILE_APPEND);
    }
    
    $formParams = [
        'date_begin' => $formValues['AccountsForm[date_begin]'],
        'date_end' => $formValues['AccountsForm[date_end]'],
        '_csrf' => $formValues['_csrf'],
        'draw' => '1',
        'start' => '0',
        'length' => '25',
        'columns[0][data]' => 'num_account',
        'columns[0][name]' => '',
        'columns[0][searchable]' => 'true',
        'columns[0][orderable]' => 'true',
        'columns[0][search][value]' => '',
        'columns[0][search][regex]' => 'false',
        'columns[1][data]' => 'date_time',
        'columns[1][name]' => '',
        'columns[1][searchable]' => 'true',
        'columns[1][orderable]' => 'true',
        'columns[1][search][value]' => '',
        'columns[1][search][regex]' => 'false',
        'columns[2][data]' => 'sum',
        'columns[2][name]' => '',
        'columns[2][searchable]' => 'true',
        'columns[2][orderable]' => 'true',
        'columns[2][search][value]' => '',
        'columns[2][search][regex]' => 'false',
        'columns[3][data]' => 'status',
        'columns[3][name]' => '',
        'columns[3][searchable]' => 'true',
        'columns[3][orderable]' => 'true',
        'columns[3][search][value]' => '',
        'columns[3][search][regex]' => 'false',
        'columns[4][data]' => 'b1',
        'columns[4][name]' => '',
        'columns[4][searchable]' => 'true',
        'columns[4][orderable]' => 'false',
        'columns[4][search][value]' => '',
        'columns[4][search][regex]' => 'false',
        'order[0][column]' => '0',
        'order[0][dir]' => 'desc',
        'search[value]' => '',
        'search[regex]' => 'false'
    ];
    
    // var_dump($client->getCookieJar());
    
    $params = [];
    foreach ($formParams as $key => $value) {
        $params[] = $key . '=' . $value;
    }
    
    $content = implode('&', $params);
    // $content = urlencode(implode('&', $params));
    
    try {
        $crawler = $client->request("POST", 'https://m-direct.ru/billing/accounts-ajax-table', [], [], ['HTTP_CONTENT_TYPE' => 'application/x-www-form-urlencoded'], $content);
        $data = json_decode($crawler->text());
        $data = $data->data;
    } catch (Exception $e) {
        file_put_contents(__DIR__ . '/mdirect_errors.log', sprintf("[%s] ", date('Y-m-d H:i:s')) . $e->getMessage() . PHP_EOL, FILE_APPEND);
    }
    
    // получаем отосланные в CRM расходы
    $sentBills = explode("\n", file_get_contents(__DIR__ . '/mdirect_sentbills.txt'));
    
    foreach ($data as $bill) {
        $date = $bill->date_time;
        $num_account =  $bill->num_account;
        $sum = (float)preg_replace('/\D/', '', $bill->sum) / 100;
        $status = $bill->status;
        
        if ($status == 'ОПЛАЧЕНО') {
            if (!in_array($num_account, $sentBills)) {
                // отсылаем расход в ритейл
                
                if (!CModule::IncludeModule("intaro.retailcrm")) {
                    file_put_contents(__DIR__ . '/mdirect_errors.log', sprintf("[%s] ", date('Y-m-d H:i:s')) . "Ошибка: не найден модуль retailcrm" . PHP_EOL, FILE_APPEND);
                    die();
                }
                
                
                $api_host = COption::GetOptionString("intaro.retailcrm", "api_host");
                $api_key = COption::GetOptionString("intaro.retailcrm", "api_key");
                
                $url = $api_host . '/api/v5';
                $client = new RetailCrmClient($url, array('apiKey' => $api_key));
                
                $parameters = array();
                
                $date = preg_replace('/^(\d+\.\d+\.\d+).*/', '$1', $date);
                
                $parameters['cost'] = json_encode(array(
                    'sites' => array('dsklad-ru'),
                    'summ' => $sum,
                    'comment' => 'm-direct.ru Account Number: ' . $num_account,
                    'dateFrom' => date('Y-m-d', strtotime(str_replace('.', '-', $date))),
                    'dateTo' => date('Y-m-d', strtotime($date)),
                    'costItem' => 'services',
                ));
                
                // var_dump($parameters);
                // die('Stop');

                $res = $client->makeRequest(
                    '/costs/create',
                    'POST',
                    $parameters
                );
                
                if ($res->isSuccessful()) {
                    // записываем инфу о текущем платеже в файл
                    $sentBills[] = $num_account;
                    file_put_contents(__DIR__ . '/mdirect_sentbills.txt', implode("\n", $sentBills));
                    file_put_contents(__DIR__ . '/mdirect_success.log', sprintf("[%s] ", date('Y-m-d H:i:s')) . print_r($res, true) . PHP_EOL, FILE_APPEND);
                } else {
                    file_put_contents(__DIR__ . '/mdirect_errors.log', sprintf("[%s] ", date('Y-m-d H:i:s')) . print_r($res, true) . PHP_EOL, FILE_APPEND);
                    die();
                }

            }
        }
    }
    
    echo 'Done';
    