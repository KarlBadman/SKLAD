<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if (!function_exists('getWord')) {
    function getWord($intCount)
    {
        $strWord = 'способ';

        if ($intCount > 20) {
            $intLastCount = substr($intCount, -1);
        } else {
            $intLastCount = $intCount;
        }

        if (($intLastCount > 1) && ($intLastCount < 5)) {
            $strWord .= 'a';
        } elseif ($intLastCount > 4) {
            $strWord .= 'ов';
        }

        return $strWord;
    }
}
if (!function_exists('getWord2')) {
    function getWord2($intCount)
    {
        $strWord = 'доступен';

        if ($intCount > 20) {
            $intLastCount = substr($intCount, -1);
        } else {
            $intLastCount = $intCount;
        }

        if ($intLastCount > 1) {
            $strWord = 'доступны';
        }

        return $strWord;
    }
}
if (!function_exists('getWord3')) {
    function getWord3($intCount)
    {
        $strWord = 'товар';

        if ($intCount > 20) {
            $intLastCount = substr($intCount, -1);
        } else {
            $intLastCount = $intCount;
        }

        if (($intLastCount > 1) && ($intLastCount < 5)) {
            $strWord .= 'a';
        } elseif ($intLastCount > 4) {
            $strWord .= 'ов';
        }

        return $strWord;
    }
}
if (!function_exists('getWord4')){
    function getWord4 ($intCount) {
        $strWord = 'рабоч';
        $strWord2 = 'дн';

        if ($intCount > 20) {
            $intLastCount = substr($intCount, -1);
        } else {
            $intLastCount = $intCount;
        }
    
        if ($intLastCount == 1) {
            $strWord .= 'ий';
            $strWord2 = 'день';
        } elseif (($intLastCount > 1) && ($intLastCount < 5)) {
            $strWord .= 'их';
            $strWord2 .= 'я';
        } elseif ($intLastCount > 4) {
            $strWord .= 'их';
            $strWord2 .= 'ей';
        }

        return $strWord . " " . $strWord2;
    }
}

