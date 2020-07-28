<?php

require_once ($_SERVER['DOCUMENT_ROOT']."/insta/class/main.php");

$maininsta = new maininsta;

$massivizcach = $maininsta->vivraspred()[0];
$massdannihuserinst = $maininsta->chtenie("2.txt")[0];

/*
echo "<pre>";
print_r($massivizcach);
echo "</pre>";
*/

$kolich_fonatov = $massdannihuserinst['data']['counts']['followed_by'];

function vivod_blok($massivizcach){
    if($massivizcach['okno']){
        ?>
        <div style="top: <?echo $massivizcach['okno']['y']?>px; left: <?echo $massivizcach['okno']['x']?>px;" class="oknodannih opac">
            <div class="nazvanie_obloch"><?echo $massivizcach['okno']['text_blok_link']?></div>
            <div class="price_cloud"><span class="old_cena"><?echo number_format($massivizcach['okno']['costold_blok_link'], 0, '', ' ')?></span> от <strong><?echo number_format($massivizcach['okno']['cost_blok_link'], 0, '', ' ')?>.–</strong></div>
            <div class="ssilka_blok"><a href="<?echo $massivizcach['okno']['ssilka_blok_link']?>">Перейти к товару</a></div>
            <i></i>
        </div>
    <?
    }
}

?>

<link rel="stylesheet" href="/insta/swiper/swiper.min.css?<?=time()?>">
<script src="/insta/swiper/swiper.min.js"></script>

<link rel="stylesheet" type="text/css" href="/insta/jelly/ns-style-growl.css?<?=time()?>">
<script src="/insta/jelly/modernizr.custom.js?<?=time()?>"></script>


<script src="/insta/js.js?<?echo time()?>"></script>

<link rel="stylesheet" href="/insta/style.css?<?echo time()?>">

<div class="insta_po_centru">
    <div class="zagol_insta_pocentru">
        <div class="zagolovok_inst_gl">
            <div class="prisoed_heshteg">Присоединяйтесь к отзывам</div>
            <div><a class="zagolovo_insta" target="_blank" href=""></a></div>
            <div class="mig_cursor"></div>
        </div>
    </div>

    <div class="flex_insta urovni_insta">
        <div class="big_foto_insta">
            <div class="laiki_i_diz_vtor_ur_inst">
                <div class="laki_cop_centr_inst">
                    <div class="laiki_bigfoto">
                        <div class="serdc_laik_inst"><img src="/insta/heart.svg"></div>
                        <div class="numb_laik_inst"><?echo $massivizcach[0]['laiki']?></div>
                    </div>
                    <a target="_blank" class="ssil_nacopir" href="https://www.instagram.com/<?echo $massivizcach[0]['avtor']?>/"><div class="cop_insta"><div class="text_copir">@<?echo $massivizcach[0]['avtor']?></div></div></a>
                </div>
            </div>

            <img class="vstavlenfot_insta" src="<?echo $massivizcach[0]['adres']?>">
            <?vivod_blok($massivizcach[0])?>
        </div>
        <div class="big_foto_insta foto_mez_treh_insta">
            <div class="laiki_i_diz_vtor_ur_inst">
                <div class="laki_cop_centr_inst">
                    <div class="laiki_bigfoto">
                        <div class="serdc_laik_inst"><img src="/insta/heart.svg"></div>
                        <div class="numb_laik_inst"><?echo $massivizcach[1]['laiki']?></div>
                    </div>
                    <a target="_blank" class="ssil_nacopir" href="https://www.instagram.com/<?echo $massivizcach[1]['avtor']?>/"><div class="cop_insta"><div class="text_copir">@<?echo $massivizcach[1]['avtor']?></div></div></a>
                </div>
            </div>
            <img class="vstavlenfot_insta" src="<?echo $massivizcach[1]['adres']?>">
            <?vivod_blok($massivizcach[1])?>
        </div>
        <div class="foto_vtor_urov">
            <div class="laiki_i_diz_vtor_ur_inst">
                <div class="laki_cop_centr_inst">
                    <div class="laiki_bigfoto">
                        <div class="serdc_laik_inst"><img src="/insta/heart.svg"></div>
                        <div class="numb_laik_inst"><?echo $massivizcach[2]['laiki']?></div>
                    </div>
                    <a target="_blank" class="ssil_nacopir" href="https://www.instagram.com/<?echo $massivizcach[2]['avtor']?>/"><div class="cop_insta"><div class="text_copir">@<?echo $massivizcach[2]['avtor']?></div></div></a>
                </div>
            </div>
            <img class="vstavlenfot_insta" src="<?echo $massivizcach[2]['adres']?>">
            <?vivod_blok($massivizcach[2])?>
        </div>
    </div>
    <div class="flex_insta vtor_urov_insta urovni_insta">

        <div class="foto_vtor_urov">
            <div class="laiki_i_diz_vtor_ur_inst">
                <div class="laki_cop_centr_inst">
                    <div class="laiki_bigfoto">
                        <div class="serdc_laik_inst"><img src="/insta/heart.svg"></div>
                        <div class="numb_laik_inst"><?echo $massivizcach[3]['laiki']?></div>
                    </div>
                    <a target="_blank" class="ssil_nacopir" href="https://www.instagram.com/<?echo $massivizcach[3]['avtor']?>/"><div class="cop_insta"><div class="text_copir">@<?echo $massivizcach[3]['avtor']?></div></div></a>
                </div>
            </div>
            <img class="vstavlenfot_insta" src="<?echo $massivizcach[3]['adres']?>">
            <?vivod_blok($massivizcach[3])?>
        </div>
        <div class="foto_vtor_urov foto_mez_treh_insta">
            <div class="laiki_i_diz_vtor_ur_inst">
                <div class="laki_cop_centr_inst">
                    <div class="laiki_bigfoto">
                        <div class="serdc_laik_inst"><img src="/insta/heart.svg"></div>
                        <div class="numb_laik_inst"><?echo $massivizcach[4]['laiki']?></div>
                    </div>
                    <a target="_blank" class="ssil_nacopir" href="https://www.instagram.com/<?echo $massivizcach[4]['avtor']?>/"><div class="cop_insta"><div class="text_copir">@<?echo $massivizcach[4]['avtor']?></div></div></a>
                </div>
            </div>
            <img class="vstavlenfot_insta" src="<?echo $massivizcach[4]['adres']?>">
            <?vivod_blok($massivizcach[4])?>
        </div>
        <div class="statist_numb_insta">
            <div class="statist_numb_insta_pocent">
                <div class="chislo_fanov_instagrama"><?echo number_format((float)$kolich_fonatov, 0, '.', ',')?></div>
                <div class="fani_string_inst"><?echo $maininsta->fanatov($kolich_fonatov)[0]?> <span class="dsklad_insta">DSKLAD</span></div>
                <div class="prisoed_po_centr">
                    <div class="prisoedinilos_insta">
                        <a class="ssilka_prisoed" target="_blank" href="https://www.instagram.com/design.sklad/">Присоединиться</a>
                        <div class="strel_vssilke"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="insta_mobile"></div>


<script src="/insta/jelly/classie.js?<?=time()?>"></script>
<script src="/insta/jelly/notificationFx.js?<?=time()?>"></script>
<script>
    objinsta.vspliv_blok = function () {
        [].forEach.call( document.querySelectorAll('.big_foto_insta, .foto_vtor_urov, #insta_mobile .swiper-slide'), function(el) {
            if(el.getElementsByClassName("oknodannih")[0]){
                var bttn = el;
                // make sure..
                bttn.disabled = false;

                var dann_blok = el.getElementsByClassName("oknodannih")[0];

                bttn.addEventListener('click', function() {
                    if(!el.getElementsByClassName("oblach")[0]){
                        // simulate loading (for demo purposes only)
                        classie.add( bttn, 'active' );
                        setTimeout( function() {

                            classie.remove( bttn, 'active' );

                            // create the notification
                            var notification = new NotificationFx({
                                message : dann_blok.innerHTML,
                                layout : 'growl',
                                wrapper : bttn,
                                effect : 'jelly',
                                type : 'notice', // notice, warning, error or success
                                style: "top: "+dann_blok.offsetTop + "px; left: "+dann_blok.offsetLeft + "px",
                                onClose : function() {
                                    bttn.disabled = false;
                                }
                            });

                            // show the notification
                            notification.show();

                        }, 0 );

                        el.getElementsByClassName("vstavlenfot_insta")[0].style.opacity = 1;
                        el.getElementsByClassName("vstavlenfot_insta")[0].style.cursor = "auto";
                    }

                    // disable the button (for demo purposes only)
                    this.disabled = true;
                } );
            }
        });
    }

</script>
