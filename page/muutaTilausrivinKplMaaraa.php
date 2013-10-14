<?php

use Verkkokauppa2\Entity\Tilaus as Tilaus;
use Verkkokauppa2\Entity\Tilausrivi as Tilausrivi;
use Verkkokauppa2\Entity\Tuote as Tuote;

include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/Tilaus.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/Tuote.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/Tilausrivi.php";

session_start();

if (!isset($_POST['tuoteId']) || !isset($_POST['maara']) || !isset($_SESSION['tilaus'])) {
    die("Olet tullut tälle sivulle väärää kautta.");
} else {
    /* @var $tilaus Tilaus */
    $tilaus = $_SESSION['tilaus'];
    $maara = $_POST['maara'];

    $tuoteId = $_POST['tuoteId'];


    if (!preg_match_all("/^[0-9]{1,3}$/", $maara)) {
        die(utf8_decode("Antamasi lisättävien tuotteiden määrä on virheellinen. Annoit '$maara'"));
    }

    $maara = intval($maara);
    echo $maara;
    $onnistui = $tilaus->muutaTilausRivinArvoa($tuoteId, $maara);
    if ($onnistui) {
        //echo utf8_decode("Tuotteen (id = $tuoteId) määrää muutettu.");
        $lastURL = $_SERVER['HTTP_REFERER'];
        //header("Location: $lastURL");
    } else {
        echo "Jotain meni vikaan.";
    }
}