<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if($USER->IsAuthorized() || $arParams["ALLOW_AUTO_REGISTER"] == "Y")
{
	if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y")
	{
		if(strlen($arResult["REDIRECT_URL"]) > 0)
		{
			$APPLICATION->RestartBuffer();
			?>
			<script type="text/javascript">
				window.top.location.href='<?=CUtil::JSEscape($arResult["REDIRECT_URL"])?>';
			</script>
			<?
			die();
		}

	}
}
?>

<?if (!empty($arResult['ORDER_DATA']['ORDER_PRCIE'])) $_SESSION['ORDER_PRICE'] = $arResult['ORDER_DATA']['ORDER_PRICE'];?>
<a name="order_form"></a>

<div id="order_form_div" class="order-checkout">

<NOSCRIPT>
	<div class="errortext"><?=GetMessage("SOA_NO_JS")?></div>
</NOSCRIPT>

    <?
    $newResult = $APPLICATION->IncludeComponent( // Компонент кастылей для заказа
        "dsklad:crutch_for_order",
        "",
        Array(
            "COMPOSITE_FRAME_MODE" => "A",
            "COMPOSITE_FRAME_TYPE" => "AUTO",
            "PARAMS" => $arParams,
            "RESULT" => $arResult,
        ),
        $component
    );

    if($newResult) {
        foreach ($newResult as $key=>$val){
            $arResult[$key] = $val;
        }
    };

    include ($_SERVER["DOCUMENT_ROOT"].$templateFolder."/functions_lang.php");
    ?>

<?
if (!function_exists("getColumnName"))
{
	function getColumnName($arHeader)
	{
		return (strlen($arHeader["name"]) > 0) ? $arHeader["name"] : GetMessage("SALE_".$arHeader["id"]);
	}
}

if (!function_exists("cmpBySort"))
{
	function cmpBySort($array1, $array2)
	{
		if (!isset($array1["SORT"]) || !isset($array2["SORT"]))
			return -1;

		if ($array1["SORT"] > $array2["SORT"])
			return 1;

		if ($array1["SORT"] < $array2["SORT"])
			return -1;

		if ($array1["SORT"] == $array2["SORT"])
			return 0;
	}
}
?>

<?
// Масив точек для карты при ajax
if($_POST["is_ajax_post"] == "Y") {
    if (is_array($arResult['mapParams']['PLACEMARKS']) && !empty($arResult['mapParams']['PLACEMARKS'])) {

        $arPlas["type"] = "FeatureCollection";
        foreach ($arResult['mapParams']["PLACEMARKS"] as $plas) {

            if ($plas['IS_TERMINAL'] == 'Y') {
                $color = !empty($arParams['COLOR_PLAS_TERMINAL']) ? $arParams['COLOR_PLAS_TERMINAL'] : '#1ab500';
                $preset = !empty($arParams['COLOR_PLAS_TERMINAL']) ? $arParams['COLOR_PLAS_TERMINAL'] : 'islands#greenDotIcon';
            } else {
                $color = !empty($arParams['COLOR_PLAS']) ? $arParams['COLOR_PLAS'] : '#1d97ff';
                $preset = !empty($arParams['TYPE_PLAS_TERMINAL']) ? $arParams['TYPE_PLAS_TERMINAL'] : 'islands#blueDotIcon';
            }

            $arPlas["features"][] = array(
                "type" => "Feature",
                "id" => $plas['TERMINAL'],
                "geometry" => array(
                    "type" => "Point",
                    "coordinates" => array($plas["LAT"], $plas["LON"])
                ),
                "properties" => array(
                    "balloonContent" => $plas["TEXT"],
                    "hintContent" => strtok(substr($plas["TEXT"], strpos($plas["TEXT"], '>') + 1), '<'),
                    "clusterCaption" => strtok(substr($plas["TEXT"], strpos($plas["TEXT"], '>') + 1), '<')
                ),
                "options" => array(
                    "iconColor" => $color,
                    "preset" => $preset,

                )
            );
        }
    } else {
        $arPlas = false;
    }
}
?>

<div class="bx_order_make">
	<?
	if(!$USER->IsAuthorized() && $arParams["ALLOW_AUTO_REGISTER"] == "N")
	{
		if(!empty($arResult["ERROR"]))
		{
			foreach($arResult["ERROR"] as $v)
				echo ShowError($v);
		}
		elseif(!empty($arResult["OK_MESSAGE"]))
		{
			foreach($arResult["OK_MESSAGE"] as $v)
				echo ShowNote($v);
		}


		include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/auth.php");
	}
	else
	{
		if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y" || $arResult["NEED_REDIRECT"] == "Y")
		{

			if(strlen($arResult["REDIRECT_URL"]) == 0)
			{
			    if(strripos($_SERVER['REQUEST_URI'],'thankyou') === false){
                    header('Location: /order/thankyou/'.$arResult['ORDER_ID']);
                }else{
                    include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/confirm.php");
                }
			}
		}
		else
		{
			?>
			<script type="text/javascript">

			<?if(CSaleLocation::isLocationProEnabled()):?>

				<?
				// spike: for children of cities we place this prompt
				$city = \Bitrix\Sale\Location\TypeTable::getList(array('filter' => array('=CODE' => 'CITY'), 'select' => array('ID')))->fetch();
				?>

				BX.saleOrderAjax.init(<?=CUtil::PhpToJSObject(array(
					'source' => $this->__component->getPath().'/get.php',
					'cityTypeId' => intval($city['ID']),
					'messages' => array(
						'otherLocation' => '--- '.GetMessage('SOA_OTHER_LOCATION'),
						'moreInfoLocation' => '--- '.GetMessage('SOA_NOT_SELECTED_ALT'), // spike: for children of cities we place this prompt
						'notFoundPrompt' => '<div class="-bx-popup-special-prompt">'.GetMessage('SOA_LOCATION_NOT_FOUND').'.<br />'.GetMessage('SOA_LOCATION_NOT_FOUND_PROMPT', array(
							'#ANCHOR#' => '<a href="javascript:void(0)" class="-bx-popup-set-mode-add-loc">',
							'#ANCHOR_END#' => '</a>'
						)).'</div>'
					)
				))?>);

			<?endif?>

			var BXFormPosting = false;

			function submitForm(val)
			{
                BX.saleOrderAjax.submitForm(val,<?=CSaleLocation::isLocationProEnabled()?>,<?=CUtil::PhpToJSObject($arParams['MAP_DELIVERY'])?>);
			}

			function SetContact(profileId)
			{
				BX("profile_change").value = "Y";
				submitForm();
			}

			</script>
			<?if($_POST["is_ajax_post"] != "Y")
			{
				?><form action="/order/" method="POST" name="ORDER_FORM" id="ORDER_FORM" enctype="multipart/form-data" data-page-type="order-base">
				<?=bitrix_sessid_post()?>
				<div id="order_form_content">
				<?
			}
			else
			{
				$APPLICATION->RestartBuffer();
			}

			if($_REQUEST['PERMANENT_MODE_STEPS'] == 1)
			{
				?>
				<input type="hidden" name="PERMANENT_MODE_STEPS" value="1" />
				<?
			}

			if(!empty($arResult["ERROR"]) && $arResult["USER_VALS"]["FINAL_STEP"] == "Y")
			{
				foreach($arResult["ERROR"] as $v)
					echo ShowError($v);
				?>
				<script type="text/javascript">
					top.BX.scrollToNode(top.BX('ORDER_FORM'));
				</script>
				<?
			}?>

            <?if($_POST["is_ajax_post"] == "Y"):
                if(!empty($arResult['TERMINAL'])){
                    $terminalOk = 'Y';
                }else{
                    $terminalOk = 'N';
                }?>
                <script>
                    parent.window.plasmark = <?=\Bitrix\Main\Web\Json::encode($arPlas)?>; // масив точек
                    parent.window.terminalOk = '<?=$terminalOk?>'; // Есть ли терминал
                </script>
            <?endif;?>
           <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/functions.php");?>
            <div id="order_wrapper">
                <div class="basket__page" id="order_form_div">
                    <div class="default">
                        <section class="heading">
                            <ul itemscope="" itemtype="http://schema.org/BreadcrumbList" class="breadcrumbs__widget">
                                <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                                    <a itemprop="item" href="/">
                                        <span itemprop="name">Главная</span>
                                    </a>
                                    <meta itemprop="position" content="1">
                                </li>
                                <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                                    <a class="hidden" itemprop="item" href="/order/"></a>
                                    <span>
                                        <span itemprop="name">Оформление заказа</span>
                                    </span>
                                    <meta itemprop="position" content="2">
                                </li>
                            </ul>
                            <div class="title">
                                <h1 style="display: inline-block;">Оформление заказа</h1>
                            </div>
                        </section>

                        <section class="goods">
                            <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/summary.php");?>
                        </section>
                        <section class="order">
                            <div class="heading">
                                <h3>Контактная информация</h3>
                                <?if(!$USER->IsAuthorized()):?>
                                    <p>Мы вас не узнали. <a href="/login/" class="border-link">Войдите</a> для быстрого оформления заказа.</p>
                                <?endif?>
                            </div>
                            <div class="info">
                                <div class="fields">
                                    <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props.php");?>
                                    <div id="delivery_payment_area">
                                        <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/paysystem.php");?>
                                        <hr>
                                        <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/delivery.php");?>
                                        <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/total.php");?>
                                        <hr>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <div class="ajaxreload mapreload">
                            <?include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/order_hiden_prop.php");?>
                        </div>
                     </div>
                 </div>
            </div>
            <?
			if(strlen($arResult["PREPAY_ADIT_FIELDS"]) > 0)
				echo $arResult["PREPAY_ADIT_FIELDS"];
			?>

			<?if($_POST["is_ajax_post"] != "Y")
			{
				?>
					</div>
					<input type="hidden" name="confirmorder" id="confirmorder" value="Y">
					<input type="hidden" name="profile_change" id="profile_change" value="N">
					<input type="hidden" name="is_ajax_post" id="is_ajax_post" value="Y">
					<input type="hidden" name="json" value="Y">
                    <script>
                        submitForm();
                    </script>
<!--					<div class="bx_ordercart_order_pay_center"><a href="javascript:void();" onclick="submitForm('Y'); return false;" id="ORDER_CONFIRM_BUTTON" class="checkout">--><?//=GetMessage("SOA_TEMPL_BUTTON")?><!--</a></div>-->
				</form>
				<?
				if($arParams["DELIVERY_NO_AJAX"] == "N")
				{
					?>
					<div style="display:none;"><?$APPLICATION->IncludeComponent("bitrix:sale.ajax.delivery.calculator", "", array(), null, array('HIDE_ICONS' => 'Y')); ?></div>
					<?
				}
			}
			else
			{
				?>
				<script type="text/javascript">
					top.BX('confirmorder').value = 'Y';
					top.BX('profile_change').value = 'N';
				</script>
				<?
				die();
			}
		}
	}
	?>
	</div>
</div>

