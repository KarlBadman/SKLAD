<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Highloadblock\HighloadBlockTable;

$arLinck = [];
$arHLBlock = HighloadBlockTable::getById(\Dsklad\Config::getParam('hl/search_links'))->fetch();
$obEntity = HighloadBlockTable::compileEntity($arHLBlock);
$photoEntityDataClass = $obEntity->getDataClass();

$rsData = $photoEntityDataClass::getList([]);
if ($arItem = $rsData->fetch()) {
    $arLinck[] = ['TEXT'=>$arItem['UF_TEXT_LINCK_SEARCH'],'URL'=>$arItem['UF_LINCK_SEARCH']];
}
?>

<h4>Популярно сейчас</h4>
<div class="header-search-result__list">
    <?foreach ($arLinck as $link):?>
        <a class="header-search-result__item" href="<?=$link['URL']?>"><?=$link['TEXT']?></a>
    <?endforeach;?>
</div>
