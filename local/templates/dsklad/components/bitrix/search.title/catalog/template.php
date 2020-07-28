<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>
<?
$INPUT_ID = trim($arParams["~INPUT_ID"]);
if(strlen($INPUT_ID) <= 0)
	$INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if(strlen($CONTAINER_ID) <= 0)
	$CONTAINER_ID = "title-search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

$rand = rand(0, count(SEARCH_PLACEHOLDERS)-1);
?>
<?if($arParams["SHOW_INPUT"] !== "N"):?>
<form class="ds-header__search" action="<?echo $arResult["FORM_ACTION"]?>" aria-invalid="false" novalidate>
    <span class="header-icon header-icon--search-m js-search"></span>
    <div class="header-search header-search--desktop js-search"><span class="icon-svg ic-search-sm"></span>
        <input data-type="desktop" class="inp-search" id="<?echo $INPUT_ID?>" type="text" name="search" autocomplete="off" placeholder="<?=SEARCH_PLACEHOLDERS[$rand]?>"><span class="header-icon header-icon--search-clear js-search-clear"></span>
    </div>
    <span class="header-search-cancel js-search-cancel">Отмена</span>
    <div class="header-search-result">
        <!-- Этот кусок нужен для мобильных-->
        <div class="header-search-info">
            <div class="header-search header-search--mobile"><span class="icon-svg ic-search-sm"></span>
                <input data-type="mobile" class="inp-search" type="text" name="search" autocomplete="off" placeholder="<?=SEARCH_PLACEHOLDERS[$rand]?>"><span class="header-icon header-icon--search-clear js-search-clear"></span>
            </div>
            <span class="header-search-cancel js-search-mobile-cancel">Отмена</span>
        </div>
        <div class="header-search-result__wrapper" id="<?=$CONTAINER_ID?>" data-block-name="title-search">
            <h4>Популярно сейчас</h4>
            <div class="header-search-result__list">
                <?foreach ($arResult['LINKS'] as $link):?>
                    <a class="header-search-result__item" href="<?=$link['URL']?>"><?=$link['TEXT']?></a>
                <?endforeach;?>
            </div>
        </div>
    </div>
</form>
<?endif?>

<script>
	BX.ready(function(){
		new JCTitleSearch({
			'AJAX_PAGE' : '/',
			'CONTAINER_ID': '<?echo $CONTAINER_ID?>',
			'INPUT_ID': '<?echo $INPUT_ID?>',
			'MIN_QUERY_LEN': 2
		});
	});
</script>