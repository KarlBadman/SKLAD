<?
if(!function_exists('getar')) {
    function getar($ar)
    {
        global $USER;
        if ($USER->GetID() == 1039) {
            echo '<script type="text/javascript">console.log(' . json_encode($ar) . ');</script>';
        }
    }
}
if(!function_exists('cutString')) {
    function cutString($string, $maxlen)
    {
        $len = (mb_strlen($string) > $maxlen)
            ? mb_strripos(mb_substr($string, 0, $maxlen), ' ')
            : $maxlen;
        $cutStr = mb_substr($string, 0, $len);
        return (mb_strlen($string) > $maxlen)
            ? '' . $cutStr . '...'
            : '' . $cutStr . '';
    }
}
if(!function_exists('api_get_img_patch')) {
    function api_get_img_patch($id_img, $SITE_URL)
    {
        $src = CFile::GetPath($id_img);
        if (!empty($src)) {
            return $SITE_URL . $src;
        } else {
            return "";
        }
    }
}
if(!function_exists('get_img_from_hl')) {
    function get_img_from_hl($UF_XML_ID, $SITE_URL)
    {

        if (!empty($src)) {
            return $SITE_URL.$src;
        } else {
            return "";
        }
    }
}

function getList($idIblock, $arOrder, $arFilter, $arSelect){

    if (intval($idIblock) <= 0 ) {
        return false;
    }
    CModule::IncludeModule('highloadblock');
    $rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(
        array(
            'filter'=>array(
                'ID' => $idIblock
            )
        )
    );
    if ( !($arData = $rsData->fetch()) ){
        return 'High-блок не найден';
    }
    $LANG_entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData);

    $main_query = new \Bitrix\Main\Entity\Query($LANG_entity);


    if (!empty($arOrder)) {
        $main_query->setOrder($arOrder);
    }

    if (!empty($arFilter)) {
        $main_query->setFilter($arFilter);
    }

    if (!empty($arSelect)) {
        $main_query->setSelect($arSelect);
    }else{
        $main_query->setSelect(array('*'));
    }

    $result = $main_query->exec();

    $result = new CDBResult($result);
    $rows = array();
    while ($row = $result->Fetch()){
        foreach ($row as $k => $v)	{
            $row[$k] = $v;
        }
        $rows[] = $row;
    }
    return $rows;
}

if (!function_exists('get_parent_section_by_element_ID')) {
    function get_parent_section_by_element_ID ($elementID) {
        if (empty($elementID))
            return false;

        $rsElement = CIBlockElement::GetByID ($elementID)->Fetch();
        if (!empty($rsElement['IBLOCK_SECTION_ID']) AND $rsElement['IBLOCK_ID'] == CATALOG_IBLOCK_ID) {
            $arSection = get_parent_section_by_section_ID ($rsElement['IBLOCK_SECTION_ID']);
        }

        return $arSection;
    }
}

if (!function_exists('get_parent_section_by_section_ID')) {
    function get_parent_section_by_section_ID ($sectionID) {
        if (empty($sectionID))
            return false;

        $navChain = CIBlockSection::GetNavChain (CATALOG_IBLOCK_ID, $sectionID);
        if ($arNav = $navChain->GetNext()) {
            return $arNav;
        }
    }
}

/**
*   Extra log exception catcher
*   @param $arParams | array | $arParams['exception_type'],
*   $arParams['exception_entity'], $arParams['mail_comment'],
*   $arParams['entity_id'], $arParams['entity_type'], $arParams['exception_text']
*   @return mixed
*/
if (!function_exists('extra_log')) {
    function extra_log ($arParams = []) {

        require_once (realpath(__DIR__) . "/xtralogtableorm.php");

        if (
            !is_array($arParams) || empty($arParams)
                || empty($arParams['exception_type']) || empty($arParams['exception_entity'])
                    || empty($arParams['entity_id']) || empty($arParams['entity_type'])
            )
                return false;

        $xtraLog = XtraLogTable::getList([
            'select' => ['*'],
            'filter' => [
                'entity_id' => $arParams['entity_id'],
                'exception_type' => $arParams['exception_type'],
                'exception_entity' => $arParams['exception_entity'],
            ]
        ])->fetchAll();

        $arFields = [
            "entity_type" => $arParams['entity_type'],
            "entity_id" => $arParams['entity_id'],
            "exception_type" => $arParams['exception_type'],
            "exception_entity" => $arParams['exception_entity'],
            "extra_info" => $arParams['exception_text'],
            "updated_at" => count ($xtraLog) < 1 ? '' : \Bitrix\Main\Type\DateTime::createFromTimestamp(time()),
            "count" => count ($xtraLog) < 1 ? 1 : intVal($xtraLog[0]['count'])+1
        ];

        if (count ($xtraLog) < 1) {

            $LOG_ID = XtraLogTable::add($arFields);
            if(intval($LOG_ID->getID()) && $LOG_ID->isSuccess()) {
				$obContext = \Bitrix\Main\Context::getCurrent();
				// goes to email
				$arFields['ID'] = $LOG_ID->getID();
				$arFields['COMMENT'] = $arParams['mail_comment'] . ' на сайте '.$_SERVER['SERVER_NAME'];
				$arMailFields = array(
					'EVENT_NAME' => 'LOGGING',
					'LID' => $obContext->getSite(),
					'C_FIELDS' => $arFields
				);
				if (CEvent::Send($arMailFields['EVENT_NAME'], $arMailFields['LID'], $arMailFields['C_FIELDS']))
                    return true;
			}

        } else {
            $res = XtraLogTable::update($xtraLog[0]['id'], $arFields);
            if ($res->isSuccess())
                return true;
        }

        return false;
    }
}


/**
 *   Extra log exception catcher
 *   @param $arParams | array | $arParams['exception_type'],
 *   $arParams['exception_entity'], $arParams['mail_comment'],
 *   $arParams['entity_id'], $arParams['entity_type'], $arParams['exception_text']
 *   @return mixed
 */
if (!function_exists('checkTempStockAcitvity')) {
    function checkTempStockAcitvity ()
    {
        global $APPLICATION;

        CModule::IncludeModule('highloadblock');
        $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('TABLE_NAME'=>'temporary_stock')))->Fetch();
        $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $stockEntityDataClass = $obEntity->getDataClass();
        $activeStock = $stockEntityDataClass::getList(array(
            'select' => array('*'),
            'filter' => array('=UF_ACTIVE' => '1', '<UF_START_DATE'=> ConvertTimeStamp(time(), 'FULL'), '>UF_FINISH_DATE'=>ConvertTimeStamp(time(), 'FULL')),
            'limit' => 1
        ))->Fetch();

        if($activeStock) {
            $styles = '
            .stickers__widget span.tmpstock:before{
                content:\'\';
            }
            .stickers__widget span.tmpstock{
                background: url(' . CFile::GetPath($activeStock['UF_STICKER_ICON']) . ') center center no-repeat;;
            }

            .temp_stock_menu {
                font-weight: 700;
            }
            .temp_stock_menu .' . $activeStock['UF_ICON_CLASS'] . ' {
                background: url(/local/templates/dsklad/images/fire.png) center center no-repeat;
                width: 23px;
                height: 23px;
                float: left;
                margin-top: -3px;
                background-size: contain;
                margin-right: 5px;
            }';
            if($activeStock['UF_ICON_ON_TAG']) {
                $styles .= '
            a.filter-item[data-id="' . $activeStock['UF_TAG'] . '"]:before {
                content: "";
                background: url(/local/templates/dsklad/images/fire.png) center center no-repeat;
                background-size: contain;
                display: inline-block;
                width: 16px;
                height: 14px;
                position: relative;
                margin-right: 4px;
            }';
            }
            $styles .='
            @media only screen and (min-width:1000px) and (max-width: 1215px) {
                .topnav nav a {
                    font-size: 12px;
                    letter-spacing: 0.02em;
                }
                .topnav nav a.sale {
                    margin-right:5px;
                }
            }

            @media only screen and (min-width:1000px) and (max-width: 1060px) {
                .topnav nav a {
                    font-size: 11px;
                    letter-spacing: 0.02em;
                }
                .topnav nav a.sale {
                    margin-right:5px;
                }
            }
            @media only screen and (min-width: 1100px){
                .topnav .main-nav-holder {
                    max-width: 1500px;
                }
            }
            @media only screen and (max-width: 999px){
                .topnav .main-nav-holder {
                    width: 1080px;
                }
            }';
            $vars = array(
                'class'=>$activeStock['UF_CSS_CLASS'],
                'link'=>$activeStock['UF_LINK'],
                'text'=>$activeStock['UF_NAME'],
                'icon_class'=>$activeStock['UF_ICON_CLASS'],
                'styles'=>$styles,
            );
            $APPLICATION->set_cookie("TEMP_STOCK_ON", 'true', time()+60*60*24, "/");

            return $vars;
        } else {
            if($APPLICATION->get_cookie("TEMP_STOCK_ON")){
                $APPLICATION->set_cookie("TEMP_STOCK_ON", 'false', time(), "/");
            }
            return false;
        }
    }
}

if (!function_exists('ipCheck2Zone')) {
    function ipCheck2Zone($IP, $CIDR) {
        list ($net, $mask) = explode ('/', $CIDR);
        $ip_net = ip2long ($net);
        $ip_mask = ~((1 << (32 - $mask)) - 1);
        $ip_ip = ip2long ($IP);
        return !empty($mask) ? (($ip_ip & $ip_mask) == ($ip_net & $ip_mask)) : ($ip_net == $ip_ip);
    }
}

if (!function_exists('checkUserIPinHiddenGroup')) {
    function checkUserIPinHiddenGroup () {

        global $APPLICATION, $USER, $arLocationInfo, $arConditionsResult, $EXCLUDEDGROUPID, $EXCLUDEDRIGHTID; $hlExcludeIpsTableName = 'excludedconditions'; $EXCLUDEDRIGHTID = '45';
        /*$USER = new CUser;*/ $GROUP = new Bitrix\Main\GroupTable; $EXCLUDEDGROUPID = $groupID = $GROUP->GetList(['filter' => ['STRING_ID' => 'untermensh']])->fetchAll()[0]['ID'];
        CModule::IncludeModule("highloadblock"); CModule::IncludeModule("statistic");
        $cityObj = new CCity(); $arLocationInfo = $cityObj->GetFullInfo();
        $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(['filter' => ['TABLE_NAME' => $hlExcludeIpsTableName]])->Fetch();
        if (!empty($arHLBlock)) {
            $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
            $hlExcludeIpsEntityClass = $obEntity->getDataClass();
            $arExcludedIps = $hlExcludeIpsEntityClass::getList([
                'select' => ['*']
            ])->fetchAll();
        }

        if (!empty($arExcludedIps) && is_array($arExcludedIps)) {

            $arConditionsResult = array_map(function ($item) {
                global $arLocationInfo, $arConditionsResult; $status = false;

                if ($arConditionsResult === true) return true;

                if ($item['UF_INVERSE'] == '0') {

                    if (!empty($item['UF_IP']) && !empty($item['UF_COUNTRY_CODE'])) {
                        $status = (strtoupper($item['UF_COUNTRY_CODE']) == strtoupper($arLocationInfo['COUNTRY_CODE']['VALUE']) && ipCheck2Zone($arLocationInfo['IP_ADDR']['VALUE'], $item['UF_IP'])) ? true : false;
                    } elseif (!empty($item['UF_COUNTRY_CODE']) && empty($item['UF_IP'])) {
                        $status = strtoupper($item['UF_COUNTRY_CODE']) == strtoupper($arLocationInfo['COUNTRY_CODE']['VALUE']) ? true : false;
                    } elseif (!empty($item['UF_IP']) && empty($item['UF_COUNTRY_CODE'])) {
                        $status = ipCheck2Zone($arLocationInfo['IP_ADDR']['VALUE'], $item['UF_IP']) ? true : false;
                    }
                } else {

                    if (!empty($item['UF_IP']) && !empty($item['UF_COUNTRY_CODE'])) {
                        $status = (strtoupper($item['UF_COUNTRY_CODE']) != strtoupper($arLocationInfo['COUNTRY_CODE']['VALUE']) && !ipCheck2Zone($arLocationInfo['IP_ADDR']['VALUE'], $item['UF_IP'])) ? true : false;
                    } elseif (!empty($item['UF_COUNTRY_CODE']) && empty($item['UF_IP'])) {
                        $status = strtoupper($item['UF_COUNTRY_CODE']) != strtoupper($arLocationInfo['COUNTRY_CODE']['VALUE']) ? true : false;
                    } elseif (!empty($item['UF_IP']) && empty($item['UF_COUNTRY_CODE'])) {
                        $status = !ipCheck2Zone($arLocationInfo['IP_ADDR']['VALUE'], $item['UF_IP']) ? true : false;
                    }
                }

                return $status;

            }, $arExcludedIps);

            $userGroups = $USER->GetUserGroupArray();
            if ($arConditionsResult[0]) {
                if (array_search($groupID, $USER->GetUserGroupArray()) === false && !in_array(1, $USER->GetUserGroupArray())) {
                    $USER->SetUserGroupArray(array_merge($userGroups, [intVal($groupID)]));
                    $USER->SetUserGroup($USER->GetID(), array_merge($userGroups, [intVal($groupID)]));
                }
            } else {
                $groupKey = array_search($groupID, $userGroups);
                if ($groupKey !== false) {
                    unset($userGroups[$groupKey]);
                    $USER->SetUserGroupArray($userGroups);
                    $USER->SetUserGroup($USER->GetID(), $userGroups);
                }
            }

            // var_dump($USER->GetUserGroupArray(), in_array(1, $USER->GetUserGroupArray()));
        }
    }
}

if (!function_exists('GetCountProductInBasket')) {
    function GetCountProductInBasket($idProduct)
    {
        $count = 0;
        $basket = Bitrix\Sale\Basket::loadItemsForFUser(Bitrix\Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
        foreach ($basket as $basketItem) {
            if ($idProduct == $basketItem->getProductId()) $count += $basketItem->getQuantity();
        }
        return $count;
    }
}


function GetCountProductInBasketOffer($ProductID, $OfferID)
{
    $count = 0;
    $arOffers = CCatalogSKU::getOffersList($ProductID);
    $arOffersIDs = array_keys($arOffers[$ProductID]);
    $basket = Bitrix\Sale\Basket::loadItemsForFUser(Bitrix\Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
    foreach($basket as $basketItem)
    {
        $idOffer = $basketItem->getProductId();
        if(in_array($idOffer, $arOffersIDs) && $idOffer != $OfferID) $count += $basketItem->getQuantity();
    }
    return $count;
}

/**
 * Получение информации о весе и размере торгового предложения
 * Сначала ищется в HL-блоке, если не найдено - запрос к 1С
 * Данные из 1С добавляются в HL-блок
 *
 * @param string $productId
 * @return array
 */
function GetPackData($productId){

    \Bitrix\Main\Loader::includeModule('highloadblock');

    $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(DPD_UPAKOVKI_HL_ID)->fetch();
    $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
    $strEntityDataClass = $obEntity->getDataClass();

    $out = [];

    $rsData = $strEntityDataClass::getList([
        'order' => [
            'UF_QUANTITY' => 'DESC'
        ],
        'filter' => [
            'UF_ID' => $productId
        ],
        'select' => [
            'ID',
            'UF_ID',
            'UF_WEIGHT',
            'UF_LENGTH',
            'UF_WIDTH',
            'UF_HEIGHT',
            'UF_QUANTITY'
        ],
    ]);

    $foundInHl = false;
    while ($arItem = $rsData->fetch()) {
        $curPack = [
            'WEIGHT' => $arItem['UF_WEIGHT'],
            'LENGTH' => $arItem['UF_LENGTH'],
            'WIDTH' => $arItem['UF_WIDTH'],
            'HEIGHT' => $arItem['UF_HEIGHT'],
            'QUANTITY' => $arItem['UF_QUANTITY'],
        ];

        $out[] = $curPack;

        $foundInHl = true;
    }

    if (!$foundInHl) {
        $url = ADDRESS_1C_SERVICES . '/UTDSklad/hs/getallpackinfo/AllPackInfo/' . $productId;
        $url = str_replace('#', '*', $url);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERPWD, 'webservup:Pfujnjdrf5');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
        $raw1cData = json_decode(curl_exec($curl), true);

        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $responseBody = curl_exec($curl);

        if ($statusCode != 200) {
            extra_log(
                array(
                    'entity_type' => 'getallpackinfo',
                    'entity_id' => $statusCode,
                    'exception_type' => 'getallpackinfo_error',
                    'exception_entity' => 'getallpackinfo',
                    "exception_text" => 'Не нашли упаковку для товара '.$productId.', обратились к веб-сервису получения данных по упаковкам, но сервер ответил кодом ' . $statusCode . ' ' . $responseBody,
                    "mail_comment" => 'Не нашли упаковку для товара '.$productId.', обратились к веб-сервису получения данных по упаковкам, но сервер ответил кодом ' . $statusCode . ' ' . $responseBody,
                )
            );

        }

        curl_close($curl);

        if (!empty($raw1cData['МассивУпаковок'])) {
            foreach ($raw1cData['МассивУпаковок'] as $key => $arUpakovka) {
                $curPack = [
                    'WEIGHT' => (float)str_replace(',', '.', $arUpakovka['Вес']),
                    'LENGTH' => (float)str_replace(',', '.', $arUpakovka['Длина']),
                    'WIDTH' => (float)str_replace(',', '.', $arUpakovka['Ширина']),
                    'HEIGHT' => (float)str_replace(',', '.', $arUpakovka['Высота']),
                    'QUANTITY' => (int)$arUpakovka['Количество'],
                ];

                $out[] = $curPack;

                $strEntityDataClass::Add([
                    'UF_ID' => $productId,
                    'UF_WEIGHT' => $curPack['WEIGHT'],
                    'UF_LENGTH' => $curPack['LENGTH'],
                    'UF_WIDTH' => $curPack['WIDTH'],
                    'UF_HEIGHT' => $curPack['HEIGHT'],
                    'UF_QUANTITY' => $curPack['QUANTITY']
                ]);

                /*
                $mode = '1C';
                */

            }
        }
    }

    return $out;
}


// мы в подвале используем стандартный компонент bitrix, нужна ли еще эта функция?
//подписка подвал
function uniSenderSubscriber($arSubscriber = array())
{
    $api_key = "5z3181izq4gcungh7ydy1hcys6xp6khrtxb359xe";

    // Данные о новом подписчике
    $user_email = $arSubscriber["EMAIL"];
    $user_name = iconv('utf-8', $arSubscriber["NAME"]);
    $user_lists = "8329797";
    //    $user_ip = "12.34.56.78";
    $user_tag = urlencode($arSubscriber["ENTER"]);

    // Создаём POST-запрос
    $POST = array(
        'api_key' => $api_key,
        'list_ids' => $user_lists,
        'fields[email]' => $user_email,
        'fields[Name]' => $user_name,
        //'request_ip' => $user_ip,
        'tags' => $user_tag
    );

    // Устанавливаем соединение
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $POST);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_URL,
        'http://api.unisender.com/ru/api/subscribe?format=json');
    $result = curl_exec($ch);

    $strOut = '';

    if ($result) {
        // Раскодируем ответ API-сервера
        $jsonObj = json_decode($result);

        if (null === $jsonObj) {
            // Ошибка в полученном ответе
            $strOut .= "Invalid JSON";

        } elseif (!empty($jsonObj->error)) {
            // Ошибка добавления пользователя
            $strOut .= "An error occured: " . $jsonObj->error . "(code: " . $jsonObj->code . ")";

        } else {
            // Новый пользователь успешно добавлен
            //echo "Added. ID is " . $jsonObj->result->person_id;
            $strOut .= "Вы успешно подписались!";

        }
    } else {
        // Ошибка соединения с API-сервером
        $strOut .= "API access error";
    }

    return $strOut;
}

function getProductPrice($ProductID){
    $result = array();
    $db_res = CPrice::GetList(
        array(),
        array(
            "PRODUCT_ID" => $ProductID,
        )
    );
    while($ar_res = $db_res->Fetch())
    {
        $result[$ar_res["CATALOG_GROUP_ID"]]=array(
            "ID" => $ar_res["ID"],
            "PRICE" => $ar_res["PRICE"],
            "CATALOG_GROUP_ID" => $ar_res["CATALOG_GROUP_ID"],
            "CURRENCY" => $ar_res["CURRENCY"],
        );
    }
    return $result;
}

function hasDiscount($ProductID){
    $res= \getProductPrice($ProductID);
    if(($res[1]['PRICE']>$res[2]['PRICE']) and ($res[2]['PRICE']>0)){
        return true;
    }
    return false;
}

function ppp($p,$name){
    global $APPLICATION,$USER;
    if ($USER->IsAdmin()){
        echo "<br/>-----------------$name START -------------------<br/>";
        echo "<pre>";
        print_r($p);
        echo "</pre>";
        echo "<br/>-----------------$name END -------------------<br/>";
    }
}

function pp($p,$name){

    echo "<br/>-----------------$name START -------------------<br/>";
    echo "<pre>";
    print_r($p);
    echo "</pre>";
    echo "<br/>-----------------$name END -------------------<br/>";

}


function gaParseCookie() {
    if (isset($_COOKIE['_ga'])) {
        list($version,$domainDepth, $cid1, $cid2) = preg_split('[\.]', $_COOKIE["_ga"],4);

        $contents = array(
            'version' => $version,
            'domainDepth' => $domainDepth,
            'cid' => $cid1.'.'.$cid2
        );
        $cid = $contents['cid'];
    }


    return $cid;
}

function rotateBlock($arr){ // если использовать rotate, для неперемешиваемых участков изобржаения
    foreach($arr as $key=>$item){
        $item->rotateImage(new ImagickPixel('none'), $key * 90);
    }
    return $arr;
}

function swop_array($arr, $file){
    $key = preg_replace('/[^\d]+/', '', $file);
    $key = str_split(strrev($key));

    $new_array = array();
    $array_keys = array();
    $last = count($arr) - 1;
    foreach($key as $item){
        if(!in_array($item, $array_keys)){
            if($last > $item){
                $array_keys[] = $item;
                $new_array[] = $arr[$item];
                unset($arr[$item]);
            }
        }
    }
    $new_array = array_merge($new_array, $arr);
    return $new_array;
}

function swopImage($file){
    $io = CBXVirtualIo::GetInstance();
    $uploadDirName = COption::GetOptionString("main", "upload_dir", "upload");
    $imageFile = "/" . $uploadDirName."/" . "swo" . "/" . $file["SUBDIR"] . "/" . $file["FILE_NAME"];
    $newsubdir = "/" . 'swo/' . $file["SUBDIR"] . '/';
    try {
        if(!file_exists($newFile = $io->GetPhysicalName( $_SERVER["DOCUMENT_ROOT"] . $imageFile))){
            $fileImg = $io->GetPhysicalName( $_SERVER["DOCUMENT_ROOT"] . "/" . $uploadDirName."/" . $file["SUBDIR"] . "/" . $file["FILE_NAME"]);

            $imagick = new Imagick($fileImg);
            $allowable_size = array(800, 1200, 1600); //допустимые размеры ширины изображений
            $current_file_width = $imagick->getImageWidth();
            $current_file_height = $imagick->getImageHeight();
            $imagick->destroy();
            if (in_array($current_file_width, $allowable_size)) {
                $height = $width = $current_file_width; //формируем квадрат на основании допустимой ширины
            } elseif ($current_file_width == 550) { //Зачем-то маркетинг решил защищать и упоротые снимки
                $height = $width = 548;
            } elseif ($current_file_width < 800 || $current_file_height < 800) { //если снимок меньше допустимого размера пропускаем его (высота или ширина меньше 800 пикселей)
                return $file;
            } else {
                $height = $width = 1600; // попытка дотянуть до максимально допустимого, если размер изображения не укладывается в стандарт
            }

            $division_param = !empty($division_param) ? $division_param : 4; //на какое кол-во режем изображение. Написано так, мало ль нужно будет сюда его передать как параметр
            $piece_height = $height/$division_param;
            $piece_width = $width/$division_param;

            $tmp = array();
            for ($x = 0; $x < $division_param; $x++) { // магия пересборки началась
                for ($y = 0; $y < $division_param; $y++) {
                    $key = $x * $division_param + $y;
                    $tmp[$key] = new Imagick($fileImg);
                    $tmp[$key]->cropImage($piece_height, $piece_width,$x*$piece_height, $y*$piece_width);
                }
            }

            $tmp = swop_array($tmp, $newFile); // магия пересборки закончилась
            $output= new Imagick($fileImg);
            $output->newImage($width, $height, new ImagickPixel('white')); // готовим белый холст контейнер
            for ($x = 0; $x < $division_param; $x++) { // склеиваем что получилось в нарезку
                for ($y = 0; $y < $division_param; $y++) {
                    $output->compositeimage($tmp[($x * $division_param)+$y]->getimage(), Imagick::COMPOSITE_COPY, $x*$piece_height, $y*$piece_width);
                }
            }
            if($dir = $_SERVER["DOCUMENT_ROOT"] . "/" . $uploadDirName . $newsubdir){
                mkdir($dir, 0755, true);
            }
            $output->writeImage($newFile);
            $output->destroy();
        }
    } catch (Exception $e){
        extra_log(
            array(
                'entity_type' => 'image file',
                'entity_id' => $file['ID'],
                'exception_type' => 'swop_image_exception',
                'exception_entity'=>'imagick_error',
                "exception_text" => (string)$e,
                "mail_comment" => 'Зафиксировано исключение при работе с изображением'
            )
        );
    }

    $file["SUBDIR"] = $newsubdir;
    $file["SRC"] = $imageFile;

    return $file;
}


//определение телефона
//Any mobile device (phones or tablets).

function mobileDetectFunction(){

    global $APPLICATION;
    $detect = new Mobile_Detect;
    if ($detect->isMobile()) $APPLICATION->IS_MOBILE = 'Y';
    if ($detect->isMobile() && empty($_COOKIE['TYPE_DEVICE_MOB'])) {
        setcookie("TYPE_DEVICE_MOB", "Y", 0, "/");
    }
}

// TODO
function actionMigrationGo()
{
    if ($_SERVER['HTTP_HOST'] != 'www.dsklad.ru:443') {
        include $_SERVER['DOCUMENT_ROOT'] . '/local/libs/vendor/autoload.php';
        Arrilot\BitrixMigrations\Autocreate\Manager::init($_SERVER["DOCUMENT_ROOT"] . '/migrations');
    }
}

// Проверяем пришел ли пользователь с номером заказа и токеном авторизации,
// если да, то проверяем токен и авторизовываем
if (!function_exists('authorizeUserByToken')) {
    function authorizeUserByToken ($orderID = 0, $token = "") {

        CModule::IncludeModule('sale'); global $USER, $canAuth; $canAuth = false;
        $order = Bitrix\Sale\Order::load($orderID);
        if ((!$orderID || !$token) || CUser::isAuthorized() || $order === null) return false;
        $orderValues = $order->getFields()->getValues();
        $orderProertyArray = $order->getPropertyCollection()->getArray();
        if ($orderValues['USER_ID'] && !$USER->isAdmin()) {

            if (is_array($orderProertyArray) && !empty($orderProertyArray)) {
                foreach ($orderProertyArray['properties'] as $property) {
                    if ($property['CODE'] == 'F_TOKEN' || $property['CODE'] == 'U_TOKEN') {
                        $canAuth = !$canAuth && !empty($property['VALUE'][0]) && $token == "?".$property['VALUE'][0] ? true : false;
                        if ($canAuth !== false) {

                            $USER->Authorize($orderValues['USER_ID']);

                        } else {

                            extra_log([
                                'entity_type' => 'authorize_token_error',
                                'entity_id' => time(),
                                'exception_type' => 'authorize_token_error',
                                'exception_entity' => 'authorizeUserByToken',
                                "exception_text" => 'Ошибка авторизации по токену, значение не получено из заказа или токен не подошел. Заказ - ' . $orderID . ' токен - '. $token,
                                "mail_comment" => 'Ошибка авторизации по токену, значение не получено из заказа или токен не подошел. Заказ - ' . $orderID . ' токен - '. $token,
                            ]);

                        }
                    }
                }

            } else {

                extra_log([
                    'entity_type' => 'dont_get_property_collection_on_order',
                    'entity_id' => time(),
                    'exception_type' => 'dont_get_property_collection_on_order',
                    'exception_entity' => 'authorizeUserByToken',
                    "exception_text" => 'По заказу не найдена коллекция св-в, юзера не удастся авторизовать. Заказ - ' . $orderID . ' токен - '. $token,
                    "mail_comment" => 'По заказу не найдена коллекция св-в, юзера не удастся авторизовать. Заказ - ' . $orderID . ' токен - '. $token,
                ]);

            }

        } else {

            extra_log([
                'entity_type' => 'authorize_user_by_token',
                'entity_id' => time(),
                'exception_type' => 'authorize_user_by_token',
                'exception_entity' => 'authorizeUserByToken',
                "exception_text" => 'По заказу не найден USER_ID, и поэтому его не удалось авторизовать. Или пользователь является админом. Заказ - ' . $orderID . ' токен - '. $token,
                "mail_comment" => 'По заказу не найден USER_ID, и поэтому его не удалось авторизовать. Или пользователь является админом. Заказ - ' . $orderID . ' токен - '. $token,
            ]);

        }

        return true;

    }
}

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\DeviceParserAbstract;
if (!function_exists()) {
    function checkHitOnHideProductPosition ($area) {
        
        CModule::IncludeModule("statistic");

        // TESTS
        // $_SERVER['REMOTE_ADDR'] = '';
        // $_SERVER['HTTP_USER_AGENT'] = "";
        
        $rsLocationInfo = new CCity();
        $arLocationInfo = $rsLocationInfo->GetFullInfo();
        $dd = new DeviceDetector($_SERVER['HTTP_USER_AGENT']);
        $dd->parse();
        
        if ($dd->isBot()) {
            return false;
        }
        
        if ($arLocationInfo['COUNTRY_CODE']['VALUE'] == 'BY' || $arLocationInfo['COUNTRY_CODE']['VALUE'] == 'KZ') {
            return false;
        }
        
        if (!empty($area)) {
            
            if ($arLocationInfo['COUNTRY_CODE']['VALUE'] != 'N0' && $arLocationInfo['COUNTRY_CODE']['VALUE'] != 'RU' && $area == 'ABROAD') {
                return true;
            }
    
            if ($arLocationInfo['COUNTRY_CODE']['VALUE'] != 'N0' 
                && (/*($arLocationInfo['COUNTRY_CODE']['VALUE'] == 'RU' && strtolower($arLocationInfo['CITY_NAME']['VALUE']) == 'москва') ||*/ $arLocationInfo['COUNTRY_CODE']['VALUE'] != 'RU')
                && $area == 'MSKABROAD') {
                return true;
            }
            
        }
        
        return false;
        
    }
}