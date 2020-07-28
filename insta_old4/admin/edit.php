<?php

require_once ($_SERVER['DOCUMENT_ROOT']."/insta/class/adminka.php");

$id_foto = $_REQUEST['idfot'];

$adminka_inst = new adminka_inst;

if($_REQUEST['save_block']){
    $adminka_inst->save_text($id_foto);
}

$db = $adminka_inst->chtenie("fotvadmnke.txt")[0];

$needle = $id_foto;

$result = array_filter($db['fotki'], function($innerArray){
    global $needle;
    return ($innerArray['id'] == $needle);
});

$result = array_values($result);

$svoystva_img = Getimagesize($_SERVER['DOCUMENT_ROOT'].$result[0]['adres']);
//$svoystva_img = Getimagesize($result[0]['adres']);
?>

<style>
    .kartinka{
        width: <?echo $svoystva_img[0]?>px;
        height: <?echo $svoystva_img[1]?>px;
        background: url("<?echo $result[0]['adres']?>");
        position: relative;
    }

    .blok_link{
        border: solid 1px #000;
        display: table;
        position: absolute;
    }

    .pox, .poy{
        display: none;
    }

    .blok_link textarea{
        height: 80px;
        width: 300px;
        resize: none;
    }

    .blok_link i{
        position: absolute;
        z-index: 9;
        display: block;
        width: 20px;
        height: 11px;
        background: url("/insta/ugol.png");
        bottom: -12px;
        left: 8%;
    }

    .peretask{
        height: 25px;
        background-color: #072dff;
        cursor: move;
    }

    .cena{
        width: 150px;
    }
</style>

<script>
    objedit = {};
    window.addEventListener("DOMContentLoaded", function() {

        objedit.block = document.getElementsByClassName("blok_link")[0];
        objedit.block.shir = objedit.block.clientWidth;
        objedit.block.vis = objedit.block.clientHeight;

        objedit.blperetask = document.getElementsByClassName("peretask")[0];

        objedit.blperetask.addEventListener("mousedown", objedit.saveXY, false);
        objedit.blperetask.addEventListener("mouseup", objedit.clearXY, false);
        document.addEventListener("mousemove", objedit.moveBlock, false);

        objedit.block.style.top = objedit.block.getElementsByClassName("poy")[0].value + "px";
        objedit.block.style.left = objedit.block.getElementsByClassName("pox")[0].value + "px";

    }, false);

    objedit.delta_x = 0;
    objedit.delta_y = 0;

    objedit.vkl = 0;

    objedit.saveXY = function () {
        objedit.vkl = 1;

        objedit.zahvat_y = event.pageY - objedit.blperetask.getBoundingClientRect().top;
        objedit.zahvat_x = event.pageX - objedit.blperetask.getBoundingClientRect().left;
    };

    objedit.moveBlock = function (obj_event) {
        //objedit.log(obj_event.pageX, obj_event.pageY);

        if(objedit.vkl == 1){
            objedit.block.style.top = obj_event.pageY - 9 - objedit.zahvat_y + "px";
            objedit.block.style.left = obj_event.pageX - 9 - objedit.zahvat_x + "px";

            objedit.block.getElementsByClassName("pox")[0].value = obj_event.pageX - 9 - objedit.zahvat_x;
            objedit.block.getElementsByClassName("poy")[0].value = obj_event.pageY - 9 - objedit.zahvat_y;
        }
    };

    objedit.clearXY = function () {
        objedit.vkl = 0;
    };

    objedit.log = function (a,b) {
        document.getElementsByClassName("log")[0].innerHTML = a+"<br>"+b;
    }
</script>

<form method="post" action="">
    <div class="kartinka">

        <div class="blok_link">
            <div class="peretask"></div>
            <textarea name="text_blok_link" placeholder="Введите описание"><? echo $result[0]['okno']['text_blok_link']?></textarea>
            <div><input type="text" class="cena" name="costold_blok_link" placeholder="Старая цена (число)" value="<? echo $result[0]['okno']['costold_blok_link']?>"><input class="cena" type="text" name="cost_blok_link" placeholder="Цена (только число)" value="<? echo $result[0]['okno']['cost_blok_link']?>"></div>
            <div><input type="text" name="ssilka_blok_link" placeholder="Введите адрес страницы" value="<? echo $result[0]['okno']['ssilka_blok_link']?>"></div>
            <input type="text" class="pox" value="<? echo $result[0]['okno']['x']?>" name="pox">
            <input type="text" class="poy" value="<? echo $result[0]['okno']['y']?>" name="poy">
            <i></i>
        </div>

    </div>
    <input type="submit" name="save_block" value="Сохранить">
</form>

<div class="log"></div>