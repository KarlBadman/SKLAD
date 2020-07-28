<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

/**
 * @var array $arParams
 */

CUtil::InitJSCore(array("popup"));

$arAuthServices = $arPost = array();
if(is_array($arParams["~AUTH_SERVICES"]))
{
	$arAuthServices = $arParams["~AUTH_SERVICES"];
}
if(is_array($arParams["~POST"]))
{
	$arPost = $arParams["~POST"];
}

$hiddens = "";
foreach($arPost as $key => $value)
{
	if(!preg_match("|OPENID_IDENTITY|", $key))
	{
		$hiddens .= '<input type="hidden" name="'.$key.'" value="'.$value.'" />'."\n";
	}
}
?>
<script type="text/javascript">
function BxSocServPopup(id)
{
	var content = BX("bx_socserv_form_"+id);
	if(content)
	{
		var popup = BX.PopupWindowManager.create("socServPopup"+id, BX("bx_socserv_icon_"+id), {
			autoHide: true,
			closeByEsc: true,
			angle: {offset: 24},
			content: content,
			offsetTop: 3
		});

		popup.show();

		var input = BX.findChild(content, {'tag':'input', 'attribute':{'type':'text'}}, true);
		if(input)
		{
			input.focus();
		}

		var button = BX.findChild(content, {'tag':'input', 'attribute':{'type':'submit'}}, true);
		if(button)
		{
			button.className = 'btn btn-primary';
		}
	}
}
</script>

<div class="bx-authform-social">

    <div class="field">
        <?
        foreach ($arAuthServices as $service):
            $onclick = ($service["ONCLICK"] <> '' ? $service["ONCLICK"] : "BxSocServPopup('" . $service["ID"] . "')");
            $img = '';
            $class = '';
            switch(strtolower($service["ID"])){
                case 'vkontakte':
                    $img = '#share-vk2';
                    $class = 'icon__share-vk2';
                    break;
                case 'facebook':
                    $img = '#share-facebook2';
                    $class = 'icon__share-facebook2';
                    break;
            }
            ?>
            <a id="bx_socserv_icon_<?= $service["ID"] ?>" href="javascript:void(0)"
               onclick="<?= htmlspecialcharsbx($onclick) ?>">
                <span class="<?= $class;?>">
                    <svg>
                      <use xmlns:xlink="http://www.w3.org/1999/xlink"
                           xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg<?= $img?>"></use>
                    </svg>
                </span>
            </a>
            <? if ($service["ONCLICK"] == '' && $service["FORM_HTML"] <> ''): ?>
            <div id="bx_socserv_form_<?= $service["ID"] ?>" class="bx-authform-social-popup">
                <form action="<?= $arParams["AUTH_URL"] ?>" method="post">
                    <?= $service["FORM_HTML"] ?>
                    <?= $hiddens ?>
                    <input type="hidden" name="auth_service_id" value="<?= $service["ID"] ?>"/>
                </form>
            </div>
        <? endif ?>
            <?
        endforeach;
        ?>
    </div>

</div>