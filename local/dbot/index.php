<?
    /**
    *   DBot REST API interface
    *   Version 1.0.0
    *   Date 30.10.2018
    */

    namespace DBotApp;

    // require files
    require $_SERVER["DOCUMENT_ROOT"] . "/local/dbot/bottools.php";
    require $_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/Utils.php";
    require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
    require $_SERVER['DOCUMENT_ROOT'] . '/local/components/swebs/custom_o2k/class.php';
    // Controllers
    require $_SERVER["DOCUMENT_ROOT"] . "/local/dbot/app/controllers/basecontroller.php";
    require $_SERVER["DOCUMENT_ROOT"] . "/local/dbot/app/controllers/exceptioncontroller.php";
    // Models
    require $_SERVER["DOCUMENT_ROOT"] . "/local/dbot/app/models/basemodel.php";
    require $_SERVER["DOCUMENT_ROOT"] . "/local/dbot/app/models/fusertokenrelations.php";
    // Autoload libs
    require $_SERVER["DOCUMENT_ROOT"] . "/local/libs/vendor/autoload.php";

    // use section
    use \Pecee\SimpleRouter\SimpleRouter;
    use \Bitrix\Main\Loader;
    use \Bitrix\Main\Application;
    use AppController\BaseController;
    use AppController\ExceptionController;

    // include modules
    Loader::includeModule('sale');
    Loader::IncludeModule('iblock');
    Loader::IncludeModule("catalog");
    Loader::includeModule('highloadblock');

    //error_reporting(1);

    $ACCESSED_METHODS = array();
    $API_METHOD = Application::getInstance()->getContext()->getRequest()->getQuery('METHOD') ? : "";
    $REQUEST_METHOD = Application::getInstance()->getContext()->getRequest()->getRequestMethod();
    
    $cExceptionBaseController = new ExceptionController("AppController\BaseController");
    // $cExceptionOtherController = new AppController\ExceptionController("AppController\OtherController"); // TODO OTHER CONTROLLER

    $ACCESSED_METHODS = array_merge($ACCESSED_METHODS
        , $cExceptionBaseController->cExceptionGetAccessedMethods() // BASE CONTROLLER
        // , $cExceptionOtherController->cExceptionOtherController(), // TODO OTHER CONTROLLER
    );

    SimpleRouter::match(['get', 'post'], '/botrest/'.strtolower($API_METHOD)."/", 'AppController\BaseController@' . strtolower($API_METHOD));

    // Method Not Found EXCEPTION
    if (!in_array($API_METHOD, $ACCESSED_METHODS))
        ExceptionController::cExceptionMethodNotFound($API_METHOD);

    SimpleRouter::start();


?>
