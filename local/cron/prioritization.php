<?php

if (!$_SERVER['DOCUMENT_ROOT'])
    $_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . "/../../");
    
require $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php";
require $_SERVER['DOCUMENT_ROOT'] . "/bitrix/admin/services/planfix/planfix.php";

$PlanFixClient = new PlanFixTaskList();

$taskFilter = array(
    'target' => 'all',
    'filter' => 'ACTIVE',
    'project.id' => 631220,
    'sort' => 'NUMBER_DESC',
);

$PlanFixClient->SetTaskAllPriority($taskFilter);