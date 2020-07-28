<?php

$module_id = 'dellindev.delivery';

CModule::AddAutoloadClasses(
    $module_id,
    array(
        "DellinAPI" => "classes/general/DellinAPI.php",
        "DellinDelivery" => "classes/general/DellinDelivery.php"
    )
);
