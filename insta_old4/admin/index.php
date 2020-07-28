<?php
session_start();
require_once ($_SERVER['DOCUMENT_ROOT']."/insta/class/adminka.php");

$admininsta = new adminka_inst;

if($_REQUEST['dobafot_insta']){
    $admininsta->zagruz_foto($_REQUEST['dobafot_insta']);
}

if($_REQUEST['idfot']){
    $admininsta->udalfoto($_REQUEST['idfot']);
}

$vivfotadminki = $admininsta->chtenie("fotvadmnke.txt")[0];
foreach ($vivfotadminki['fotki'] as $massfoto) {
    /*
    $i += 1;
    $formirtablic .= "
        <tr>
            <td>".$i."</td>
            <td><img src='".$massfoto['adres']."'</td>
            <td>".$massfoto['laiki']."</td>
            <td>
                <form method='post' action=''>
                    <input type='hidden' name='idfot' value='".$massfoto['id']."'>
                    <input type='submit' name='del' value='x'>
                </form>
            </td>
        </tr>
    ";
    */

    $kartochki_insi .= "
        <div class='kartoch_insti'>
        <div class='relativ_insta'>
            <div class='edit_foto'><a target='_blank' href='/insta/admin/edit.php?idfot=".$massfoto['id']."'>Редактировать</a></div>
            <div class='foto_insti'><a target='_blank' href='".$massfoto['urlfoto']."'><img src='".$massfoto['adres']."'></a></div>
            <div class='laiki_udalit'>
                <div class='laiki'>".$massfoto['laiki']."</div>
                <div class='udalit_foto'>
                    <form method='post' action=''>
                        <input type='hidden' name='idfot' value='".$massfoto['id']."'>
                        <input type='submit' class='udalit_insta' name='del' value='Удалить'>
                    </form>
                </div>
            </div>
        </div>
        <div class='url_insta'><a target='_blank' href='".$massfoto['urlfoto']."'>".$massfoto['urlfoto']."</a></div>
        </div>
    ";
}

?>

<style>
    body{
        font-family: Arial, Helvetica, sans-serif;
    }

    .foto_insti img{
        width: 225px;
        height: 225px;
    }

    .foto_insti{
        height: 225px;
        overflow: hidden;
    }

    .kartoch_insti{
        display: inline-block;
        margin: 10px;
        border: 1px solid#eee;
        vertical-align: top;
    }

    .laiki_udalit{
        position: absolute;
        bottom: 10px;
        z-index: 9;
        width: 100%;
    }

    .laiki{
        padding-left: 10px;
        color: #fff;
        font-size: 18px;
        float: left;
    }

    .udalit_foto{
        float: right;
        padding-right: 10px;
    }

    .url_insta{
        font-size: 11px;
        padding: 5px;
        width: 180px;
        overflow: hidden;
    }

    .url_insta a{
        color: 000;
    }

    .relativ_insta{
        position: relative;
    }

    .refresh_insta{
        width: 18px;
        height: 18px;
        background: url("/insta/obnov.png");
    }

    .obnov_insta{
        display: flex;
        margin-top: 20px;
        margin-left: 10px;
    }

    .link_obnov{
        margin-left: 10px;
    }

    .link_obnov a{
        font-size: 16px;
        color: #000;
        text-decoration: none;
        border-bottom: 1px dashed;
        padding-bottom: 2px;
    }

    .udalit_insta{
        border: 0;
        background: none;
        color: #fff;
        cursor: pointer;
        border-bottom: 1px solid;
        padding: 0;
    }

    .edit_foto{
        position: absolute;
        top: 20px;
        right: 10px;
    }

    .edit_foto a{
        color: #fff;
    }
</style>

<form method="post" action="">
    <input type="text" name="dobafot_insta">
    <input name="podgruz_kart" type="submit" value="Загрузить">
</form>

<div class="ryadi_instag">
    <?echo $kartochki_insi?>
</div>

<div class="obnov_insta">
    <div class="refresh_insta"></div>
    <div class="link_obnov"><a target="_blank" href="/insta/save.php">Обновить кэш</a></div>
</div>
