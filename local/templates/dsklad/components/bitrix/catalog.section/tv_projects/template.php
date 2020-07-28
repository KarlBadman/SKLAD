<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);
?>
<div class="list">
    <?
    $strElementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
    $strElementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
    $arElementDeleteParams = array('CONFIRM' => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));
    foreach ($arResult['ITEMS'] as $arItem) {
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
        $strMainID = $this->GetEditAreaId($arItem['ID']);
        ?>
        <div class="item" id="<?= $strMainID ?>">
            <span style="background: url('<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>') center center;" class="play">
                <span><i></i></span>
            </span>
            <p><b><?= $arItem['NAME'] ?></b> <?= $arItem['PREVIEW_TEXT'] ?></p>
        </div>
        <?
    }
    ?>
</div>
