<?
    require "../templates/header.php";
    require $_SERVER['DOCUMENT_ROOT'] . "/local/libs/vendor/autoload.php";
    require "./instaclient.php";

    use Insta\clientInterface;
    
    $client = new ClientInterface();
    
    // Set page properties
    use Bitrix\Main\Page\Asset;
    $APPLICATION->SetPageProperty('title', 'Интеграция с инстаграм');
    Asset::getInstance()->addCss("/bitrix/admin/services/assets/css/cropper.min.css");
    Asset::getInstance()->addJs("/bitrix/admin/services/assets/js/cropper.min.js");
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            
            <?require "./views/top.php"?>
            
            <?if ($client->isSettingsView) : ?>
                <?require "./views/settings.php"?>
            <?endif;?>
            
            <?if ($client->isCurrentUserView) : ?>
                <?require "./views/currentUser.php"?>
            <?endif;?>
            
            <?if ($client->isUserByIDView) : ?>
                <?require "./views/userById.php"?>
            <?endif;?>
            
            <?if ($client->isMediaView) : ?>
                <?require "./views/isMedia.php"?>
            <?endif;?>
            
            <?if ($client->isWidgetView) : ?>
                <?require "./views/isWidget.php"?>
            <?endif;?>
            
        </div>
    </div>
</div>
    
<?require "../templates/footer.php"?>