<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
error_reporting(E_ALL);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once('import_excel/PHPExcel.php');
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Event;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\DiscountCouponsManager;
use Bitrix\Sale\Fuser;
use Bitrix\Sale\Order;

$GLOBALS['LOCK_HANDLER'] = true;



@set_time_limit(0);
@ignore_user_abort(true);
Loader::includeModule('sale');
Loader::includeModule('iblock');
Loader::includeModule('catalog');
Loader::includeModule('highloadblock');


class ImportProducts {

	private static $filePath = '/8888.xlsx';
	private static $iblock = 26;
	private static $iblockBrand = 27;
	private static $priceType = 24;
	private static $unitType = 6;
	private static $key = 'F2f1T5a0';
    private static $polya = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN'];
	public static function start() {

		if(\Bitrix\Main\Loader::includeModule('iblock') && \Bitrix\Main\Loader::includeModule('catalog') && self::$key == $_REQUEST['key']) {
            self::import();
		}
	}

    function getPolya($polya,$sheet,$stp) {
        global $customF;
        $customF = array();
        foreach($polya as $key => $value) {

            $product_x = $sheet->getCell($value."1")->getValue();

            if($product_x == $stp) {
                return $polya[$key];
            }

            if(strstr($product_x, 'Custom_')) {
                //   $customF[] = $product_x;

            }

        }
        //  echo '-----'.print_r($customF).'-----'.'<br>';
    }

    function getNewFiled($polya,$sheet) {


        foreach($polya as $key => $value) {

            $product_x = $sheet->getCell($value."1")->getValue();

            if(strstr($product_x, 'C')) {
                /*  echo $product_x;*/
                $customF[] = $product_x;
                /*     print_r($customF);*/
            }

        }

        return $customF ;
    }
	
	private static function import() {
		$countProductsAdded = 0;	
		$countProductsUpdated = 0;


		$xls = PHPExcel_IOFactory::load($_SERVER["DOCUMENT_ROOT"].self::$filePath);

		
		$xls->setActiveSheetIndex(0);
		$sheet = $xls->getActiveSheet();
		
		//echo "<h3>Процесс загрузки товаров инициализирован.</h3>";
		//echo "На данный момент в базе <b>".self::getCountProductsFromDB()."</b> продуктов.<br><br>";
		
		$arProductFromDB = self::getAllProductIDsFromDB();
		
		for ($i = 2; $i < $sheet->getHighestRow() + 1; $i++) {
			$product = self::getProduct($sheet, $i);
			
			$element = new CIBlockElement();
			

		}
		
		//echo "<b>Добавлено: ".$countProductsAdded."</b> продуктов.<br>";
		//echo "<b>Обновлено: ".$countProductsUpdated."</b> продуктов.<br>";
	}

	private static function getAllProductIDsFromDB() {
		$arProductResult = array();
		
		$arProducts = CIBlockElement::GetList(array("SORT"=>"ASC"), 
				array("IBLOCK_ID" => self::$iblock), 
				false, 
				false, 
				array("ID", "IBLOCK_ID", "NAME", "PROPERTY_CML2_ARTICLE", "PREVIEW_PICTURE"));
				
		while($product = $arProducts->GetNextElement()){
			$arFields = $product->GetFields();
			$arProductResult['product-'.$arFields["PROPERTY_CML2_ARTICLE_VALUE"]] = $arFields;
		}
				
		return $arProductResult;		
	}
	
	private static function getCountProductsFromDB() {
		return CIBlockElement::GetList(
                array(),
                array('IBLOCK_ID' => self::$iblock),
                array(),
                false,
                array('ID', 'NAME')
            );
	}
	
	private static function getSectionID($product) {
		$section = new CIBlockSection;
		$sectionId = 0;
		
		$groupsLevel1 = CIBlockSection::GetList(array("SORT"=>"ASC"), 
			array("IBLOCK_ID" => self::$iblock, "IBLOCK_SECTION_ID" => 0, "NAME" => $product['group_level_1']), 
			false, 
			array("ID", "NAME"));
		
		if ($group = $groupsLevel1->GetNextElement()) {
			$arFields = $group->GetFields();
			
			$sectionId = $arFields["ID"];
		} else {
			$groupLevel1Id = $section->Add(array(
				  "ACTIVE" => true,
				  "IBLOCK_SECTION_ID" => 0,
				  "IBLOCK_ID" => self::$iblock,
				  "NAME" => $product['group_level_1'],
				  ));
				  
		//	$section->Update($groupLevel1Id, array("CODE" => $groupLevel1Id));
			
			$sectionId = $groupLevel1Id;
		}
		
		$groupsLevel2 = CIBlockSection::GetList(array("SORT"=>"ASC"), 
			array(
				"IBLOCK_ID" => self::$iblock, 
				"IBLOCK_SECTION_ID" => $sectionId, 
				"NAME" => $product['group_level_2']
				), 
			false, 
			array("ID", "NAME"));
		
		if ($group = $groupsLevel2->GetNextElement()) {
			$arFields = $group->GetFields();
			
			$sectionId = $arFields["ID"];
		} else {
			$groupLevel2Id = $section->Add(
				array(
				  "ACTIVE" => true,
				  "IBLOCK_SECTION_ID" => $sectionId,
				  "IBLOCK_ID" => self::$iblock,
				  "NAME" => $product['group_level_2'],
				)
			);

            //	$section->Update($groupLevel2Id, array("CODE" => $groupLevel2Id));
			
			$sectionId = $groupLevel2Id;
		}
		
		if ($product['group_level_3']) {
			$groupsLevel3 = CIBlockSection::GetList(array("SORT"=>"ASC"), 
				array(
					"IBLOCK_ID" => self::$iblock, 
					"IBLOCK_SECTION_ID" => $sectionId, 
					"NAME" => $product['group_level_3']
					), 
				false, 
				array("ID", "NAME"));
			
			if ($group = $groupsLevel3->GetNextElement()) {
				$arFields = $group->GetFields();
				
				$sectionId = $arFields["ID"];
			} else {
				$groupLevel3Id = $section->Add(
					array(
					  "ACTIVE" => true,
					  "IBLOCK_SECTION_ID" => $sectionId,
					  "IBLOCK_ID" => self::$iblock,
					  "NAME" => $product['group_level_3'],
					)
				);

                //	$section->Update($groupLevel3Id, array("CODE" => $groupLevel3Id));
				
				$sectionId = $groupLevel3Id;
			}
		}
		
		return $sectionId;
	}

	private static function getBrandIdByProduct($product) {	
		/*$arBrands = CIBlockElement::GetList(array("SORT"=>"ASC"),
				array("IBLOCK_ID" => self::$iblockBrand, "NAME" => $product["brand"]), 
				false, 
				false, 
				array("ID", "IBLOCK_ID", "NAME"));
				
		if ($brand = $arBrands->GetNextElement()) {
			$arFields = $brand->GetFields();
			
			CIBlockElement::SetPropertyValueCode($arFields["ID"], "COUNTRY", $product['brandCountry']);
			
			return $arFields["ID"];
		} else {
			$element = new CIBlockElement;
			
			$arFields = array(
				"IBLOCK_ID" => self::$iblockBrand,
				"NAME" => $product["brand"],
				"PROPERTY_VALUES" => array(
					"COUNTRY" => $product['brandCountry'],
				),
				"ACTIVE" => "Y"
			);
			
			return $element->Add($arFields);
		}*/
	}
	
	private function getEnumPropertyIdByProduct($propertyCode, $productProperty) {
		$property_enums = CIBlockPropertyEnum::GetList(
			array("DEF"=>"DESC", "SORT"=>"ASC"), 
			array("IBLOCK_ID" => self::$iblock, "CODE" => $propertyCode));
			
		while($enum_fields = $property_enums->GetNext()) {		
			if ($enum_fields['VALUE'] == $productProperty) {				
				return $enum_fields['ID'];
			}
		}
		
		$property = CIBlockProperty::GetByID($propertyCode, self::$iblock)->GetNext();
		$PROPERTY_ID = $property['ID'];
		
		$ibpenum = new CIBlockPropertyEnum;
	/*
		$result = $ibpenum->Add(array('PROPERTY_ID' => $PROPERTY_ID, 'VALUE' => $productProperty, 'XML_ID' => uniqid()));
		
		return $result;*/
	}

    private static function getProduct($sheet, $i) {
        global $appayFieldsNew;

        $product = array();





         if(self::getPolya(self::$polya,$sheet,'UF_CITYNAME')) {
             if(trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'UF_CITYNAME').$i)->getValue())) {


                 $arHLBlock = HighloadBlockTable::getById(22)->fetch();
                 $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
                 $strEntityDataClass = $obEntity->getDataClass();

                 $rsData = $strEntityDataClass::getList(array(
                     'select' => array('UF_CITYCODE', 'UF_CITYNAME',"UF_CITYID"),
                     'filter' => array(
                         'UF_CITYNAME' => trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'UF_CITYNAME').$i)->getValue())
                     ),
                 ));
                 if ($arItem = $rsData->fetch()) {

                     $intCityCode = $arItem['UF_CITYCODE'];
                     $intCityID = $arItem['UF_CITYID'];

                 }




                 $adpes = explode(",",trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'ADPES').$i)->getValue()));
               /*  echo 'XXXX';print_r($adpes);*/
                // return;

                 $appay = array();
                 if(self::getPolya(self::$polya,$sheet,'UF_TERMINALCODE')) {
                     $appay['terminalCode'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'UF_TERMINALCODE').$i)->getValue());
                 }

                 if(self::getPolya(self::$polya,$sheet,'UF_TERMINALNAME')) {
                     $appay['terminalName'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'UF_TERMINALNAME').$i)->getValue());
                 }



                // $appay['address']['cityId'] = '195851995';
               //  $appay['address']['countryCode'] = '196025140';
                // $appay['address']['regionCode'] = '26';
                 //$appay['address']['regionName'] = 'Ставропольский';
                 if($intCityCode) {
                     $appay['address']['cityCode'] = $intCityCode;
                 }

                 if($intCityID) {
                     $appay['address']['cityId'] = trim($intCityID);
                 }


                 if(self::getPolya(self::$polya,$sheet,'UF_CITYNAME')) {
                     $appay['address']['cityName'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'UF_CITYNAME').$i)->getValue());
                 }
                 if($adpes[0]) {
                     $appay['address']['index'] = trim($adpes[0]);
                 }


                 $gopod = array();
                 $gopod = explode(" ",$adpes[2]);

                 $g = 0;
                $pos = strpos($gopod[1], "г");


                 if ($pos === false) {
                   //  echo "Строка '$findme' не найдена в строке '$mystring1'";
                 } else {
                     //echo "Строка '$findme' найдена в строке '$mystring1'";
                     //echo " в позиции $pos";
                     $g = 2;
                 }

                 $pos2 = strpos($gopod[1], "г.");

                 if ($pos2 === false) {
                     //  echo "Строка '$findme' не найдена в строке '$mystring1'";
                 } else {
                     //echo "Строка '$findme' найдена в строке '$mystring1'";
                     //echo " в позиции $pos";
                     $g = 2;
                 }

                  $pos3 = strpos($gopod[1], "город");

                 if ($pos3 === false) {
                     //  echo "Строка '$findme' не найдена в строке '$mystring1'";
                 } else {
                     //echo "Строка '$findme' найдена в строке '$mystring1'";
                     //echo " в позиции $pos";
                     $g = 2;
                 }

                  $pos4 = strpos($gopod[1], "Город");

                 if ($pos4 === false) {
                     //  echo "Строка '$findme' не найдена в строке '$mystring1'";
                 } else {
                     //echo "Строка '$findme' найдена в строке '$mystring1'";
                     //echo " в позиции $pos";
                     $g = 2;
                 }







                 $gopod2 = array();
                 $gopod2 = explode(" ",$adpes[4]);


                 $pos = strpos($gopod2[1], "г");


                 if ($pos === false) {
                     //  echo "Строка '$findme' не найдена в строке '$mystring1'";
                 } else {
                     //echo "Строка '$findme' найдена в строке '$mystring1'";
                     //echo " в позиции $pos";
                     $g = 3;
                 }

                 $pos2 = strpos($gopod2[1], "г.");

                 if ($pos2 === false) {
                     //  echo "Строка '$findme' не найдена в строке '$mystring1'";
                 } else {
                     //echo "Строка '$findme' найдена в строке '$mystring1'";
                     //echo " в позиции $pos";
                     $g = 3;
                 }

                 $pos3 = strpos($gopod2[1], "город");

                 if ($pos3 === false) {
                     //  echo "Строка '$findme' не найдена в строке '$mystring1'";
                 } else {
                     //echo "Строка '$findme' найдена в строке '$mystring1'";
                     //echo " в позиции $pos";
                     $g = 3;
                 }

                 $pos4 = strpos($gopod2[1], "Город");

                 if ($pos4 === false) {
                     //  echo "Строка '$findme' не найдена в строке '$mystring1'";
                 } else {
                     //echo "Строка '$findme' найдена в строке '$mystring1'";
                     //echo " в позиции $pos";
                     $g = 3;
                 }





                 if($g == 2) {
                     $id_me = 3;
                     $id_me2 = 4;
                 } elseif($g == 3)  {
                     $id_me = 5;
                     $id_me2 = 6;

                 } else {
                     $id_me = 4;
                     $id_me2 = 5;
                 }

                 if($adpes[$id_me]) {
                     $ul = '';
                     $ul = trim(str_replace("улица","", $adpes[$id_me]));
                     $ul = trim(str_replace("ул.","", $ul));
                     $ul = trim(str_replace("ул","", $ul));
                     $appay['address']['street'] = $ul;
                 }
                 $abbp = '';
                 if(substr($adpes[$id_me], 0, 1) == 'г') {
                     $abbp = 'г';
                 } else  if(substr($adpes[$id_me], 0, 1) == 'ш') {
                     $abbp = 'ш';
                 } else  if(substr($adpes[$id_me], 0, 6) == 'проезд') {
                     $abbp = 'проезд';
                 }  else  if(substr($adpes[$id_me], 0, 5) == 'пр-кт') {
                     $abbp = 'пр-кт';
                 } else  if(substr($adpes[$id_me], 0, 3) == 'пер') {
                     $abbp = 'пер';
                 } else  if(substr($adpes[$id_me], 0, 3) == 'б-р') {
                     $abbp = 'б-р';
                 } else  if(substr($adpes[$id_me], 0, 2) == 'пл') {
                     $abbp = 'пл';
                 } else  if(substr($adpes[$id_me], 0, 8) == 'проспект') {
                     $abbp = 'проспект';
                 }  else  if(substr($adpes[$id_me], 0, 8) == 'ул.') {
                     $abbp = 'ул.';
                 }  else  if(substr($adpes[$id_me], 0, 8) == 'ул') {
                     $abbp = 'ул.';
                 }  else  if(substr($adpes[$id_me], 0, 8) == 'улица') {
                     $abbp = 'ул.';
                 }

                 if(!$abbp) {
                     $abbp = 'ул.';
                 }

                 if($adpes[$id_me]) {
                      $appay['address']['streetAbbr'] = trim($abbp);
                  }





                 if(self::getPolya(self::$polya,$sheet,'Время работы')) {
                     $appay['schedule']['data-ml'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'Время работы').$i)->getValue());
                 }





                //$appay['address']['streetAbbr'] = 'шоссе';
                 //$appay['address']['new_adpess'] = str_replace(array("\r", "\n"),"", trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'ADPES').$i)->getValue()));
                 if($adpes[$id_me2]) {
                    $appay['address']['houseNo'] = trim(str_replace("дом","",$adpes[$id_me2]));
                 }
                 if(self::getPolya(self::$polya,$sheet,'Описание проезда')) {
                     $appay['address']['descript'] = '';
                     //$appay['address']['descript'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'Описание проезда').$i)->getValue());
                 }
                if(self::getPolya(self::$polya,$sheet,'POS1')) {
                     $appay['geoCoordinates']['latitude'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'POS1').$i)->getValue());
                }
                 if(self::getPolya(self::$polya,$sheet,'POS2')) {
                     $appay['geoCoordinates']['longitude'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'POS2').$i)->getValue());
                 }
                 if(trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'Оплата наличными').$i)->getValue())) {
                     $appay['online'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'Оплата наличными').$i)->getValue());
                 }

                 if(trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'Ограничение суммы НПП').$i)->getValue())) {
                     $appay['max_pay'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'Ограничение суммы НПП').$i)->getValue());
                 }

                 if(trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'Оплата наличными').$i)->getValue())) {
                     $appay['nalichnie'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'Оплата наличными').$i)->getValue());
                 }





                 /*echo 'MMM';*/
           /*  echo '<pre>';
                 var_dump($appay);*/

                 $appay_stp = (serialize(str_replace(array("\r", "\n"),"", $appay)));



        echo '<item>';
        //$product['akcii_moscov'] = trim($sheet->getCell("AN$i")->getValue()); //ok
        //$product['akcii_moscov'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'Sale_price').$i)->getValue()); //ok
        if(self::getPolya(self::$polya,$sheet,'UF_TERMINALCODE')) {
          echo  '<uf_terminalcode>'.$product['brand'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'UF_TERMINALCODE').$i)->getValue()).'</uf_terminalcode>'; //ok
        }
        if(self::getPolya(self::$polya,$sheet,'UF_TERMINALNAME')) {
            echo  '<uf_terminalname>'.$product['article'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'UF_TERMINALNAME').$i)->getValue()).'</uf_terminalname>'; //ok
        }
         if($intCityCode) {
             echo  '<uf_citycode>'.$intCityCode.'</uf_citycode>'; //ok
         }

        if(self::getPolya(self::$polya,$sheet,'UF_COUNTRYCODE')) {
            echo '<uf_countrycode>'.$product['group_level_1'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'UF_COUNTRYCODE').$i)->getValue()).'</uf_countrycode>'; //ok
        }
        if(self::getPolya(self::$polya,$sheet,'UF_CITYID')) {

            echo '<uf_cityid>'.$product['group_level_2'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'UF_CITYID').$i)->getValue()).'</uf_cityid>'; //ok

        }
        if(self::getPolya(self::$polya,$sheet,'UF_CITYNAME')) {
            echo "<uf_cityname>".$product['group_level_3'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'UF_CITYNAME').$i)->getValue())."</uf_cityname>"; //ok
        }
        if($appay_stp) {
            echo "<uf_data_source>".$appay_stp.'</uf_data_source>'; //ok
        //    echo "<uf_data_source>".str_replace('"',"&quot;",$appay_stp).'</uf_data_source>'; //ok
        }

     if(self::getPolya(self::$polya,$sheet,'Оплата наличными')) {
         echo "<uf_online_pay>".$product['group_level_3'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'Оплата наличными').$i)->getValue())."</uf_online_pay>"; //ok
     }


                 if(self::getPolya(self::$polya,$sheet,'gabapit_max')) {
                     echo "<uf_gabapit_max>".$appay['UF_GABAPIT_MAX'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'gabapit_max').$i)->getValue())."</uf_gabapit_max>"; //ok;
                 }

                 if(self::getPolya(self::$polya,$sheet,'max_sum')) {
                     echo "<uf_max_sum>".$appay['UF_MAX_SUM'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'max_sum').$i)->getValue())."</uf_max_sum>"; //ok;
                 }

                 if(self::getPolya(self::$polya,$sheet,'max_ves_upakovki')) { 
                     echo "<uf_max_ves_upakovki>".$appay['UF_MAX_VES_UPAKOVKI'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'max_ves_upakovki').$i)->getValue())."</uf_max_ves_upakovki>"; //ok;;
                 }

                 if(self::getPolya(self::$polya,$sheet,'max_ves_otpavki')) {
                     echo "<uf_max_ves_otpavki>".$appay['UF_MAX_VES_OTPAVKI'] = trim($sheet->getCell(self::getPolya(self::$polya,$sheet,'max_ves_otpavki').$i)->getValue())."</uf_max_ves_otpavki>"; //ok;;
                 }




        echo '</item>';

             }
         }

        /* echo '<pre>';
         print_r($appayFieldsNew);*/
        //$product['description'] = trim($sheet->getCell("AM$i")->getValue()); //ok


        return $product;
    }
}

ImportProducts::start();
