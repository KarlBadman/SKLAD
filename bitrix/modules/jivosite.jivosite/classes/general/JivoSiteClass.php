<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/jivosite.jivosite/config.php');

class JivoSiteClass{
    public function addScriptTag() {

        global $APPLICATION;

        if(self::isAdminPage() || self::isEditMode()) {
            return;
        }

        $add_script = false;
        $sites = COption::GetOptionString("jivosite.jivosite", "sites");
        $sites = json_decode($sites, true);
        if(isset($sites) and is_array($sites)) {
            foreach ($sites as $site => $value) {
                if ($site == SITE_ID) {
                    $add_script = true;
                }
            }
            if (!$add_script) {
                return;
            }
        }

        $widget_id = COption::GetOptionString("jivosite.jivosite", "widget_id");
        //$APPLICATION->AddHeadScript("//".JIVO_CODE_URL."/script/widget/$widget_id");
        $APPLICATION->AddHeadString("\n<!-- BEGIN JIVOSITE CODE -->
		<script type='text/javascript'>
		(function(){ document.jivositeloaded=0;var widget_id = 'rqY001plT9';var d=document;var w=window;function l(){var s = d.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}//эта строка обычная для кода JivoSite
		function zy(){
		    //удаляем EventListeners
		    if(w.detachEvent){//поддержка IE8
			w.detachEvent('onscroll',zy);
			w.detachEvent('onmousemove',zy);
			w.detachEvent('ontouchmove',zy);
			w.detachEvent('onresize',zy);
		    }else {
			w.removeEventListener(\"scroll\", zy, false);
			w.removeEventListener(\"mousemove\", zy, false);
			w.removeEventListener(\"touchmove\", zy, false);
			w.removeEventListener(\"resize\", zy, false);
		    }
		    //запускаем функцию загрузки JivoSite
		    if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}
		    //Устанавливаем куку по которой отличаем первый и второй хит
		    var cookie_date = new Date ( );
		    cookie_date.setTime ( cookie_date.getTime()+60*60*28*1000); //24 часа для Москвы
		    d.cookie = \"JivoSiteLoaded=1;path=/;expires=\" + cookie_date.toGMTString();
		}
		if (d.cookie.search ( 'JivoSiteLoaded' )<0){//проверяем, первый ли это визит на наш сайт, если да, то назначаем EventListeners на события прокрутки, изменения размера окна браузера и скроллинга на ПК и мобильных устройствах, для отложенной загрузке JivoSite.
		    if(w.attachEvent){// поддержка IE8
			w.attachEvent('onscroll',zy);
			w.attachEvent('onmousemove',zy);
			w.attachEvent('ontouchmove',zy);
			w.attachEvent('onresize',zy);
		    }else {
			w.addEventListener(\"scroll\", zy, {capture: false, passive: true});
			w.addEventListener(\"mousemove\", zy, {capture: false, passive: true});
			w.addEventListener(\"touchmove\", zy, {capture: false, passive: true});
			w.addEventListener(\"resize\", zy, {capture: false, passive: true});
		    }
		}else {zy();}
		})();</script>
        <!-- END JIVOSITE CODE -->\n");
    }

    static private function isAdminPage() {
        return defined('ADMIN_SECTION');
    }

    static private function isEditMode() {
        if(isset($_SESSION["SESS_INCLUDE_AREAS"]) && $_SESSION["SESS_INCLUDE_AREAS"]) {
            return true;
        }

        if (isset($_GET["bitrix_include_areas"]) && $_GET["bitrix_include_areas"] == "Y") {
            return true;
        }

        $aUserOpt = CUserOptions::GetOption("global", "settings");
        if(isset($aUserOpt["panel_dynamic_mode"]) && $aUserOpt["panel_dynamic_mode"] == "Y") {
            return true;
        }

        return false;
    }
}
