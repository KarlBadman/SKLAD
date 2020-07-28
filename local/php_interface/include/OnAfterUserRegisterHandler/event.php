<?php
AddEventHandler("main", "OnAfterUserAdd", "OnAfterUserRegisterHandler");
AddEventHandler("main", "OnAfterUserRegister", "OnAfterUserRegisterHandler");
AddEventHandler("main", "OnAfterUserSimpleRegister", "OnAfterUserRegisterHandler");