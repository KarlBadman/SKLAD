<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;
$APPLICATION->SetPageProperty('title', 'Выгрузить архив снимков');
Loader::includeModule('highloadblock');

function getImagesFromHL($filter, $limit='', $offset='')
{
    $images = array();
    $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
    $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
    $photoEntityDataClass = $obEntity->getDataClass();

    $rsData = $photoEntityDataClass::getList(array(
        'select' => array('*'),
        'filter' => $filter,
        'limit' => $limit,
        'offset' => $offset
    ));

    while ($arItem = $rsData->Fetch()) {
        if ($arItem['UF_FILE']) {
            $sourceImage = CFile::GetFileArray($arItem['UF_FILE']);
            $arImage = CFile::ResizeImageGet($sourceImage, array('width' => 264, 'height' => 264), BX_RESIZE_IMAGE_PROPORTIONAL, false, array('name' => 'sharpen', 'precision' => 15));
            $images[] = array(
                'id' => $arItem['ID'],
                'thumb' => $arImage['src'],
                'src' => $sourceImage['SRC'],
                'width' => $sourceImage['WIDTH'],
                'height' => $sourceImage['HEIGHT'],
                'UF_FILE' => $arItem['UF_FILE'],
                'swopped' => str_replace('upload/uf', 'upload/swo/uf', $sourceImage['SRC']),
            );
        }
    }
    return $images;
}

    $request = Application::getInstance()->getContext()->getRequest();
    $raw_product_list = $request->getPost('product_list');
    $product_list = explode("\n", str_replace("\r", "", $raw_product_list ));
    if(!empty($raw_product_list)){
        $arFilter = array('IBLOCK_ID' => 35, 'DEPTH_LEVEL' => 1, '!%CODE' => array('discounts', 'chernaya', 'kiber'));
        $rsSections = CIBlockSection::GetList(array('LEFT_MARGIN' => 'ASC'), $arFilter);
        while ($arSection = $rsSections->Fetch()) {
            $section_list[$arSection['ID']] = $arSection['NAME'];
        }

        $rsCatProd = \CCatalogProduct::GetList(
            array(),
            array('ELEMENT_NAME' => $product_list ),
            false,
            false,
            array('ID', 'ELEMENT_NAME')
        );
        $products = $products_ids = array();
        while ($ar_res = $rsCatProd->Fetch()) {
            $products[$ar_res['ID']] = $ar_res['ELEMENT_NAME'];
            $products_ids[] = $ar_res['ID'];

            $product_sections  = \Bitrix\Iblock\SectionElementTable::getList(array(
                'select' => array('IBLOCK_SECTION_ID'),
                'filter' => array('IBLOCK_ELEMENT_ID' =>$ar_res['ID']),
            ))->fetchAll();
            $product_sections = array_filter( array_map(function($item) use ($section_list) {return in_array($item['IBLOCK_SECTION_ID'], array_keys($section_list)) ? $section_list[$item['IBLOCK_SECTION_ID']] : false;}, $product_sections) , 'strlen' );
            if(count($product_sections) == 1){
                $product_sections = array_values($product_sections)[0];
            } else {
                die('Товар привязан больше чем к одной категории. Устраните неоднозначность');
            }
        }

        $offers = CCatalogSKU::getOffersList(
            $products_ids,
            '',
            array("ACTIVE" => "Y"),
            ///array("AVAILABLE" => "Y", "ACTIVE" => "Y"),
            array("IBLOCK_ID", "ID", "CATALOG_QUANTITY", "NAME", "DETAIL_PAGE_URL"),
            array('CODE' =>
                array(
                    'FOTOGRAFIYA_1',
                    'FOTOGRAFIYA_2',
                    'FOTOGRAFIYA_3',
                    'FOTOGRAFIYA_4',
                    'FOTOGRAFIYA_5',
                    'FOTOGRAFIYA_6',
                    'KOD_TSVETA'
                )
            )
        );

        $arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/foto_1'))->fetch();
        $obEntity = HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();
        $filesdirectory = 'files';
        $folder_path = __DIR__ . '/' . $filesdirectory . '/' . $product_sections;
        if (!file_exists($folder_path)) {
            mkdir($folder_path , 0775, true);
        }

        foreach($offers as $parent => $offer) {
            $parent_directory = $folder_path . '/' . $products[$parent];
            if (!mkdir($parent_directory , 0775)) {
                die('Не удалось создать директории 1 уровня.. ' . $parent_directory );
            }
            foreach($offer as $color) {
                $i = 0;
                $color_name = preg_match('/\(([^\)]*)\)/', $color['NAME'], $output_array);
                $color_name = $output_array[1];
                $distination_directory = $parent_directory   . '/' . mb_convert_case($color_name, MB_CASE_TITLE, "UTF-8");
                if (!mkdir($distination_directory, 0775)) {
                    die('Не удалось создать директории 2 уровня.. ' . $distination_directory);
                }
                for ($i = 1; $i < 7; ++$i) {
                    if (!empty($color['PROPERTIES']['FOTOGRAFIYA_' . $i]['~VALUE'])) {
                        $rsData = $strEntityDataClass::getList(array(
                            'select' => array('UF_FILE'),
                            'filter' => array('UF_XML_ID' => $color['PROPERTIES']['FOTOGRAFIYA_' . $i]['~VALUE']),
                            'limit' => '1',
                        ));
                        if ($arItem = $rsData->fetch()) {
                            if ($arItem['UF_FILE']) {
                                $source = CFile::GetFileArray($arItem['UF_FILE']);
                                $newfile = $distination_directory . '/' . $products[$parent] . ' ' . $color_name . ' ' . $i . '.' . explode('.', $source['SRC'])[1];
                                if (!copy($_SERVER['DOCUMENT_ROOT'] . '/' . $source['SRC'], $newfile)) {
                                    echo "не удалось скопировать $file...\n";
                                }
                            }
                        }
                    }
                }
            }
        }

        class FlxZipArchive extends ZipArchive
        {
            public function addDir($location, $name, $array_to_delete = array())
            {
                $this->addEmptyDir($name);
                return $this->addDirDo($location, $name, $array_to_delete);
            }
            private function addDirDo($location, $name, $array_to_delete = array())
            {
                $name .= '/';
                $location .= '/';
                $dir = opendir ($location);
                while ($file = readdir($dir))
                {
                    if ($file == '.' || $file == '..') continue;
                    $do = (filetype( $location . $file) == 'dir') ? 'addDir' : 'addFile';
                    if(is_dir($location . $file)){
                        $array_to_delete['dir'][] = $location . $file;
                    } else {
                        $array_to_delete['files'][] = $location . $file;
                    }

                    if($do == 'addDir'){
                        $array_to_delete = $this->$do($location . $file, $name . $file, $array_to_delete);
                    } else {
                        $this->$do($location . $file, $name . $file);
                        $array_to_delete = $array_to_delete;
                    }
                }
                return $array_to_delete;
            }
        }

        $the_folder = __DIR__ . '/' . $filesdirectory;
        $archive_file_name = 'imgarchive.zip';
        $zip_file_name = __DIR__ . '/' . $archive_file_name;
        if (!file_exists($zip_file_name)) {
            ob_clean();
            $za = new FlxZipArchive;
            $res = $za->open($zip_file_name, ZipArchive::CREATE);
            if ($res === TRUE) {
                $array_to_delete = $za->addDir($the_folder, basename($the_folder));
                $za->close();
            } else {
                die('Could not create a zip archive');
            }
        }
        if (file_exists($zip_file_name)) {
            foreach($array_to_delete['files'] as $deleting){
                if(!unlink($deleting)){
                    die('Проблема формирования архива на этапе зачистки временных файлов. Обратитесь к разработчикам.');
                }
            }
            foreach(array_reverse($array_to_delete['dir']) as $deleting){
                if(!rmdir($deleting)){
                    die('Проблема формирования архива на этапе зачистки временных файлов. Обратитесь к разработчикам.');
                }
            }

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $archive_file_name . '"');
            header('Expires: 0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($zip_file_name));
            if(readfile($zip_file_name)){
                unlink($zip_file_name);
            }
            exit;
        } else {
            exit("Could not find Zip file to download");
        }
    }
//сохранять имя пользователя от которого была инициализация. Писать в битрикс евентс
require "../templates/header.php"; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
    <div class="header clearfix">
        <h3 class="text-muted">Архивировать снимки по списку товаров</h3>
    </div>

    <div class="alert alert-info " role="alert">
        <h4 class="alert-heading">Описание</h4>
        <p>Сервис позволяет скачать архив снимков по запрошенным названиям товаров.</p>
        <p>Осторожно заполните список товаров нужными названиями, где каждое название с новой строки.</p>
    </div>
    <form method="post" id="changeGroup" >
        <textarea class="form-control" id="product_list" rows="25" name="product_list"><?=$raw_product_list?></textarea>
        <button type="submit" class="btn btn-primary mt-5">Собрать архив и скачать</button>
    </form>
<?require "../templates/footer.php"?>