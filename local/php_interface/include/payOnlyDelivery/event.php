<?php
AddEventHandler('sale', 'OnSaleOrderBeforeSaved', 'payOnlyDelivery');
