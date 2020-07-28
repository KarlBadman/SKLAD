<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<form method="post">
    <input name="IBLOCK_ID" placeholder="ID инфоблока">
    <input name="SECTION_ID" placeholder="ID раздела">
    <input type="submit" value="OK">
</form>

<?if(!empty($_REQUEST['IBLOCK_ID']) && !empty($_REQUEST['SECTION_ID'])):?>
    <style>
        td{
            padding: 10px;
        }
    </style>
    <?
    $arFields = array();
    $arSelect = Array("ID", "NAME", "DETAIL_PAGE_URL");
    $arFilter = Array("IBLOCK_ID"=>IntVal($_REQUEST['IBLOCK_ID']), "IBLOCK_SECTION_ID"=>IntVal($_REQUEST['SECTION_ID']));
    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
    while($ob = $res->GetNextElement()){
        $arFields[]= $ob->GetFields();
    }
    ?>
    <table>
        <thead>
        <tr>
            <td>
                ID
            </td>
            <td>
                NAME
            </td>
            <td>
                URL
            </td>
        </tr>
        </thead>
        <tbody>
            <?foreach ($arFields as $item):?>
                <tr>
                    <td>
                        <?=$item['ID']?>
                    </td>
                    <td>
                        <?=$item['NAME']?>
                    </td>
                    <td>
                        https://www.dsklad.ru<?=$item['DETAIL_PAGE_URL']?>
                    </td>
                </tr>
            <?endforeach;?>
        </tbody>
    </table>
<?endif;?>