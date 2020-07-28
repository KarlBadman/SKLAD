<?php
/**
 * User: a.kobetskoy
 * Date: 18.07.2018
 */

class feeds
{
    const USERNAME = "feedgenarator";
    const PASSWD = "eDQDy5FerT3UH4IaIb7L8CUSXnjtbeg5";

    #CHECK AND REMOVE ALL UNNESESSARY
    public $SITE_NAME_DEFINE = 'Дизайн Склад';
    public $SITE_URL_DEFINE = 'https://www.dsklad.ru';
    public $CATALOG_IBLOCK_ID = 35;
    public $FOTO_1 = 15;
    public $FOTO_2 = 16;
    public $FOTO_3 = 17;
    public $FOTO_4 = 18;
    public $FOTO_5 = 19;
    public $FOTO_6 = 20;

    public $rates = array();

    #исключенные категории
    public $EXCLUDED_CATEGORIES = array(
        "180", // Услуги
        "214", // Услуги
        "181", // Доп Услуги
        "215", // Доп Услуги
        "157", // Материалы
        //"176", // Аксессуары
        "209", // Инвентарь и хозяйственные принадлежности
        "219", // Новое с сайта
        "210", // Наборы
        "192", // Тестовый комплект
        "213", // Тестовый комплект
        "199", // Оборудование (объекты основных средств)
        "211", // Оборудование (объекты основных средств)
        "201", // ЧЕРНАЯ ПЯТНИЦА
        "216", // КИБЕРПОНЕДЕЛЬНИК 2019
        "217", // КИБЕРПОНЕДЕЛЬНИК 2019
        "218", // Скидки
    );

    #REPLACE VARIABLES
    public $from = array('"', '&', '>', '<', '\'');
    public $to = array('&quot;', '&amp;', '&gt;', '&lt;', '&apos;');

    #YANDEX_DESCRIPTION_FORBIDDEN_STRINGS (пока только для названий)
    public $ya_forbidden_strings = array(
        "sale",
        "скидка",
        "распродажа",
        "дешевый",
        "подарок",
        "бесплатно",
        "акция",
        "специальная цена",
        "новинка",
        "new",
        "аналог",
        "заказ",
        "хит"
    );

    //EXTRA CATEGORIES FIX
    public $ya_extra_categories = array(
        //fix for socks
        1 => array('id'=>1, 'parent_id'=>0, 'parent_text'=>'', 'name'=>'Мебель', 'count'=>0),
        2 => array('id'=>2, 'parent_id'=>1, 'parent_text'=>'parentId="1"', 'name'=>'Фурнитура для мебели', 'count'=>0),
        //fix for chairs
        10 => array('id'=>10, 'parent_id'=>0, 'parent_text'=>'', 'name'=>'Мягкая мебель', 'count'=>0),
        11 => array('id'=>11, 'parent_id'=>10, 'parent_text'=>'parentId="10"', 'name'=>'Кресла', 'count'=>0),
        //fix for pillows
        20 => array('id'=>20, 'parent_id'=>0, 'parent_text'=>'', 'name'=>'Товары для дома', 'count'=>0),
        21 => array('id'=>21, 'parent_id'=>20, 'parent_text'=>'parentId="20"', 'name'=>'Текстиль для дома', 'count'=>0),
        22 => array('id'=>22, 'parent_id'=>21, 'parent_text'=>'parentId="21"', 'name'=>'Декоративные подушки', 'count'=>0)
    );

    //fix for light
    public $ya_light_fix_categories = array(
        177=>array('id'=>177, 'parent_id'=>212, 'parent_text'=>'parentId="212"', 'name'=>'Люстры и потолочные светильники', 'count'=>0),
        178=>array('id'=>178, 'parent_id'=>212, 'parent_text'=>'parentId="212"', 'name'=>'Настенно-потолочные светильники', 'count'=>0),
        179=>array('id'=>179, 'parent_id'=>212, 'parent_text'=>'parentId="212"', 'name'=>'Настольные лампы', 'count'=>0),
        185=>array('id'=>185, 'parent_id'=>212, 'parent_text'=>'parentId="212"', 'name'=>'Торшеры и напольные светильники', 'count'=>0)
    );

    #имя файла
    public $file_name;
    #директория. Используется только для фомрирования ссылки
    public $directory = 'i/';

    public function __construct()
    {
        if(!$this->checkauth()){
            header("HTTP/1.0 403 Forbidden");
            exit;
        }
        $_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__ . "/../"); // Master
        require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/local/php_interface/include/Utils.php"))
            require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/Utils.php");
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        set_time_limit(500);
    }


    /**
     * Проверяем авторизацию
     */
    public function checkauth () {
        if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == self::USERNAME && $_SERVER['PHP_AUTH_PW'] == self::PASSWD) {
            return true;
        }
    }

    /**
     * Параметр для яндекс фида. Вместо можно упразднить, при переводе на шаблоны и использовать #SITE_URL#
     * @return string
     */
    public function yml_root()
    {
        return empty($_SERVER['CONTEXT_DOCUMENT_ROOT']) ? $_SERVER["DOCUMENT_ROOT"] : $_SERVER['CONTEXT_DOCUMENT_ROOT'];
    }

    /**
     * Корневой путь для формирования url
     * @return string
     */
    public function root_address()
    {
        return ($_SERVER['SERVER_NAME'] == 'www.dsklad.ru' || empty($_SERVER['SERVER_NAME'])) ? $this->SITE_URL_DEFINE : 'https://' . $_SERVER['SERVER_NAME'];
    }

    /**
     * Получить кол-во товаров от которого считать цену
     * @param int $product_id
     * @return int
     */
    public function getProductCount($recommended_count = 1, $active = true)
    {
        $recommended_count = !empty($recommended_count)? (int)$recommended_count : 1;
        return ($active) ? $recommended_count : 1;
    }

    /**
     * Получить отзывы по id товара
     * @param int $prod_id
     * @return array
     */
    public function getReviews($prod_id)
    {
        // reviews
        $arOrder = array(
            'created' => 'desc'
        );
        $arSelect = array(
            'NAME', 'DETAIL_TEXT', 'DATE_CREATE', 'PROPERTY_RATING', 'ID'
        );

        $dbElement = CIBlockElement::GetList($arOrder, array('IBLOCK_ID' => 39, 'ACTIVE' => 'Y', 'PROPERTY_SHIPMENT' => $prod_id), false, false, $arSelect);
        $reviews = array();
        while ($arFields = $dbElement->GetNext()) {
            $arFields['PROPERTY_RATING_VALUE'] = (strtolower($arFields['PROPERTY_RATING_VALUE'] != 'undefined') && !empty($arFields['PROPERTY_RATING_VALUE'])) ? $arFields['PROPERTY_RATING_VALUE'] : 5;
            $reviews[] = $arFields;
        }
        //получение всех картинок(множественное св-то)
        foreach ($reviews as $key => $arItem) {
            $arImgID = array();
            $res = CIBlockElement::GetProperty(39, $arItem['ID'], "sort", "asc", array("CODE" => "PHOTO_REV"));
            while ($ob = $res->GetNext()) {
                $arImgID[] = $ob['VALUE'];
            }
            if (!empty($arImgID)) {
                $reviews[$key]['PHOTO_REV_ID'] = $arImgID;
            }
        }
        return $reviews;
    }

    /**
     * Отразить изображение
     * @param string $fileImg
     * @param string $newFile
     * @throws ImagickException
     */
    public function makeMirrorPic($fileImg, $newFile)
    {
        $imagick = new Imagick($fileImg);
        $imagick->flopImage();
        // сохраняем изображение
        $imagick->writeImage($newFile);
        unlink($imagick);
    }

    /**
     * Сформировать имя для сохраняемого файла фида
     * @param string $executedFile
     * @param string $task
     * @return string
     */
    public function generateFilename($executedFile, $task = '', $extention = 'xml')
    {
        $fileinfo = pathinfo($executedFile);
        $task = empty($task) ? $task : '_' . $task;
        $this->file_name = $fileinfo['filename'] . $task . '.' . $extention;
        $file = __DIR__ . '/' . $this->file_name;
        if (!is_file($file))
            fopen($file, "w");
        return $file;
    }

    /**
     * Удалить строки с пустыми блоками
     * 1 регулярка удалит строки с незамененными переменными, например #ITEM#
     * 2 регулярка удалит строки с пустыми данными, например <test></test>. Добавлен ! чтобы содержимое <![CDATA не отваливалось
     * @param string $string
     * @return null|string|string[]
     */
    public function removeEmptyBlocks($string)
    {
        $string = preg_replace("/^.*(#[A-Za-z_]*#).*$/m", "", $string);
        $string = preg_replace("/^.*<[^\/!]*><\/.*$/m", "", $string);
        return $string;
    }

    /**
     * Вывести информационный блок отладки
     * @param string $state
     * @param string $extrainfo
     */
    public function printInfo($state, $extrainfo = '')
    {
        print '-------------------[ ' . $state . ' ]-------------------' . PHP_EOL;
        if (!empty($extrainfo)) {
            print '--[ ' . $extrainfo . ' ]--' . PHP_EOL;
        } else {
            printf('Time ' . $state . ':' . date("Y-m-d H:i") . PHP_EOL);
        }
    }

    /**
     * Вывести ссылку на фид
     */
    public function printLink()
    {
        if (!empty($_SERVER['SCRIPT_URI'])) {
            print '<br><a href="/' . $this->directory . $this->file_name . '" target="_blank">OPEN FILE</a>' . PHP_EOL;
        }
    }

    /**
     * расчитать маржинальность товара
     * @param $price - цена товара
     * @param $cost - себестоимость товара
     * @param $currency - валюта
     * @return float|int
     */
    public function marginCalculation($price, $cost, $currency) {
        //стоимость товара в фиде - (стоимость товара в фиде* 20/120) - себестоимость
        //Себестоимость в валюте, и её нужно пересчитать по курсу на текущий день
        if(empty($this->rates[$currency])){
            $this->rates[$currency] = Utils::getBankCurrency($currency);
        }

        return $price - ($price * 20/120) - ($cost * $this->rates[$currency]);
    }
}