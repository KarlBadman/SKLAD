<?php
IncludeModuleLangFile(__FILE__);
class CustomRetailCrmOrder
{
    public static $MODULE_ID = 'intaro.retailcrm';
    public static $CRM_API_HOST_OPTION = 'api_host';
    public static $CRM_API_KEY_OPTION = 'api_key';
    public static $CRM_ORDER_TYPES_ARR = 'order_types_arr';
    public static $CRM_DELIVERY_TYPES_ARR = 'deliv_types_arr';
    public static $CRM_PAYMENT_TYPES = 'pay_types_arr';
    public static $CRM_PAYMENT_STATUSES = 'pay_statuses_arr';
    public static $CRM_PAYMENT = 'payment_arr'; //order payment Y/N
    public static $CRM_ORDER_LAST_ID = 'order_last_id';
    public static $CRM_SITES_LIST = 'sites_list';
    public static $CRM_ORDER_PROPS = 'order_props';
    public static $CRM_LEGAL_DETAILS = 'legal_details';
    public static $CRM_CUSTOM_FIELDS = 'custom_fields';
    public static $CRM_CONTRAGENT_TYPE = 'contragent_type';
    public static $CRM_ORDER_FAILED_IDS = 'order_failed_ids';
    public static $CRM_ORDER_HISTORY_DATE = 'order_history_date';
    public static $CRM_CATALOG_BASE_PRICE = 'catalog_base_price';
    public static $CRM_ORDER_NUMBERS = 'order_numbers';

    const CANCEL_PROPERTY_CODE = 'INTAROCRM_IS_CANCELED';

    /**
     * Mass order uploading, without repeating; always returns true, but writes error log
     * @param $pSize
     * @param $failed -- flag to export failed orders
     * @return boolean
     */
    public static function uploadOrder($orderId, $failed = false, $orderList = false)
    {
        if (!CModule::IncludeModule("iblock")) {
            RCrmActions::eventLog('RetailCrmOrder::uploadOrders', 'iblock', 'module not found');
            return true;
        }
        if (!CModule::IncludeModule("sale")) {
            RCrmActions::eventLog('RetailCrmOrder::uploadOrders', 'sale', 'module not found');
            return true;
        }
        if (!CModule::IncludeModule("catalog")) {
            RCrmActions::eventLog('RetailCrmOrder::uploadOrders', 'catalog', 'module not found');
            return true;
        }

        $resOrders = array();
        $resCustomers = array();
        $orderIds = array();

        $lastUpOrderId = COption::GetOptionString(self::$MODULE_ID, self::$CRM_ORDER_LAST_ID, 0);
        $failedIds = unserialize(COption::GetOptionString(self::$MODULE_ID, self::$CRM_ORDER_FAILED_IDS, 0));

        if ($failed == true && $failedIds !== false && count($failedIds) > 0) {
            $orderIds = $failedIds;
        } elseif ($orderList !== false && count($orderList) > 0) {
            $orderIds = $orderList;
        } else {
            $dbOrder = \Bitrix\Sale\Internals\OrderTable::GetList(array(
                'order'   => array("ID" => "ASC"),
                'filter'  => array('ID' => $orderId),
                'limit'   => $pSize,
                'select'  => array('ID')
            ));
            while ($arOrder = $dbOrder->fetch()) {
                $orderIds[] = $arOrder['ID'];
            }
        }
        
        if (count($orderIds) <= 0) {
            return false;
        }

        $api_host = COption::GetOptionString(self::$MODULE_ID, self::$CRM_API_HOST_OPTION, 0);
        $api_key = COption::GetOptionString(self::$MODULE_ID, self::$CRM_API_KEY_OPTION, 0);

        $optionsSitesList = unserialize(COption::GetOptionString(self::$MODULE_ID, self::$CRM_SITES_LIST, 0));  
        $optionsOrderTypes = unserialize(COption::GetOptionString(self::$MODULE_ID, self::$CRM_ORDER_TYPES_ARR, 0));
        $optionsDelivTypes = unserialize(COption::GetOptionString(self::$MODULE_ID, self::$CRM_DELIVERY_TYPES_ARR, 0));
        $optionsPayTypes = unserialize(COption::GetOptionString(self::$MODULE_ID, self::$CRM_PAYMENT_TYPES, 0));
        $optionsPayStatuses = unserialize(COption::GetOptionString(self::$MODULE_ID, self::$CRM_PAYMENT_STATUSES, 0)); // --statuses
        $optionsPayment = unserialize(COption::GetOptionString(self::$MODULE_ID, self::$CRM_PAYMENT, 0));
        $optionsOrderProps = unserialize(COption::GetOptionString(self::$MODULE_ID, self::$CRM_ORDER_PROPS, 0));
        $optionsLegalDetails = unserialize(COption::GetOptionString(self::$MODULE_ID, self::$CRM_LEGAL_DETAILS, 0));
        $optionsContragentType = unserialize(COption::GetOptionString(self::$MODULE_ID, self::$CRM_CONTRAGENT_TYPE, 0));
        $optionsCustomFields = unserialize(COption::GetOptionString(self::$MODULE_ID, self::$CRM_CUSTOM_FIELDS, 0));

        $api = new RetailCrm\ApiClient($api_host, $api_key);

        $arParams = array(
            'optionsOrderTypes'     => $optionsOrderTypes,
            'optionsDelivTypes'     => $optionsDelivTypes,
            'optionsPayTypes'       => $optionsPayTypes,
            'optionsPayStatuses'    => $optionsPayStatuses,
            'optionsPayment'        => $optionsPayment,
            'optionsOrderProps'     => $optionsOrderProps,
            'optionsLegalDetails'   => $optionsLegalDetails,
            'optionsContragentType' => $optionsContragentType,
            'optionsSitesList'      => $optionsSitesList,
            'optionsCustomFields'   => $optionsCustomFields,
        );

        $recOrders = array();
        foreach ($orderIds as $orderId) {
            $id = \Bitrix\Sale\Order::load($orderId);
            if (!$id) {
                continue;
            }
            $order = RetailCrmOrder::orderObjToArr($id);
            
            $user = Bitrix\Main\UserTable::getById($order['USER_ID'])->fetch();

            $arCustomers = RetailCrmUser::customerSend($user, $api, $optionsContragentType[$order['PERSON_TYPE_ID']], false, $site);
            $arOrders = RetailCrmOrder::orderSend($order, $api, $arParams, false, $site); 

            if (!$arCustomers || !$arOrders) {
                continue;
            }
            
            $resCustomers[$order['LID']][] = $arCustomers;
            $resOrders[$order['LID']][] = $arOrders; 
            
            $recOrders[] = $orderId;
        }
        
        if (count($resOrders) > 0) {
            foreach ($resCustomers as $key => $customerLoad) {
                if ($optionsSitesList) {
                    if (array_key_exists($key, $optionsSitesList) && $optionsSitesList[$key] != null) {
                        $site = $optionsSitesList[$key];
                    } else {
                        continue;
                    }
                } elseif (!$optionsSitesList) {
                    $site = 'dsklad-ru';
                }
                if (RCrmActions::apiMethod($api, 'customersUpload', __METHOD__, $customerLoad, $site) === false) {
                    var_dump($customerLoad);
                    return false;
                }
                if (count($optionsSitesList) > 1) {
                    time_nanosleep(0, 250000000);
                }
            }
            foreach ($resOrders as $key => $orderLoad) {

                if ($optionsSitesList) {
                    if (array_key_exists($key, $optionsSitesList) && $optionsSitesList[$key] != null) {
                        $site = $optionsSitesList[$key];
                    } else {
                        continue;
                    }
                } elseif (!$optionsSitesList) {
                    $site = 'dsklad-ru';
                }
                if (RCrmActions::apiMethod($api, 'ordersUpload', __METHOD__, $orderLoad, $site) === false) {
                    var_dump($orderLoad);
                    return false;
                }
                if (count($optionsSitesList) > 1) {
                    time_nanosleep(0, 250000000);
                }
            }
            if ($failed == true && $failedIds !== false && count($failedIds) > 0) {
                COption::SetOptionString(self::$MODULE_ID, self::$CRM_ORDER_FAILED_IDS, serialize(array_diff($failedIds, $recOrders)));
            } elseif ($lastUpOrderId < max($recOrders) && $orderList === false) {
                COption::SetOptionString(self::$MODULE_ID, self::$CRM_ORDER_LAST_ID, max($recOrders));
            }
        }

        return true;
    }
    
}
