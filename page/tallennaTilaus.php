<?php
use Verkkokauppa2\Entity\Tilaus as Tilaus;

//use Verkkokauppa2\Entity\TietokantaAvustaja as TKAvustaja;

//include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/TietokantaAvaustaja.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/Tilaus.php";
session_start();

if(!isset($_SESSION['tilaus'])){
    die("Olet tullut tälle sivulle väärää reittiä.");
}



/* @var $tilaus Tilaus */
$tilaus = $_SESSION['tilaus'];

if(count($tilaus->getTilausrivit()) <= 0){
    die("Tilauksessasi ei ole tuotteita. Lisää tilaukseen tuotteita ennen ostosten maksamista.");
}
$tilaus->tallennaTilausTietokantaan(true);
$tilaus->tulostaTilausrivit();
session_destroy();