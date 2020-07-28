<?
use Bitrix\Main\Application;
require "../templates/header.php";
?>
    <div class="header clearfix">
        <h3 class="text-muted">Массовая правка цен</h3>
    </div>
<? $APPLICATION->SetPageProperty('title', 'Массовая правка цен'); ?>

    <?
    $cateogory_tag_match = array(160=>198, 161=>197);


    $request = Application::getInstance()->getContext()->getRequest();
    $productsGroup = $request->getPost('productsGroup');

    $defaultGroup = 188; //стулья как значение по умолчанию

    $productsGroup = (!empty($productsGroup)) ? $productsGroup : $defaultGroup;
    $selectedGroup = $request->getPost('selectedGroup');
    $productsGroup = (!empty($selectedGroup)) ? $selectedGroup : $productsGroup;

    $price_ids = $request->getPost('price_ids');
    foreach($price_ids as $key=>$val){
        CPrice::Update($key, array('PRICE' => (int)$val));
    }

    $groups = CIBlockSection::GetList(Array('order'=>'ASC'), Array('IBLOCK_ID'=>$IBLOCK_ID, 'GLOBAL_ACTIVE'=>'Y'), true);

    $productsresults = CIBlockElement::GetList(Array("sort"=>"asc", "name"=>"asc"),  Array("ACTIVE"=>"Y", "SECTION_ID" => $productsGroup),false, false, array('ID','PROPERTY_TAGS'));
    $products = [];
    while($product = $productsresults->GetNext()) {
        $products[$product['ID']]['NAME']=$product['NAME'];
        $res = CCatalogSKU::getOffersList(
            $product['ID'], // массив ID товаров
            $iblockID = 0, // указываете ID инфоблока только в том случае, когда ВЕСЬ массив товаров из одного инфоблока и он известен
            $skuFilter = array('ACTIVE' => 'Y'), // дополнительный фильтр предложений. по умолчанию пуст.
            $fields = array('NAME', 'CATALOG_GROUP_1', 'CATALOG_GROUP_2'),  // массив полей предложений. даже если пуст - вернет ID и IBLOCK_ID
            $propertyFilter = array('CODE' => array('RECOMMENDED_QUANTITY_FOR_SALE')) /* свойства предложений. имеет 2 ключа:
                               ID - массив ID свойств предложений
                                      либо
                               CODE - массив символьных кодов свойств предложений
                                     если указаны оба ключа, приоритет имеет ID*/
        );
        $products[$product['ID']]['REGULAR_PRICES'] = $product['PROPERTY_TAGS_VALUE'];
        $products[$product['ID']]['OFFERS'] = $res[$product['ID']];
        foreach ($res[$product['ID']] as $id=>$offer) {
            $dbPricesProduct = CPrice::GetListEx(
                array(),
                array('PRODUCT_ID' => $id),
                false,
                false,
                array("ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO")
            );

            while ($arPricesProduct = $dbPricesProduct->Fetch()) {
                $products[$product['ID']]['OFFERS'][$id]['RANGE_PRICES'][$arPricesProduct['QUANTITY_FROM'] . '_' . $arPricesProduct['QUANTITY_TO']][$arPricesProduct['CATALOG_GROUP_ID']] = $arPricesProduct;
            }
        }
    }

    $dbPriceType = CCatalogGroup::GetList(array("SORT" => "ASC"), false, false, false, array('ID', 'NAME'));
    $price_types = array();
    while ($arPriceType = $dbPriceType->Fetch()) {
        $price_types[] = $arPriceType;
    }
    ?>
    <form method="post" id="changeGroup">
        <div class="form-group row">
            <label for="productsGroup" class="col-sm-3 col-form-label">Категория товаров:</label>
            <div class="col-sm-9">
                <select class="form-control" id="productsGroup" name="productsGroup" onchange="$('#changeGroup').submit()">
                    <? while($element = $groups->Fetch()){
                        if( $element["ELEMENT_CNT"] > 0 && $element['IBLOCK_ID'] == 35) {?>
                        <option value="<?=$element['ID']?>" <? if($productsGroup == $element['ID']){?>selected<? }?>><?=$element['NAME']?></option>
                    <? }
                    } ?>
                </select>
            </div>
        </div>
    </form>
    <form method="post" action="#">
        <table class="table table-striped text-center">
            <thead>
            <tr>
                <th scope="col" class="text-left">Позиция</th>
                <!--th scope="col">Цены</th-->
                <th scope="col">Диапазоны цен</th>
            </tr>
            </thead>
            <tbody>
            <? foreach($products as $productkey => $product){ ?>
                <tr>
                    <th scope="row" class="text-left" data-placement="right" data-html="true"><?=$product['NAME']?></th>
                    <? /*?>
                        <td>
                            <a class="collapsed" data-toggle="collapse" href="#collapsePrices<?=$productkey;?>" role="button" aria-expanded="false" aria-controls="collapseExample'. $fetch['id'] .'">развернуть</a>
                            <div class="collapse" id="collapsePrices<?=$productkey;?>">
                                <div class="card card-body">
                                    <table class="table table-striped text-center">
                                        <thead>
                                        <tr>
                                            <th scope="col">Торговое Предложение</th>
                                            <th scope="col">Базовая</th>
                                            <th scope="col">Безбазовая</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row" class="text-left" data-placement="right" data-html="true"><?=$product['NAME']?></th>
                                                <td><input type="text" name="transaction" value="129993.01" placeholder="ГДЕ ЦЕНА?"></td>
                                                <td><input type="text" name="transaction" value="129993.02" placeholder="ГДЕ ЦЕНА?"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
*/?>
                        <td>
                            <a class="collapsed" data-toggle="collapse" href="#collapsePricesRanges<?=$productkey;?>" role="button" aria-expanded="false" aria-controls="collapseExample'. $fetch['id'] .'">развернуть</a>
                            <div class="collapse" id="collapsePricesRanges<?=$productkey;?>">
                                <div class="card card-body">
                                    <table class="table table-striped text-center">
                                        <thead>
                                        <tr>
                                            <th scope="col">Торговое Предложение</th>
                                            <th>Цены в диапазонах</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <? foreach($product['OFFERS'] as $offer){ ?>
                                            <tr>
                                                <th scope="row" class="text-left" data-placement="right" data-html="true"><?=$offer['NAME']?></th>
                                                <td>

                                                        <table class="table table-striped text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">Диапазон</th>
                                                                    <? foreach ($price_types as $price_type) {?>
                                                                        <th scope="col"><?=$price_type["NAME"]?></th>
                                                                    <? }?>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($offer['RANGE_PRICES'] as $item) { ?>
                                                                <tr>

                                                                <td><?=array_values($item)[0]['QUANTITY_FROM'] . ' - ' . array_values($item)[0]['QUANTITY_TO']?></td>
                                                                    <? foreach ($price_types as $price_type) {
                                                                        if(!empty($item[$price_type['ID']]['ID'])) {?>
                                                                        <td><input type="number" name="price_ids[<?=$item[$price_type['ID']]['ID']?>]" value="<?=(int)$item[$price_type['ID']]['PRICE']?>" placeholder="ГДЕ ЦЕНА?"></td>
                                                                    <? } else{?>
                                                                            <td></td>
                                                                        <? }
                                                                    }?>
                                                                </tr>
                                                                <?}?>
                                                            </tbody>
                                                        </table>
                                                </td>
                                            </tr>
                                        <? }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    <?// }?>
                </tr>
            <? } ?>
            </tbody>
        </table>
        <input type="hidden" name="selectedGroup" value="<?=$productsGroup?>">
        <input type="submit" name="save" value="Сохранить" class="btn btn-primary">
    </form>

<?require "../templates/footer.php"?>