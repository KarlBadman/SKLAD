<?php
    $_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../..';
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

    echo "<pre>";
    
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
    
    // страница со счетами
    try {
        $crawler = $client->request('GET', 'https://app.convead.io/accounts/16131/billing/invoices');
    } catch (Exception $e) {
        var_dump($e->getMessage());
    }

    function convertDate($date) {
        $months = [
            'Январь'    => '01',
            'Февраль'   => '02',
            'Март'      => '03',
            'Апрель'    => '04',
            'Май'       => '05',
            'Июнь'      => '06',
            'Июль'      => '07',
            'Август'    => '08',
            'Сентябрь'  => '09',
            'Октябрь'   => '10',
            'Ноябрь'    => '11',
            'Декабрь'   => '12',
        ];
        $date = str_replace(',', '', $date);
        $date = explode(' ', $date);
        return implode('-', array($date[2], $months[$date[0]], $date[1]));
    }
    
    $invoiceDates = $crawler->filter('table tr td:nth-child(1)'); // даты счетов
    $invoiceStatuses = $crawler->filter('table tr td:nth-child(2)'); // статусы
    $invoiceLinks = $crawler->filter('table tr td:nth-child(4) a')->links(); // ссылки на счета
    
    $invoices = [];
    for ($i=0; $i < $invoiceDates->count(); $i++) {
        $invoices[$invoiceDates->getIterator()[$i]->textContent] = [
            'date' => convertDate($invoiceDates->getIterator()[$i]->textContent),
            'status' => $invoiceStatuses->getIterator()[$i]->textContent,
            'uri' => $invoiceLinks[$i]->getUri(),
        ];
    }
    
    
    // download invoices
    foreach ($invoices as $date => $invoice) {
        if  ($invoice['status'] == 'оплачено') {
            
            $invoiceFileName = __DIR__ . '/convead_invoices/' . $invoice['date'] . '.pdf';
            $invoices[$date]['filename'] = $invoiceFileName;
            
            if (!file_exists($invoiceFileName)) {
            
                $jar = CookieJar::fromArray([
                                    '_convead_session' => $client->getCookieJar()->get('_convead_session')->getValue()
                                    ], '.convead.io');
            
                try {
                    $res = $guzzle->get(
                        $invoice['uri'],
                        ['cookies' => $jar, 'save_to' => $invoiceFileName]
                    );
                } catch (Exception $e) {
                    var_dump($e->getMessage());
                }
                
            }
        }
    }
    
    foreach ($invoices as $date => $invoice) {
        $filename = $invoice['filename'];
    
        if ($filename) {
            // var_dump($filename);
            $parser = new \Wrseward\PdfParser\Pdf\PdfToTextParser('pdftotext');
            $parser->parse($filename);
            
            
            $data = explode("\n\n", $parser->text());
            for ($i=0; $i < count($data); $i++) {
                $field = $data[$i];
                if ($field == 'Начислено (USD):') {
                    $invoices[$date]["sum"] = $data[$i+1] * 70;
                }
                if (strpos($field, 'Тариф ') === 0) {
                    $invoices[$date]["period"] = $data[$i-1];
                }
            }
        }
    }
    
    // получаем отосланные в CRM расходы
    $sentBills = explode("\n", file_get_contents(__DIR__ . '/convead_invoices/sent.txt'));
    
    foreach ($invoices as $date => $invoice) {
        if  ($invoice['status'] == 'оплачено') {
            
            $date = $invoice['date'];
            $sum = $invoice['sum'];
            $period = $invoice['period'];
            
            if (!in_array($date, $sentBills) && $sum) {
                // отсылаем расход в ритейл
                
                if (!CModule::IncludeModule("intaro.retailcrm")) {
                    file_put_contents(__DIR__ . '/convead_errors.log', sprintf("[%s] ", date('Y-m-d H:i:s')) . "Ошибка: не найден модуль retailcrm" . PHP_EOL, FILE_APPEND);
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
                    'comment' => 'convead. за период: ' . $period,
                    'summ' => $sum,
                    'dateFrom' => $date,
                    'dateTo' => $date,
                    'costItem' => 'services',
                ));
                
                $res = $client->makeRequest(
                    '/costs/create',
                    'POST',
                    $parameters
                );
                
                if ($res->isSuccessful()) {
                    // записываем инфу о текущем платеже в файл
                    $sentBills[] = $date;
                    file_put_contents(__DIR__ . '/convead_invoices/sent.txt', implode("\n", $sentBills));
                    file_put_contents(__DIR__ . '/convead_success.log', sprintf("[%s] ", date('Y-m-d H:i:s')) . print_r($res, true) . PHP_EOL, FILE_APPEND);
                } else {
                    file_put_contents(__DIR__ . '/convead_errors.log', sprintf("[%s] ", date('Y-m-d H:i:s')) . print_r($res, true) . PHP_EOL, FILE_APPEND);
                    die();
                }
                
            }
            
        }
    }
    
    echo 'Ok';