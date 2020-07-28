<?php

require_once ($_SERVER['DOCUMENT_ROOT']."/insta/class/main.php");

class adminka_inst extends maininsta{

    public function zagruz_foto($urlfoto){
        $adresfoto = "http://api.instagram.com/oembed/?url=".$urlfoto;
        $mediaid = $this->poluchobj($adresfoto)[0];

        $chtenbd = $this->chtenie("fotvadmnke.txt")[0];

        $chtenbd['fotki'][] = array(
            "id" => $mediaid['media_id'],
            "adres" => $mediaid['thumbnail_url'],
            "urlfoto" => $urlfoto,
            "laiki" => $this->kolichlaikov($urlfoto)[0],
            "avtor" => $this->vivavtora($mediaid)[0]
        );

        $chtenbd['fotki'] = $this->sortirovka($chtenbd['fotki'])[0];

        $zapisvmass = json_encode($chtenbd);

        $this->savevfile("fotvadmnke.txt", $zapisvmass);

        $this->raspred();//raspredelenie

        header ('Location: /insta/admin/');
        exit();

        //return $this->chtenie("fotvadmnke.txt")[0];
    }

    public function udalfoto($idfoto){
        $massbdadmin = $this->chtenie("fotvadmnke.txt")[0];

        foreach ($massbdadmin['fotki'] as $mass) {
            if($mass['id'] != $idfoto){
                $novmass[] = $mass;
            }
        }

        $fotki['fotki'] = $novmass;

        $zapisvmass = json_encode($fotki);

        $this->savevfile("fotvadmnke.txt", $zapisvmass);

        $this->raspred();//raspredelenie

    }

    public function save_text($id){
        $massbdadmin = $this->chtenie("fotvadmnke.txt")[0];
        foreach ($massbdadmin['fotki'] as $fot) {
            if($fot['id'] == $id){
                $zapolnen['x'] = $_REQUEST['pox'];
                $zapolnen['y'] = $_REQUEST['poy'];
                $zapolnen['text_blok_link'] = $_REQUEST['text_blok_link'];
                $zapolnen['ssilka_blok_link'] = $_REQUEST['ssilka_blok_link'];
                $zapolnen['cost_blok_link'] = $_REQUEST['cost_blok_link'];
                $zapolnen['costold_blok_link'] = $_REQUEST['costold_blok_link'];
                $fot['okno'] = $zapolnen;
            }
            $novmass[] = $fot;
        }

        $massbdadmin['fotki'] = $novmass;

        $zapisvmass = json_encode($massbdadmin);
        $this->savevfile("fotvadmnke.txt", $zapisvmass);
    }

}

?>