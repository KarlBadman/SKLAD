<?php
function OnEpilogHandler() {
    if (isset($_REQUEST['PAGEN_1']) && intval($_REQUEST['PAGEN_1'])>0) {
        $title = $GLOBALS['APPLICATION']->arPageProperties['TITLE'];
        $description = $GLOBALS['APPLICATION']->arPageProperties['DESCRIPTION'];
        $GLOBALS['APPLICATION']->SetPageProperty('title', $title.' (страница '.intval($_REQUEST['PAGEN_1']).')');
        $GLOBALS['APPLICATION']->SetPageProperty('description', $description.' (страница '.intval($_REQUEST['PAGEN_1']).')');
    }
}