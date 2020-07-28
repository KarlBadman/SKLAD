<?php

use \Bitrix\Main\Web\Uri;
use \Bitrix\Main\Web\HttpClient;

// Подключаем языковые файлы.
IncludeModuleLangFile(__FILE__);

/**
 * Class DellinAPI
 * Класс предназначен для расчета стоимости доставки.
 */
class DellinAPI
{
    /**
     * URL API для расчета стоимости перевозки.
     * @var string
     */
    protected static $calculator_url = 'https://api.dellin.ru/v1/public/calculator.json';

    /**
     * URL API для поиска населенного пункта.
     * @var string
     */
    protected static $city_search_url = 'https://www.dellin.ru/api/cities/search.json';

    /**
     * Id типов упаковки
     * @var array
     */
    protected static $packing_types_id = array(
        'hard' => '0x838FC70BAEB49B564426B45B1D216C15',
        'additional' => '0x9A7F11408F4957D7494570820FCF4549',
        'bubble' => '0xA8B42AC5EC921A4D43C0B702C3F1C109',
        'bag' => '0xAD22189D098FB9B84EEC0043196370D6',
        'pallet' => '0xBAA65B894F477A964D70A4D97EC280BE'
    );

    /**
     * Находим информацию о городе через api деловых линий.
     * @param $query
     * @return mixed
     */
    protected static function SearchCity($query)
    {
        // Формируем строку запроса.
        $uri = new Uri(self::$city_search_url);
        $uri->addParams(array('q' => $query));

        // Инициализируем http клиент.
        $http = new HttpClient();
        $response = $http->get($uri->getUri());

        return json_decode($response);
    }

    /**
     * Определяем населенный пункт по региону.
     * @param array $city_list
     * @param string $bx_city_name
     * @param string $bx_region_name
     * @return mixed
     */
    protected static function SelectCityByRegion($city_list, $bx_city_name, $bx_region_name = '')
    {
        $dl_city = $city_list[0];

        $bx_region_name = iconv('utf-8', 'cp1251', $bx_region_name);
        $short_region_name = str_replace('область', 'обл.', $bx_region_name);

        $bx_region_name = iconv('cp1251', 'utf-8', $bx_region_name);
        $short_region_name = iconv('cp1251', 'utf-8', $short_region_name);

        foreach ($city_list as $item) {
            $item_city_name = (string)$item->city;
            $item_region_name = (string)$item->regionString;

            if (
                $item_city_name  == $bx_city_name && (
                    $item_region_name == $short_region_name ||
                    $item_region_name == $bx_region_name
                )
            ) {
                $dl_city = $item;
                break;
            }
        }

        return $dl_city;
    }

    /**
     * Получаем КЛАДР код по названию города.
     * Хочу заметить, что работает только с городами РФ.
     * @param integer $bx_location_to_id
     * @return string
     */
    public static function GetCityKLADRCode($bx_location_to_id)
    {
        $kladr_code = '';

        $cache = new CPHPCache();
        $life_time = 24*60*60;
        $cache_id = 'DELLIN_LOCATION_KLADR_CODE|' . $bx_location_to_id;

        if ($cache->InitCache($life_time, $cache_id)) {
            $cache_data = $cache->GetVars();
            $kladr_code = $cache_data['VALUE'];
        } else {
            $db_vars = CSaleLocation::GetList(false, array("CODE" => $bx_location_to_id, "LID" => "ru"));
            $bx_location = $db_vars->Fetch();
            $dl_locations = empty($bx_location) ? array() : self::SearchCity($bx_location['CITY_NAME']);

            if (count($dl_locations) > 0) {
                $dl_city = self::SelectCityByRegion($dl_locations, $bx_location['CITY_NAME']);
                $kladr_code = (string)$dl_city->code;

                $cache->StartDataCache($life_time, $cache_id);
                $cache->EndDataCache(array('VALUE' => $kladr_code));
            }
        }

        return $kladr_code;
    }

    /**
     * Рассчитываем кол-во грузовых мест.
     * @param array $arOrder
     * @param array $arConfig
     * @return int
     */
    protected static function GetNumbersOfCargoPlaces($arOrder, $arConfig)
    {
        $numbers_of_places = 1;

        switch ($arConfig['LOADING_GROUPING_OF_GOODS']['VALUE']) {
            // Если считаем весь заказ, как 1 грузоместо.
            case 'ONE_CARGO_SPACE':
                break;

            // Если группируем каждый вид товара, как отдельное грузоместо.
            case 'SEPARATED_CARGO_SPACE':
                $numbers_of_places = count($arOrder['ITEMS']);
                break;

            // Если каждая единица товара - отдельное грузоместо.
            case 'SINGLE_ITEM_SINGLE_SPACE':
                $numbers_of_places = 0;

                foreach ($arOrder['ITEMS'] as $item) {
                    $numbers_of_places += $item['QUANTITY'];
                }
                break;
        }

        return $numbers_of_places;
    }

    /**
     * Конвертируем граммы в килограммы.
     * @param float $bx_goods_weight_in_gram
     * @return bool|float|int
     */
    protected static function ConvertWeightFromGramToKilogram($bx_goods_weight_in_gram)
    {
        $weight_in_kg = CSaleMeasure::Convert((float)$bx_goods_weight_in_gram, "G", "KG");
        return $weight_in_kg > 0 ? $weight_in_kg : 0.01;
    }

    /**
     * Конвертируем кубические миллиметры в не менее кубические метры.
     * @param float $bx_goods_volume_in_mm
     * @return bool|float|int
     */
    protected static function ConvertVolumeFromCubicMillimeterToCubicMeter($bx_goods_volume_in_mm)
    {
        $volume_in_meter = $bx_goods_volume_in_mm / (1000 * 1000 * 1000);

        return $volume_in_meter > 0 ? $volume_in_meter : 0.01;
    }

    /**
     * Проверка на перегруз
     * @param float $weight
     * @return bool
     */
    protected static function IsOversizedWeight($weight)
    {
        return $weight > 100;
    }

    /**
     * Получаем массив с параметрами веса, основываясь на перегрузе
     * @param float $bx_compare_weight
     * @param null|float $bx_set_weight
     * @return array
     */
    protected static function GetWeightArray($bx_compare_weight, $bx_set_weight = null) {
        $compare_weight = self::ConvertWeightFromGramToKilogram($bx_compare_weight);
        $total_weight = is_null($bx_set_weight) ? $compare_weight : self::ConvertWeightFromGramToKilogram($bx_set_weight);

        if (self::IsOversizedWeight($compare_weight)) {
            return array('sized' => $total_weight, 'oversized' => $total_weight);
        }

        return array('sized' => $total_weight);
    }

    /**
     * Определяем параметры "sizedWeight" и "oversizedWeight" по способу группировки груза
     * @param array $arOrder
     * @param array $arConfig
     * @return array
     */
    protected static function CalculateWeight($arOrder, $arConfig)
    {
        $arWeight = array();

        switch ($arConfig['LOADING_GROUPING_OF_GOODS']['VALUE']) {
            // Если считаем весь заказ, как 1 грузоместо.
            case 'ONE_CARGO_SPACE':
                $arWeight = self::GetWeightArray($arOrder['WEIGHT']);
                break;

            // Если группируем каждый вид товара, как отдельное грузоместо.
            case 'SEPARATED_CARGO_SPACE':
                $max_item_weight = 0;
                $sum_item_weight = 0;

                foreach ($arOrder['ITEMS'] as $item) {
                    $item_weight = $item['WEIGHT'] * $item['QUANTITY'];
                    $sum_item_weight += $item_weight;

                    if ($item_weight > $max_item_weight) {
                        $max_item_weight = $item_weight;
                    }
                }

                $arWeight = self::GetWeightArray($max_item_weight, $sum_item_weight);
                break;

            // Если каждая единица товара - отдельное грузоместо.
            case 'SINGLE_ITEM_SINGLE_SPACE':
                $max_item_weight = 0;
                $sum_item_weight = 0;

                foreach ($arOrder['ITEMS'] as $item) {
                    $sum_item_weight += $item['WEIGHT'] * $item['QUANTITY'];

                    if ($item['WEIGHT'] > $max_item_weight) {
                        $max_item_weight = $item['WEIGHT'];
                    }
                }

                $arWeight = self::GetWeightArray($max_item_weight, $sum_item_weight);
                break;
        }

        return $arWeight;
    }

    /**
     * Определяем негаборитный груз, по его размерам.
     * @param int $bx_length
     * @param int $bx_width
     * @param int $bx_height
     * @return bool
     */
    protected static function IsOversized($bx_length, $bx_width, $bx_height)
    {
        return $bx_length >= 3000 || $bx_width >= 3000 || $bx_height >= 3000;
    }

    /**
     * Определяем негаборитный груз, по его объему.
     * @param int $bx_volume
     * @return bool
     */
    protected static function IsOversizedVolume($bx_volume)
    {
        return $bx_volume >= 27000000000;
    }

    /**
     * Получаем массив объемов груза
     * @param int $sized_volume
     * @param null|int $oversized_volume
     * @return array
     */
    protected static function GetVolumeArray($sized_volume, $oversized_volume = null)
    {
        $arVolume = array();

        $arVolume['sized'] = $sized_volume;

        if (!is_null($oversized_volume)) {
            $arVolume['oversized'] = $oversized_volume;
        }

        return $arVolume;
    }

    /**
     * Получаем массив объемов груза с проверкой негабаритности его размеров.
     * @param array $bx_size_list
     * @param int $sized_volume
     * @param null|int $oversized_volume
     * @return array
     */
    protected static function GetVolumeArrayBySize($bx_size_list, $sized_volume, $oversized_volume = null)
    {
        if (self::IsOversized($bx_size_list['length'], $bx_size_list['width'], $bx_size_list['height'])) {
            return self::GetVolumeArray($sized_volume, $oversized_volume);
        }

        return self::GetVolumeArray($sized_volume);
    }

    /**
     * Получаем массив объемов груза с проверкой негабаритности его объема.
     * @param int $bx_volume
     * @param int $sized_volume
     * @param null|float|int $oversized_volume
     * @return array
     */
    protected static function GetVolumeArrayByVolume($bx_volume, $sized_volume, $oversized_volume = null)
    {
        if (self::IsOversizedVolume($bx_volume)) {
            return self::GetVolumeArray($sized_volume, $oversized_volume);
        }

        return self::GetVolumeArray($sized_volume);
    }

    /**
     * Определяем параметры "sizedVolume" и "oversizedVolume" по способу группировки груза
     * @param array $arOrder
     * @param array $arConfig
     * @return array
     */
    protected static function CalculateVolume($arOrder, $arConfig) {
        $arVolume = array();

        $totalVolume = 0;
        $totalOversizedVolume = 0;

        foreach ($arOrder['ITEMS'] as $item) {
            $bx_item_length = $item['DIMENSIONS']['LENGTH'];
            $bx_item_width = $item['DIMENSIONS']['WIDTH'];
            $bx_item_height = $item['DIMENSIONS']['HEIGHT'];

            $totalVolume += $item['QUANTITY'] * $bx_item_length * $bx_item_width * $bx_item_height;
        }

        $totalVolume = self::ConvertVolumeFromCubicMillimeterToCubicMeter($totalVolume);

        switch ($arConfig['LOADING_GROUPING_OF_GOODS']['VALUE']) {
            // Если считаем весь заказ, как 1 грузоместо.
            case 'ONE_CARGO_SPACE':
                $bx_item_max_size = array('length' => 0, 'width' => 0, 'height' => 0);

                foreach ($arOrder['ITEMS'] as $item) {
                    $bx_item_length = $item['DIMENSIONS']['LENGTH'];
                    $bx_item_width = $item['DIMENSIONS']['WIDTH'];
                    $bx_item_height = $item['DIMENSIONS']['HEIGHT'];

                    if ($bx_item_length > $bx_item_max_size['length']) {
                        $bx_item_max_size['length'] = $bx_item_length;
                    }

                    if ($bx_item_width > $bx_item_max_size['width']) {
                        $bx_item_max_size['width'] = $bx_item_width;
                    }

                    if ($bx_item_height > $bx_item_max_size['height']) {
                        $bx_item_max_size['height'] = $bx_item_height;
                    }
                }

                $arVolume = self::GetVolumeArrayBySize($bx_item_max_size, $totalVolume, $totalVolume);
                break;

            // Если группируем каждый вид товара, как отдельное грузоместо.
            case 'SEPARATED_CARGO_SPACE':
                foreach ($arOrder['ITEMS'] as $item) {
                    $bx_item_length = $item['DIMENSIONS']['LENGTH'];
                    $bx_item_width = $item['DIMENSIONS']['WIDTH'];
                    $bx_item_height = $item['DIMENSIONS']['HEIGHT'];

                    $bx_item_volume = $item['QUANTITY'] * $bx_item_length * $bx_item_width * $bx_item_height;

                    if (self::IsOversizedVolume($bx_item_volume)) {
                        $totalOversizedVolume += $bx_item_volume;
                    }
                }

                $arVolume = self::GetVolumeArrayByVolume(
                    $totalOversizedVolume,
                    $totalVolume,
                    self::ConvertVolumeFromCubicMillimeterToCubicMeter($totalOversizedVolume)
                );
                break;

            // Если каждая единица товара - отдельное грузоместо.
            case 'SINGLE_ITEM_SINGLE_SPACE':
                foreach ($arOrder['ITEMS'] as $item) {
                    $bx_item_length = $item['DIMENSIONS']['LENGTH'];
                    $bx_item_width = $item['DIMENSIONS']['WIDTH'];
                    $bx_item_height = $item['DIMENSIONS']['HEIGHT'];

                    $bx_item_volume = $bx_item_length * $bx_item_width * $bx_item_height;

                    if (self::IsOversizedVolume($bx_item_volume)) {
                        $totalOversizedVolume += $bx_item_volume;
                    }
                }

                $arVolume = self::GetVolumeArrayByVolume(
                    $totalOversizedVolume,
                    $totalVolume,
                    self::ConvertVolumeFromCubicMillimeterToCubicMeter($totalOversizedVolume)
                );
                break;
        }

        return $arVolume;
    }

    /**
     * Получаем значения для поля "{arrival-derival}Services"
     * @param array $bx_fields
     * @return array
     */
    protected static function GetAdditionalServices($bx_fields)
    {
        $value_list = array();

        foreach ($bx_fields as $item) {
            if ($item['VALUE'] == 'NULL') {
                continue;
            }

            $value_list[] = $item['VALUE'];
        }

        return $value_list;
    }

    /**
     * Получаем массив из id типов упаковки.
     * @param array $bx_fields
     * @return array
     */
    protected static function GetSelectedPackingTypesId($bx_fields)
    {
        $value_list = array();

        foreach ($bx_fields as $key => $item) {
            if ($item['VALUE'] == 'N') {
                continue;
            }

            $value_list[] = self::$packing_types_id[$key];
        }

        return $value_list;
    }

    /**
     * Получаем информацио о доставке на основании настроек модуля и содержимого заказа.
     * @param array $arOrder
     * @param array $arConfig
     * @return array
     */
    public static function GetDeliveryData($arOrder, $arConfig)
    {
        // Получаем КЛАДР код населенного пункта, из которого нужно доставить груз.
        $delivery_from_kladr_code = (string)$arConfig['KLADR_CODE_DELIVERY_FROM']['VALUE'];

        // Получаем КЛАДР код населенного пункта, в который нужно доставить груз.
        $delivery_to_kladr_code = self::GetCityKLADRCode($arOrder['LOCATION_TO']);

        // Получаем кол-во грузовых мест.
        $dl_api_numbers_of_cargo_places = self::GetNumbersOfCargoPlaces($arOrder, $arConfig);

        // Получаем вес.
        $dl_api_weight = self::CalculateWeight($arOrder, $arConfig);

        // Получаем объем.
        $dl_api_volume = self::CalculateVolume($arOrder, $arConfig);

        // Формируем данные для отправки.
        // Полный список параметров можно найти тут:
        // http://dev.dellin.ru/api/public/calculator/
        $dl_api_data = array(
            // Ключ API (обязательное поле).
            // string
            'appKey' => (string)$arConfig['API_KEY']['VALUE'],

            // Код КЛАДР пункта отправки (обязательное поле).
            // string
            'derivalPoint' => $delivery_from_kladr_code,

            // Необходима доставка груза от адреса.
            // boolean
            'derivalDoor' => $arConfig['IS_GOODS_LOADING']['VALUE'] == 'Y',

            // Требуются дополнительные услуги для доставки груза от адреса - боковая погрузка
            // array
            "derivalServices" => array(),

            // Код КЛАДР пункта прибытия (обязательное поле).
            // string
            'arrivalPoint' => $delivery_to_kladr_code,

            // Необходима доставка груза до адреса.
            // boolean
            'arrivalDoor' => $arConfig['IS_GOODS_UNLOADING']['VALUE'] == 'Y',

            // Требуются дополнительные услуги для доставки груза до адреса - боковая погрузка
            // array
            "arrivalServices" => array(),

            // Общий объём груза в кубических метрах (обязательный поле).
            // string
            'sizedVolume' => $dl_api_volume['sized'],

            // Общий вес груза в килограммах (обязательный поле).
            // string
            'sizedWeight' => $dl_api_weight['sized'],

            // Заявленная стоимость груза в рублях. При отсутсвии - груз не страхуется, при передаче 0 - страхуется без объявленной стоимости,
            // при передаче значения больше 0 - страхуется на указанную сумму.
            // float | integer
            'statedValue' => $arConfig['IS_INSURANCE_GOODS_WITH_DECLARED_PRICE']['VALUE'] == 'Y' ? (float)$arOrder['PRICE'] : 0,

            // Упаковать груз в упаковку?
            // array
            'packages' => self::GetSelectedPackingTypesId(array(
                'hard' => $arConfig['PACKING_FOR_GOODS_HARD'],
                'additional' => $arConfig['PACKING_FOR_GOODS_ADDITIONAL'],
                'bubble' => $arConfig['PACKING_FOR_GOODS_BUBBLE'],
                'bag' => $arConfig['PACKING_FOR_GOODS_BAG'],
                'pallet' => $arConfig['PACKING_FOR_GOODS_PALLET']
            )),

            // Количество мест, по-умолчанию расчет производится для одного места.
            // integer
            'quantity' => $dl_api_numbers_of_cargo_places
        );

        // Если есть перегруз или негаборит, то добавим эти параметры.
        if ($dl_api_volume['oversized'] || $dl_api_weight['oversized']) {
            $dl_api_data['oversizedVolume'] = $dl_api_volume['sized'];
            $dl_api_data['oversizedWeight'] = $dl_api_weight['sized'];
        }

        // Добавляем дополнительные услуги, если отмечена галочка, что необходима погрузка товара
        if ($dl_api_data['derivalDoor']) {
            $dl_api_data['derivalServices'] = self::GetAdditionalServices(array(
                $arConfig['LOADING_TYPE'],
                $arConfig['LOADING_TRANSPORT_REQUIREMENTS'],
                $arConfig['LOADING_ADDITIONAL_EQUIPMENT']
            ));
        }

        // Добавляем дополнительные услуги, если отмечена галочка, что необходима разгрузка товара
        if ($dl_api_data['arrivalDoor']) {
            $dl_api_data['arrivalServices'] = self::GetAdditionalServices(array(
                $arConfig['UNLOADING_TYPE'],
                $arConfig['UNLOADING_TRANSPORT_REQUIREMENTS'],
                $arConfig['UNLOADING_ADDITIONAL_EQUIPMENT']
            ));
        }

        return $dl_api_data;
    }

    /**
     * Проверяем код на валидность.
     * @param array $data
     * @param array $arOrder
     * @param array $arConfig
     * @return bool
     */
    protected static function IsDataValid($data, $arOrder, $arConfig)
    {
        return
            (
                $arOrder['PRICE'] > 0
            ) && (
                $data["appKey"] !== ''
            ) && (
                $data["derivalPoint"] !== '' &&
                $data["arrivalPoint"] !== ''
            ) && !(
                $arConfig["INTERCITY_HIDE"]["VALUE"] == "Y" &&
                $data["derivalPoint"] == $data["arrivalPoint"]
            );
    }

    /**
     * Формируем строку ошибки.
     * @param $errors
     * @return string
     */
    protected static function GetResponseErrors($errors)
    {
        $errors_srt = '';

        if (is_string($errors)) {
            $errors_srt = $errors;
        } elseif (count($errors) > 0) {
            // Так как приходит не массив а object(stdClass),
            // то тут немного заморочимся.
            $error_list = array();

            foreach ($errors as $key => $val) {
                $error_list[] = $key . ' - ' . $val;
            }

            $errors_srt = implode('; ', $error_list);
        }

        return $errors_srt;
    }

    /**
     * Расчет конечной стоимости.
     * @param object $response
     * @param array $arConfig
     * @return bool|float
     */
    protected static function CalculatePrice($response, $arConfig)
    {
        $total_price = false;

        $is_small_goods_price = $arConfig['IS_SMALL_GOODS_PRICE']['VALUE'] == 'Y';
        $is_try_small_goods_price_calculate = $arConfig['TRY_SMALL_GOODS_PRICE_CALCULATE']['VALUE'] == 'Y';

        if ($response->price > 0 || ($is_small_goods_price && $response->small->price > 0)) {
            // Доставка межгород
            $total_price = $response->price;

            if ($is_small_goods_price) {
                if ($response->small->price > 0) {
                    $total_price = $response->small->price + $response->small->insurance + $response->small->notify->price;
                } elseif (!$is_try_small_goods_price_calculate) {
                    $total_price = false;
                }
            }
        }

        return $total_price;
    }

    /**
     * Расчет стоимости доставки.
     * @param array $arOrder
     * @param array $arConfig
     * @return array|bool
     */
    public static function Calculate($arOrder, $arConfig)
    {
        $result = array('STATUS' => 'ERROR', 'BODY' => GetMessage('DELLIN_CALCULATE_ERROR'));
        $data = self::GetDeliveryData($arOrder, $arConfig);

        if (self::IsDataValid($data, $arOrder, $arConfig)) {
            $cache = new CPHPCache();
            $life_time = 10*60;
            $cache_id = 'DELLIN_CALCULATE|' . serialize($data) . '&' . serialize($arConfig);

            if ($cache->InitCache($life_time, $cache_id)) {
                $cache_data = $cache->GetVars();
                $result = $cache_data['VALUE'];
            } else {
                $http_client = new HttpClient();
                $http_client->setHeader('Content-Type', 'application/json', true);

                try {
                    $response = json_decode($http_client->post(self::$calculator_url, json_encode($data)));

                    if (isset($response->errors)) {
                        $result['BODY'] = self::GetResponseErrors($response->errors);
                    } elseif ($price = self::CalculatePrice($response, $arConfig)) {
                        $result['STATUS'] = 'OK';
                        $result['BODY'] = array($price, $response->time->nominative);
                    }
                } catch (Exception $e) {
                    $result['BODY'] = GetMessage('DELLIN_CONNECTION_ERROR');
                }

                $cache->StartDataCache($life_time, $cache_id);
                $cache->EndDataCache(array('VALUE' => $result));
            }
        }

        return $result;
    }
}
