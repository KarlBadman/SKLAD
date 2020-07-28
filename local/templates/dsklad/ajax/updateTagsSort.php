<?
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include.php';
use Bitrix\Main\Context;
use Bitrix\Main\Web\Json;

$request = Context::getCurrent()->getRequest();

$id = $request->get('id');

if (!$request->isAjaxRequest()) {
    echo 'error';
    die;
}

if(!empty($id)) {
    $currentSort = 0;
    $dbItems = \Bitrix\Iblock\ElementTable::getList(array(
        'select' => array('SORT'),
        'filter' => array('IBLOCK_ID' => \Dsklad\Config::getParam('iblock/tags'), 'ID' => $id)
    ));
    while ($arItem = $dbItems->fetch()){
        $currentSort = $arItem['SORT'];
    }

    $element = new CIBlockElement();
    $currentSort++;
    $res = $element->Update($id, ['SORT' => $currentSort]);

    if($res) {
        $result = ['status' => 'ok'];
    }
    else {
        $result = ['status' => 'error', 'message' => $element->LAST_ERROR];
    }

}
else {
    $result = ['status' => 'error', 'message' => 'empty id element'];

}

echo Json::encode($result);