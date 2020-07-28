<?php

use Dsklad\Config;

function SaveNameProfileUser($order)
{
   if($order->isNew()) {
       $user = new CUser;
       $userName = explode(' ',$order->getPropertyCollection()->getPayerName()->getValue());
       $user->Update($order->getUserId(), ['NAME'=>$userName[0],'LAST_NAME'=>$userName[1]]);
   }
}