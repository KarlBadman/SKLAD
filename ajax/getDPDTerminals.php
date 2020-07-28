<?php
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
    
    require_once($_SERVER["DOCUMENT_ROOT"] . "/local/components/swebs/order/class.php");
    
    
    $obOrderHelp = new COrderBasket;
	$obOrderHelp->setSourceTerminals('HL');
    
    $dpd = $obOrderHelp->getDPDTerminals();
    
    foreach ($dpd['TERMINAL'] as $terminal) {
        $result[$terminal['terminalCode']] = $terminal['css_class'];
    }
    
    echo json_encode($result);