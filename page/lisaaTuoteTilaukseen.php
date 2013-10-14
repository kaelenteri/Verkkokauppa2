<?php

use Verkkokauppa2\Entity\Tilaus as Tilaus;
use Verkkokauppa2\Entity\Tuote as Tuote;
use Verkkokauppa2\Entity\TietokantaAvustaja as TKAvustaja;

include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/Tilaus.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/Tuote.php";

session_start();



if (!isset($_POST['id']) || !isset($_POST['maara'])) {
    die("Olet tullut tälle sivulle väärää kautta. Palaa takaisin.");
} else {



    $id = $_POST['id'];
    $maara = $_POST['maara'];
    if(!preg_match("/([1-9]+)[0-9]{0,2}/", $maara)){
        die("Antamasi lisättävien tuotteiden määrä on virheellinen. Annoit '$maara'");
    }
    
    $yhteys = TKAvustaja::avaaSQL();
    $tuote = Tuote::etsiTuoteIdLla($id, $yhteys);
    if ($tuote === null) {
        die("Tietokannasta ei löytynyt tuotetta. Ota yhteys järjestelmänvalvojaan.");
    } else {
        $tilaus;
        if (!isset($_SESSION['tilaus'])) {
            $tilaus = new Tilaus();
            $_SESSION['tilaus'] = $tilaus;
        } else {
            $tilaus = $_SESSION['tilaus'];
        }

        $tilaus->lisaaTuoteTilaukseen($tuote, $maara);
        //$_SESSION['tilaus'] = $tilaus;

        $lastURL = $_SERVER['HTTP_REFERER'];
        header("Location: $lastURL");
    }
}