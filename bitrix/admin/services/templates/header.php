<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php'); ?>
<?define("SERVICES_ADMIN_URL", "/bitrix/admin/services/");?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title><? $APPLICATION->ShowTitle();?></title>
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="<?=SERVICES_ADMIN_URL?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?=SERVICES_ADMIN_URL?>assets/css/cropper.min.css">
    <link rel="stylesheet" href="<?=SERVICES_ADMIN_URL?>assets/css/planfix.css">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light ">
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#">Сервисы <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/bitrix/admin/">В основную часть админки</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 col-lg-9">