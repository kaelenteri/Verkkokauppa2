<?php
use Verkkokauppa2\Entity\Tilaus as Tilaus;
use Verkkokauppa2\Entity\Tuote as Tuote;
use Verkkokauppa2\Entity\TietokantaAvustaja as TKAvustaja;

include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/Tilaus.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/Tuote.php";
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="../css/tyylit.css">
        <title></title>
    </head>
    <body>
        <?php
       
        if(!isset($_SESSION['tilaus'])){
            $_SESSION['tilaus'] = new Tilaus();
            
        }
        /* @var $tilaus Tilaus */
        $tilaus = $_SESSION['tilaus'];
        
        
        $tilaus->tulostaTilausrivit("tilaus");

        
        ?>
        
        <form action="testi/tuhoaSessio.php" method="post"><input type="submit" value="Poista tilaus" /></form>
        <form action="tallennaTilaus.php" method="post"><input type="submit" value="Tee tilaus" /></form>
    </body>
</html>
