<?php

namespace Swebs\Helper\Main;

class User
{
    public static function getID($isAllowAnonymous = false)
    {
        global $USER;

        $intUserID = $USER->GetID();

        if ($intUserID == NULL && $isAllowAnonymous) {
            $intUserID = \CSaleUser::GetAnonymousUserID();
        }

        return $intUserID;
    }
}