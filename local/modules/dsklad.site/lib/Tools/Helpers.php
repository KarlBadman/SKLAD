<?
namespace Dsklad\Tools;

class Helpers
{

    /**
     * написание слова товар в зависемости о
     *
     * @$intCount $number
     */
    public function wordProducts($intCount = 0)
    {
        if ($intCount == 0) return 'товаров';
        $strWord = 'товар';
        $intCount = substr($intCount, -2);

        if ($intCount > 20) {
            $intLastCount = substr($intCount, -1);
        } else {
            $intLastCount = $intCount;
        }

        if (($intLastCount > 1) && ($intLastCount < 5)) {
            $strWord .= 'a';
        } elseif ($intLastCount > 4 || $intLastCount == 0) {
            $strWord .= 'ов';
        }

        return $strWord;
    }

    /**
     * Склонение слов после числительных
     * http://dimox.name/plural-form-of-nouns/
     *
     * @param $number
     * @param $after
     * @return string
     */
    public static function plural_form($number, $after)
    {
        $cases = array (2, 0, 1, 1, 1, 2);
        return $after[ ($number%100>4 && $number%100<20) ? 2 : $cases[min($number%10, 5)] ];
    }

    /**
     * Получение кода свойства
     * @param $propId
     * @return string
     */
    public static function getCodeValue($propId)
    {
        $propId = (int)$propId;
        if (empty($propId)) {
            return '';
        }

        $propInfoRes = \CIBlockProperty::GetByID($propId);
        if ($propInfo = $propInfoRes->Fetch()) {
            return $propInfo['CODE'];
        }

        return '';
    }

    /**
     * Получает список подразделов раздела инфоблока
     * @param $parentId
     * @param array $arFilter
     * @param array $arSelect
     * @return array
     */
    public static function getSubSections($parentId, $arFilter = [], $arSelect = [])
    {
        if (!is_array($parentId)) {
            $parentId = [$parentId];
        }

        $db = \CIBlockSection::GetTreeList(
            array_merge(
                [
                    'ACTIVE' => 'Y',
                    'GLOBAL_ACTIVE' => 'Y'
                ],
                is_array($arFilter) ? $arFilter : []
            ),
            array_merge(
                [
                    'ID',
                    'RIGHT_MARGIN'
                ],
                is_array($arSelect) ? $arSelect : []
            )
        );
        $allSections = [];
        while ($ar = $db->Fetch()) {
            $allSections[$ar['ID']] = $ar;
        }

        $out = [];
        foreach ($parentId as $sectionId) {
            if (empty($allSections[$sectionId])) {
                continue;
            }

            $out[$sectionId] = [];
            foreach ($allSections as $section) {
                if (
                    $section['LEFT_MARGIN'] > $allSections[$sectionId]['LEFT_MARGIN']
                    && $section['RIGHT_MARGIN'] < $allSections[$sectionId]['RIGHT_MARGIN']
                ) {
                    $out[$sectionId][] = $section;
                }
            }
        }

        return $out;
    }
}