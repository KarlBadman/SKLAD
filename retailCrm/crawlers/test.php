<?php
    $_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../..';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';        
    require_once __DIR__ . '/vendor/autoload.php';
    
    $filename = __DIR__ . '/1.pdf';

    if ($filename) {
        // var_dump($filename);
        $parser = new \Wrseward\PdfParser\Pdf\PdfToTextParser('pdftotext');
        $parser->parse($filename);
        
        
        $data = explode("\n", $parser->text());
        echo "<pre>" . print_r($data, true);
        die();
        for ($i=0; $i < count($data); $i++) {
            $field = $data[$i];
            if ($field == 'Начислено (USD):') {
                $invoices[$date]["sum"] = $data[$i+2] * 70;
            }
        }
    }