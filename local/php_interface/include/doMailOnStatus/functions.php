<?php

function doMailOnStatus (&$arFields, &$arTemplate){
    if($arTemplate['EVENT_NAME'] == 'SALE_STATUS_CHANGED_PD' || $arTemplate['EVENT_NAME'] == 'SALE_STATUS_CHANGED_F'){
        if (file_exists($_SERVER['DOCUMENT_ROOT']."/local/php_interface/include/eventTableorm.php"))
            require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/eventTableorm.php");

        $result = \EventTable::getList(array(
            'select'  => array('ID','EVENT_NAME'),
            'filter'  => array('?C_FIELDS'=>$arFields['ORDER_ID'],'EVENT_NAME'=>array('SALE_STATUS_CHANGED_PD','SALE_STATUS_CHANGED_F')),
        ));

        $rows = array();

        while ($row = $result->fetch())
        {
            $rows[$row['EVENT_NAME']][] = $row['ID'];
        }

        if(count($rows[$arTemplate['EVENT_NAME']]) >1){
            \EventTable::delete($rows[$arTemplate['EVENT_NAME'][1]]);
            return false;
        }

    }
}