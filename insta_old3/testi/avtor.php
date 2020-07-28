<?php

require_once ($_SERVER['DOCUMENT_ROOT']."/insta/class/main.php");

$test_random = new maininsta;

echo "<pre>";
print_r($test_random->vivavtora("https://www.instagram.com/p/BaHCNYSFQLY/?taken-by=design.sklad")[0]);
echo "</pre>";

?>