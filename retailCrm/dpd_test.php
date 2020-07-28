<?php

require_once __DIR__ . '/../Local/php_interface/include/classes/dpd_service.class.php';

$orderId = 74279;

$dpd = new DPD_service_my();

$res = $dpd->getStatesByClientOrder($orderId);

if ($res && count($res['states']) > 0) {
                    
                    $newStates = [];
                    $terminalCode = '';
                    
                    if ($res['states'][0]) {
                        foreach ($res['states'] as $state) {
                            if ($state['transitionTime'] >= $tt) {
                                if ($state['transitionTime'] > $tt) {
                                    $tt = $state['transitionTime'];
                                    $newStates = [$state['newState']];
                                } else {
                                    $newStates[] = $state['newState'];
                                }
                                
                                if ($state['newState'] == 'OnTerminalDelivery')
                                    $terminalCode = $state['terminalCode'];
                            }
                        }
                    } else {
                        $state = $res['states'];
                        if ($state['newState'] == 'OnTerminalDelivery')
                            $terminalCode = $state['terminalCode'];
                        $tt = $state['transitionTime'];
                        $newStates = [$state['newState']];
                    }
}

echo "<pre>";
var_dump($res);