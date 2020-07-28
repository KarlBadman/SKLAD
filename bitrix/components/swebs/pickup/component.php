<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arResult = array();

$arResult['DPD'] = $this->getDPDTerminals();

$this->IncludeComponentTemplate();
