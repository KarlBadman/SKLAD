<?php

class maininsta{

    public $dannie;
    public $urls;
	public $DB;
    private $dannie_vichetet;//dlya randoma

    public function __construct(){
        $this->dannie['cliid'] = "4cef72764ef64c7cb808d57f3dc1e56e";
        $this->dannie['token'] = "1513074597.1677ed0.e73d0b11f9224d1b8d3b08db5e1a5252";
        $this->dannie['dannieusera'] = "https://api.instagram.com/v1/users/1513074597/?client_id=4cef72764ef64c7cb808d57f3dc1e56e&access_token=1513074597.1677ed0.e73d0b11f9224d1b8d3b08db5e1a5252";
		
		//$this->dannie['cliid'] = "05f0beefa4704d45928188aec69ea392";
        //$this->dannie['token'] = "6234489494.05f0bee.a99098c4b2ee4ed5a1f3f9fd9632dbe6";
		
        $this->dannie['clitok'] = "client_id=".$this->dannie['cliid']."&access_token=".$this->dannie['token'];

        $this->urls['poslednie'] = "https://api.instagram.com/v1/users/1513074597/media/recent/?".$this->dannie['clitok'];
        $this->urls['fotka'] = "https://api.instagram.com/v1/media/";
    }

    public function poluchobj($adres){//poluchenie poslednih fotok
        $text = file_get_contents($adres);

        $arr = json_decode($text,true);

        $vivfunc[0] = $arr;

        return $vivfunc;
    }

    public function laiki($idfoto){//laiki u fotok
        $text = file_get_contents($this->urls['fotka'].$idfoto."?".$this->dannie['clitok']);

        $arr = json_decode($text,true);

        $vivfun[0] = $arr;

        return $vivfun;
    }

    public function pereborfotok(){//sozdanie massiva fotok
        $massfoto = $this->poluchobj($this->urls['poslednie'])[0]['data'];

        foreach ($massfoto as $fotki) {
            $newmassfotok['foto'] = $fotki['images']['standard_resolution']['url'];
            $newmassfotok['liki'] = $this->laiki($fotki['id'])[0]['data']['likes']['count'];

            $sbornewmass[] = $newmassfotok;
        }

        $vivfunc[0] = $sbornewmass;

        return $vivfunc;

    }
	
	public function sboradresov(){
        /*
		$sql_korz= "SELECT * FROM b_iblock_element_property WHERE IBLOCK_ELEMENT_ID='21688' and IBLOCK_PROPERTY_ID='2766'";
		$resultkorz= mysqli_query($this->DB, $sql_korz);
		$rowkorz= mysqli_fetch_array($resultkorz);
		do{
			$massadresov[] = $rowkorz['VALUE'];
		}
		while($rowkorz= mysqli_fetch_array($resultkorz));
        */

        $massbdadmin = $this->chtenie("fotvadmnke.txt")[0];

        foreach ($massbdadmin['fotki'] as $massadm) {
            $massadresov[] = $massadm['urlfoto'];
        }

        $vivfunc[0] = $massadresov;
		
		return $vivfunc;

	}

    public function savevfile($imya, $json){
        $path= $_SERVER['DOCUMENT_ROOT']."/insta/bd/".$imya;
        $fp=fopen($path,'r');
        $c=fread($fp, filesize($path));
        fclose($fp);
        $fp=fopen($path,'w');
        fwrite($fp,$json);
        fclose($fp);
    }
	
	public function vzyatfoto(){//zapis v 1.txt

        $massbdadmin = $this->chtenie("fotvadmnke.txt")[0];

        foreach ($massbdadmin['fotki'] as $mass) {
            $mass['laiki'] = $this->kolichlaikov($mass['urlfoto'])[0];
            $novmass[] = $mass;
        }

        $zapiskash['fotki'] = $novmass;

        $zapiskash['fotki'] = $this->sortirovka($zapiskash['fotki'])[0];

        $zapisvmass = json_encode($zapiskash);
        $this->savevfile("fotvadmnke.txt", $zapisvmass);

        //$danniepolz = file_get_contents($this->dannie['dannieusera']);
        $danniepolz = maininsta::cur("https://www.instagram.com/design.sklad/")[0][0];
        $this->savevfile("2.txt", $danniepolz);//sohranenie dannih polzovatelya

        $this->raspred();//raspredelenie

        echo "Успешно записано";
    }

    public function sortirovka($mass){
        foreach ($mass as $massfot) {//perebor dlya sortirovki
            $sortpolaikam[] = number_format($massfot['laiki'], 0, '', '');
        }

        ksort($sortpolaikam);
        $sortpolaikam = array_values($sortpolaikam);

        $chtenbd['laki'] = $sortpolaikam;
        array_multisort($sortpolaikam, SORT_DESC, $mass);

        $vivfunc[0] = $mass;

        return $vivfunc;
    }

    //raspredelenie dlya randoma
    public function otnyatotobsch(){
        $this->dannie_vichet->fotok_popalo += 1;

        if($this->dannie_vichet->fotok_popalo >= $this->dannie_vichet->maxfot){
            $this->dannie_vichet->fotok_popalo = 0;
            $this->dannie_vichet->nomermesta += 1;
            $this->dannie_vichet->maxfot = ceil(($this->dannie_vichet->obschKONST - $this->dannie_vichet->otnim) / (6 - $this->dannie_vichet->nomermesta));
        }
    }

    public function raspred(){
        $massbdadmin = $this->chtenie("fotvadmnke.txt")[0];

        $obschkolich_fotok = count($massbdadmin['fotki']);
        $maxfot_random = ceil($obschkolich_fotok / 5);

        $this->dannie_vichet->otnim = $obschkolich_fotok;//otnyatie iz obscheuj kolichastva
        $this->dannie_vichet->maxfot = $maxfot_random;//max na shag
        $this->dannie_vichet->obschKONST = $obschkolich_fotok;
        $this->dannie_vichet->nomermesta = 1;
        $this->dannie_vichet->fotok_popalo = 0;

        $i = 0;
        foreach ($massbdadmin['fotki'] as $massfot) {
            $i += 1;
            $this->dannie_vichet->otnim = $i;

            $raspred1['urov'.$this->dannie_vichet->nomermesta][] = $massfot;
            $this->otnyatotobsch();

        }

        $massbdadmin['raspred'] = $raspred1;

        $zapisvmass = json_encode($massbdadmin);
        $this->savevfile("fotvadmnke.txt", $zapisvmass);
    }
    //END raspredelenie dlya randoma

    public function vivraspred(){
        $massivizcach = $this->chtenie("fotvadmnke.txt")[0]['raspred'];

        foreach ($massivizcach as $kewy => $mass) {
            $rand[] = $mass[rand(0, count($mass)-1)];
        }

        $vivfunc[0] = $rand;

        return $vivfunc;
    }
	
	public function chtenie($file){
		$text = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/insta/bd/'.$file);
		$arr = json_decode($text,true);
		
		$vivfunc[0] = $arr;
		
		return $vivfunc;
	}

    public function getInstMed($url){
        $getvivavtor = file_get_contents("http://api.instagram.com/oembed/?url=$url");
        $getvivavtor = json_decode($getvivavtor);

        $vivfunc[0] = $getvivavtor;

        return $vivfunc;
    }

    public function vivavtora($urloemed){
        $avtor = $urloemed['title'];
        $avtor = explode("@", $avtor);
        $avtor = str_replace(array("\n", ' '), '$$', $avtor[1]);//zamena posledneuj simvola
        $avtor = explode("$$", $avtor);
        $avtor = $avtor[0];

        if($avtor == ""){
            $avtor = "design.sklad";
        }

        $vivfunc[0] = $avtor;

        return $vivfunc;
    }

    public function kolichlaikov($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FAILONERROR, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); // times out after 4s
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5 GTB6");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($curl);

        $razbornalaiki = explode("<meta content=", $data);
        $otdelenlaik = explode("Likes", $razbornalaiki[1]);
        $laiki = substr($otdelenlaik[0], 1);
        $laiki = str_replace(",","",$laiki);
        //$avtor = explode("@", $data);
        //$avtor = explode(" ", $avtor[2]);

        $vivfunc[0] =  $laiki;
       //$vivfunc[1] = $avtor[0];

        return $vivfunc;
    }

    public function fanatov($kol_fanov){
        $kolichsimv = mb_strlen($kol_fanov, 'utf-8');
        $posled_cifra = mb_substr($kol_fanov, $kolichsimv-1,$kolichsimv-1, 'UTF-8');
        $posled_dve = mb_substr($kol_fanov, $kolichsimv-2,$kolichsimv-1, 'UTF-8');
        $varifanov = array(
            "0" => "фанатов",
            "1" => "фанат",
            "2" => "фаната",
            "3" => "фаната",
            "4" => "фаната",
            "5" => "фанатов",
            "6" => "фанатов",
            "7" => "фанатов",
            "8" => "фанатов",
            "9" => "фанатов"
        );

        $fanatov = $varifanov[$posled_cifra];

        $mass_dve_numb = array(
            "11" => "фанатов",
            "12" => "фанатов",
            "13" => "фанатов",
            "14" => "фанатов",
            "15" => "фанатов",
            "16" => "фанатов",
            "17" => "фанатов",
            "18" => "фанатов",
            "19" => "фанатов"
        );

        if($mass_dve_numb[$posled_dve]){
            $fanatov = $mass_dve_numb[$posled_dve];
        }

        $vivfunc[0] = $fanatov;

        return $vivfunc;
    }

    public static function cur($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FAILONERROR, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); // times out after 4s
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5 GTB6");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($curl);

        preg_match_all('#"edge_followed_by":{"count":(.+?)}#is', $data, $podpis);

        $vivfunc[0] = $podpis[1];//kolich podpisok

        return $vivfunc;
    }

    public static function get($get){//get
        $req = $_REQUEST[$get];
        $req = str_replace("'", '', $req);

        return $req;
    }

}

?>