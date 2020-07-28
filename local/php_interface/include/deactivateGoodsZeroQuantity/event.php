<?php
AddEventHandler("catalog", "OnProductUpdate", "deactivateGoodsZeroQuantity");
AddEventHandler("catalog", "OnProductAdd", "deactivateGoodsZeroQuantity");


