<?
/**
 * feed for google reviews
 * User: a.kobetskoy
 * Date: 18.07.2018
 * @see https://developers.google.com/product-review-feeds/schema/#deleted_reviews
 * @see https://developers.google.com/product-review-feeds/sample/
 * @see https://www.freeformatter.com/xml-validator-xsd.html
 */

#@TODO move to templates in module settings
$main_template = '<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns:vc="http://www.w3.org/2007/XMLSchema-versioning"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xsi:noNamespaceSchemaLocation=
 "http://www.google.com/shopping/reviews/schema/product/2.2/product_reviews.xsd">
    <publisher>
        <name>#SITE_NAME#</name>
        <favicon>#FAVICON#</favicon>
    </publisher>
    <reviews>#REVIEWS#</reviews>
    </feed>';

$reviews_template = '
<review>
    <review_id>#REVIEW_ID#</review_id>
    <reviewer>
        <name>#REVIEWER_NAME#</name>
    </reviewer>
    <review_timestamp>#TIMESTAMP#</review_timestamp>
    <content>#REVIEW_TEXT#</content>
    <review_url type="group">#REVIEW_URL#</review_url>
    <reviewer_images>#IMAGES#</reviewer_images>
    <ratings>
        <overall min="1" max="5">#RATING#</overall>
    </ratings>
    <products>
        <product>
            <product_ids>
                <skus>
                    <sku>#SKU#</sku>
                </skus>
            </product_ids>
            <product_name>#PRODUCT_NAME#</product_name>
            <product_url>#PRODUCT_URL#</product_url>
        </product>
    </products>
</review>';

$images_templates = '
<reviewer_image>
            <url>#IMAGE_URL#</url>
</reviewer_image>' . PHP_EOL;

require_once(__DIR__ . '/feeds.php');
$feeds = new feeds();

$task = @$argv[1];

#@TODO move to options in module settings
$OPTIONS = array(
    '#SITE_NAME#' => $feeds->SITE_NAME_DEFINE,
    '#FAVICON#' => $feeds->SITE_URL_DEFINE . '/favicon.ico'
);

$feeds->printInfo('START');
$categoryArr = array();
$tovars = array();
$reviews_list = '';

// получаем категории
$categoryList = CIBlockSection::GetList(
    array("SORT" => "ASC"),
    array(
        "IBLOCK_ID" => $feeds->CATALOG_IBLOCK_ID,
        "ACTIVE" => "Y",
        "!ID" => $feeds->EXCLUDED_CATEGORIES
        //из каталога фильтр
    ),
    array("ELEMENT_SUBSECTIONS" => "Y", "CNT_ACTIVE" => "Y"),
    ///array("ELEMENT_SUBSECTIONS" => "Y"),
    array(),
    false
);

while ($ar_result = $categoryList->GetNext()) {
    if ($ar_result["ELEMENT_CNT"] > 0) {
        $categoryArr[$ar_result["ID"]] = array(
            "id" => $ar_result["ID"],
            "parent_id" => $ar_result["IBLOCK_SECTION_ID"] ? $ar_result["IBLOCK_SECTION_ID"] : 0,
            "parent_text" => $ar_result["IBLOCK_SECTION_ID"] ? "parentId=\"" . $ar_result["IBLOCK_SECTION_ID"] . "\"" : "",
            "name" => $ar_result["NAME"],
            "count" => 0
        );
        $cat_id[] = $ar_result["ID"];
    }
}

$CIBlockElement = CIBlockElement::GetList(
    $arOrder = Array("SORT" => "ASC"),
    ///$arFilter = Array("IBLOCK_ID" =>$feeds->CATALOG_IBLOCK_ID,"SECTION_ID" => $cat_id),
    $arFilter = Array("IBLOCK_ID" => $feeds->CATALOG_IBLOCK_ID, "ACTIVE" => "Y", "SECTION_ID" => $cat_id),
    $arGroupBy = false,
    $arNavStartParams = false,
    $arSelectFields = Array(
        "NAME",
        "IBLOCK_SECTION_ID",
        "CODE",
        "IBLOCK_ID",
        "ID",
        "DETAIL_TEXT",
        "DETAIL_PAGE_URL",
        "PROPERTY_CML2_ARTICLE"
    )
);

while ($ob = $CIBlockElement->GetNextElement()) {
    $ar_result = $ob->GetFields();
    $ar_reviews = $feeds->getReviews($ar_result['ID']);
    if (empty($ar_reviews))
        continue;
    $tovars[$ar_result['ID']] = $ar_result;
    $tovars[$ar_result['ID']]['REVIEWS'] = $ar_reviews;
}
unset($ar_result, $ar_reviews);

if (is_writable($file = $feeds->generateFilename(__FILE__))) {
    foreach ($tovars as $key => $values) {
        foreach ($values['REVIEWS'] as $review) {
            $images_list = '';
            $REVIEW_DATA['#REVIEW_ID#'] = $review['ID'];
            $REVIEW_DATA['#REVIEWER_NAME#'] = $review['NAME'];
            $REVIEW_DATA['#TIMESTAMP#'] = date("Y-m-d\TH:i:s\Z", strtotime($review['DATE_CREATE']));
            $REVIEW_DATA['#REVIEW_TEXT#'] = html_entity_decode(strip_tags($review['DETAIL_TEXT']));
            $REVIEW_DATA['#REVIEW_URL#'] = $feeds->root_address() . $values['DETAIL_PAGE_URL'] . "#tab-reviews";
            $REVIEW_DATA['#RATING#'] = $review['PROPERTY_RATING_VALUE'];
            $REVIEW_DATA['#SKU#'] = $values['PROPERTY_CML2_ARTICLE_VALUE'];
            $REVIEW_DATA['#PRODUCT_NAME#'] = $values['NAME'];
            $REVIEW_DATA['#PRODUCT_URL#'] = $feeds->root_address() . $values['DETAIL_PAGE_URL'];
            $strImgBig = '';
            if (!empty($review['PHOTO_REV_ID'][0])) {
                foreach ($review['PHOTO_REV_ID'] as $arImgMore) {
                    $strImgBig = $feeds->root_address() . \CFile::ResizeImageGet($arImgMore, array('width' => 700, 'height' => 450), BX_RESIZE_IMAGE_PROPORTIONAL)['src'];
                    if (!empty($strImgBig)) {
                        $images_list .= str_replace('#IMAGE_URL#', $strImgBig, $images_templates);
                    }
                }
            }

            $REVIEW_DATA = str_replace(array_keys($REVIEW_DATA), array_values($REVIEW_DATA), $reviews_template);
            $splitted = explode('#IMAGES#', $REVIEW_DATA);

            $REVIEW_DATA = $splitted[0] . $images_list . $splitted[1];
            unset($splitted);
            $reviews_list .= $feeds->removeEmptyBlocks($REVIEW_DATA);
            unset($REVIEW_DATA);
        }
    }

    $main_template = str_replace(array_keys($OPTIONS), array_values($OPTIONS), $main_template);
    $splitted = explode('#REVIEWS#', $main_template);

    $fp = fopen($file, "w");
    fwrite($fp, $splitted[0] . $reviews_list . $splitted[1]);
    fclose($fp);
}

unset($tovars, $key, $values);

$feeds->printInfo('END');
$feeds->printLink();
?>