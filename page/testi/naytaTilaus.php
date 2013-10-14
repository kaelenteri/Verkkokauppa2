<?php 
include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/Tilausrivi.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/Tilaus.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/Tuote.php";


?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="../../css/tyylit.css">

        <title></title>
    </head>
    <body>
        <?php
        if(!isset($_GET['tilausId'])){
            die("Et ole antanut tunnusta tilaukselle");
        }
        $tid = $_GET['tilausId'];
        $tilaus = \Verkkokauppa2\Entity\Tilaus::etsiTilaus($tid);
        $tilaus->tulostaTilaus("tilaus");

        ?>
    </body>
</html>
