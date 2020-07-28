<?require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
$id = intval($_POST["id"]);
if($id>0){
	if(CModule::IncludeModule("sale")){
		if (!CSaleBasket::Delete($id))
mail('professor26@mail.ru','id',print_r($id,true));
			// echo 'error!';
	}
}	
?>