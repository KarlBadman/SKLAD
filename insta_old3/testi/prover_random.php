<?php

require_once ($_SERVER['DOCUMENT_ROOT']."/insta/class/main.php");

class test_random extends maininsta{

    private $dannie_vichetet;

    public function otnyatotobsch($otschmesta){
        /*
        $this->dannie_vichet->proverka = $this->dannie_vichet->otnim;
        $otschmesta = $this->dannie_vichet->obschKONST - $otschmesta;
        $this->dannie_vichet->maxfot = ceil($this->dannie_vichet->otnim / $otschmesta);
        $this->dannie_vichet->otnim = $this->dannie_vichet->otnim - $this->dannie_vichet->maxfot;
        $this->dannie_vichet->sleduch = $this->dannie_vichet->obschKONST - $this->dannie_vichet->otnim;
        */

        $this->dannie_vichet->fotok_popalo += 1;

        if($this->dannie_vichet->fotok_popalo >= $this->dannie_vichet->maxfot){
            $this->dannie_vichet->fotok_popalo = 0;
            $this->dannie_vichet->nomermesta += 1;
            $this->dannie_vichet->maxfot = ceil(($this->dannie_vichet->obschKONST - $this->dannie_vichet->otnim) / (6 - $this->dannie_vichet->nomermesta));
        }
    }

    public function testrand(){
        $massbdadmin = $this->chtenie("fotvadmnke.txt")[0];

        $obschkolich_fotok = count($massbdadmin['fotki']);
        $maxfot_random = ceil($obschkolich_fotok / 5);
        //$this->dannie_vichet->sleduch = 0;

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
            $this->otnyatotobsch($this->dannie_vichet->nomermesta);

            echo $this->dannie_vichet->nomermesta;

            /*
            if($i <= $maxfot_random){
                $raspred1['urov1'][] = $massfot;
                $this->otnyatotobsch(5);
            }
            elseif($i > $this->dannie_vichet->sleduch and $i <= $this->dannie_vichet->sleduch + $this->dannie_vichet->maxfot){
                $raspred1['urov2'][] = $massfot;
                $this->otnyatotobsch(4);
                $kolich1 = $this->dannie_vichet->sleduch;
            }
            elseif($i > $this->dannie_vichet->sleduch and $i <= $this->dannie_vichet->sleduch + $this->dannie_vichet->maxfot){
                $raspred1['urov3'][] = $massfot;
                $this->otnyatotobsch(3);
            }
            elseif($i > $this->dannie_vichet->sleduch and $i <= $this->dannie_vichet->sleduch + $this->dannie_vichet->maxfot){
                $raspred1['urov4'][] = $massfot;
                $this->otnyatotobsch(2);
            }
            elseif($i > $this->dannie_vichet->sleduch and $i <= $this->dannie_vichet->sleduch + $this->dannie_vichet->maxfot){
                $raspred1['urov5'][] = $massfot;
                $this->otnyatotobsch(1);
            }
            elseif($i > 5){
                break;
            }
            */

        }

        $vivfunc[0] = $raspred1;

        return $vivfunc;
    }

}

$test_random = new test_random;

echo "<pre>";
print_r($test_random->testrand()[0]);
echo "</pre>";

?>