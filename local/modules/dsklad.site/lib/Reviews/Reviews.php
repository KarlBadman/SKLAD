<?
namespace Dsklad;

use Dsklad\Tools\Helpers;

class Reviews
{
    /**
     * @param $arFields
     * @throws \Exception
     */
    public static function OnAfterIBlockElementAdd(&$arFields)
    {
        if ($arFields['IBLOCK_ID'] == Config::getParam('iblock/reviews')) {
            foreach ($arFields['PROPERTY_VALUES'] as $propId => $prop) {
                if (Helpers::getCodeValue($propId) == 'SHIPMENT') {
                    Product::updateReviewsStatistic((int)$prop[0]['VALUE']);
                }
            }
        }
    }

    /**
     * @param $arFields
     * @throws \Exception
     */
    public static function OnAfterIBlockElementUpdate(&$arFields)
    {
        if ($arFields['IBLOCK_ID'] == Config::getParam('iblock/reviews')) {
            $element = \CIBlockElement::GetByID($arFields['ID'])->GetNextElement(false, false);
            if ($element->fields['IBLOCK_ID'] == Config::getParam('iblock/reviews')) {
                Product::updateReviewsStatistic((int)$element->GetProperty('SHIPMENT')['VALUE']);
            }
        }
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public static function OnBeforeIBlockElementDelete($id)
    {
        $element = \CIBlockElement::GetByID($id)->GetNextElement(false, false);
        if ($element->fields['IBLOCK_ID'] == Config::getParam('iblock/reviews')) {
            Product::updateReviewsStatistic((int)$element->GetProperty('SHIPMENT')['VALUE'], $id);
        }
    }
}