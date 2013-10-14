<?php

use Verkkokauppa2\Entity\Tuote as Tuote;
use Verkkokauppa2\Entity\Tilaus as Tilaus;
use Verkkokauppa2\Entity\TietokantaAvustaja as TKAvustaja;

include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/Tilausrivi.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/Tilaus.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/Tuote.php";

session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        if (!isset($_SESSION['tilaus'])) {
            $_SESSION['tilaus'] = new Tilaus();
        }
        $tilaus = $_SESSION['tilaus'];


        if (!isset($_GET['id'])) {
            echo "Et ole antanut tuotteen id:tä. Tarkista URL.";
        } else {
            $ostoksetKorissa = $tilaus->getTilauksenTuotteidenYhteismaara();
            echo "<p><a href=\"ostoskori.php\">Ostoskori [$ostoksetKorissa]</a></p>";
            $id = $_GET['id'];
            $yhteys = TKAvustaja::avaaSQL();
            $tuote = Tuote::etsiTuoteIdLla($id, $yhteys);
            if ($tuote === null) {
                echo "Tuotetta antamallasi ID:llä ei löydy. Tarkista URL";
            } else {
                ?>

                <p><?php echo $tuote->getNimi() . " (#" . $tuote->getId() . ")" ?></p>
                <?php
                if ($tuote->getTarjoushinta() === null) {
                    echo "<p>" . $tuote->getHinta() . "</p>";
                } else {
                    echo "<p style=\"text-decoration: line-through; color: red;\">" . $tuote->getHinta() . "</p>";
                    echo "<p>Tarjouksessa vain " . $tuote->getTarjoushinta() . "</p>";
                }
                ?>
                <p>Tuotetta varastossa: <?php echo $tuote->getVarastoTilanne()?>kpl</p>
                <p><form action="lisaaTuoteTilaukseen.php" method="post">
                    <input type="text" pattern="([1-9]+)[0-9]{0,2}" name="maara"/>
                    <input type="hidden" name="id" value="<?php echo $id ?>" />
                    <input type="submit" value="Lisää tuote ostoskoriin." />
                </form>
            
        </p>
        <p><?php echo utf8_encode($tuote->getKuvaus()) ?></p>
        <p><img id="tuote" src="../kuvat/<?php echo $tuote->getId() . ".jpg" ?>" alt="<?php echo "Kuva: " . $tuote->getNimi() ?>" /></p>



        <?php
        //var_dump($tilaus);
    }
}
?>
</body>
</html>
