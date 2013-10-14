<?php

namespace Verkkokauppa2\Entity;

use Verkkokauppa2\Entity\Tilausrivi as Tilausrivi;
use Verkkokauppa2\Entity\Tuote as Tuote;
use Verkkokauppa2\Entity\TietokantaAvustaja as TietokantaAvustaja;

include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/Tilausrivi.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/TietokantaAvustaja.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/Verkkokauppa2/Entity/Tuote.php";

/**
 * Description of Tilaus
 *
 * @author Make
 */
class Tilaus {

    /**
     *
     * @var integer $id
     */
    private $id;

    /**
     *
     * @var integer $status
     */
    private $status;
 
    /**
     *
     * @var integer $aika 
     */
    private $aika;

    /**
     *
     * @var string $toimitusosoite 
     */
    private $toimitusosoite;

    /**
     *
     * @var Tilausrivi[] $tilausrivit;
     */
    private $tilausrivit = array();

    /**
     * 
     * @param \Verkkokauppa2\Entity\type $status
     * @param type $aika
     * @param type $toimitusosoite
     * @param \Verkkokauppa2\Entity\Tilausrivi $tilausrivit
     * @param type $id
     */
    function __construct($status = "odottaa", $aika = null, $toimitusosoite = null, $id = null) {
        $this->id = $id;
        $this->status = $status;
        $this->aika = $aika;
        $this->toimitusosoite = $toimitusosoite;
    }

    public function tallennaTilausTietokantaan($tallennaMyosrivit) {
        $yhteys = TietokantaAvustaja::avaaSQL();
        if (!$yhteys) {
            die('Could not connect to MySQL: ' . mysqli_connect_error());
        }
        $aika = $this->getAika();
        if ($aika === null) {
            $aika = time();
        }
        $kysely = "INSERT INTO tilaus (status, aika) VALUES ('odottaa', '$aika')";

        $tulos = $yhteys->query($kysely);


        if (!$tulos) {
            die("Tietokantayhteydessä on jotain vikaa.");
        }
        $this->setId($yhteys->insert_id);
        if ($tallennaMyosrivit === true) {
            $this->tallennaTilausrivitTietokantaan($yhteys);
        }
        $yhteys->close();
    }

    public function tallennaTilausrivitTietokantaan($yhteys) {

        foreach ($this->getTilausrivit() as $tl) {

            $tilausId = $this->getId();
            $tuoteId = $tl->getTuote()->getId();
            $kplMaara = $tl->getKplMaara();
            $kysely = "INSERT INTO tilausrivi (tilausId, tuoteId, kplMaara) VALUES ('$tilausId', '$tuoteId', '$kplMaara')";

            $yhteys->query($kysely);
            if ($yhteys->affected_rows <= 0) {
                die("Tietokannassa on jotain vikaa 2.");
            }
            $onnistuikoVarastotilanteenPaivitys = $tl->paivitaVarastoTilanne($yhteys);

            $tl->setId($yhteys->insert_id);
        }
    }

    public function lisaaTuoterivi(Tilausrivi $tilausrivi) {
        $this->tilausrivit[] = $tilausrivi;
    }

    public function lisaaTuoteTilaukseen(Tuote $tuote, $maara) {
        if ($this->loytyykoTuoteTilausriveista($tuote->getId())) {
            $this->kasvataTilausrivinArvo($tuote->getId(), $maara);
            return true;
        } else {

            $this->lisaaTuoterivi(new Tilausrivi(intval($maara), null, $tuote, $this));
            return true;
        }
    }

    public function kasvataTilausrivinArvo($tuoteId, $maara) {
        foreach ($this->getTilausrivit() as $tl) {
            if ($tl->getTuote()->getId() == $tuoteId) {
                $tl->kasvataKplMaaraa($maara);
                return true;
            }
        }
    }

    public function muutaTilausRivinArvoa($tuoteId, $maara) {

        if ($maara == 0) {
 
            $this->poistaTilausRiviTilauksesta($tuoteId);
            return true;
        } else {

            foreach ($this->getTilausrivit() as $tl) {
                if ($tl->getTuote()->getId() == $tuoteId) {
                    if ($tl->getKplMaara() > $maara) {
                        $tl->setKplMaara($maara);
                        return true;
                    }
                    if ($tl->getKplMaara() + $maara > $tl->getTuote()->getVarastoTilanne()) {
                        $maara = $tl->getTuote()->getVarastoTilanne();
                    }

                    $tl->setKplMaara($maara);
                    return true;
                }
            }
            return false;
        }
    }

    public function poistaTilausRiviTilauksesta($tuoteId) {
        foreach ($this->tilausrivit as $tr) {
                       
            if ($tr->getTuote()->getId() == $tuoteId) {
                
                unset($tr);
            }
        }
    }

    public function getTilauksenSumma() {
        $yhteensa = 0;
        foreach ($this->getTilausrivit() as $rivi) {
            $yhteensa += $rivi->getTuote()->getHinta() * $rivi->getKplMaara();
        }
        return $yhteensa;
    }

    public function tulostaTilaus($tableID = null) {

        if ($tableID === null) {
            echo "<table>";
        } else {
            echo "<table id=\"$tableID\">";
        }


        echo "<tr>";
        echo "<td>";
        echo "TilausID";
        echo "</td>";
        echo "<td>";
        echo $this->getId();
        echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>";
        echo "Tilauksen aika";
        echo "</td>";
        echo "<td>";
        echo date("d.m.Y H:i:s", $this->getAika());
        echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>";
        echo "Toimitusosoite";
        echo "</td>";
        echo "<td>";
        echo $this->getToimitusosoite();
        echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>";
        echo "Tila";
        echo "</td>";
        echo "<td>";
        echo $this->getStatus();
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        echo "<br />";

        $this->tulostaTilausrivit($tableID);
    }

    public function tulostaTilausrivit($tableID = null) {
        if ($tableID === null) {
            echo "<table>";
        } else {
            echo "<table id=\"$tableID\">";
        }

        echo "<tr>";
        echo "<td>";
        echo "TuoteID";
        echo "</td>";
        echo "<td>";
        echo "Tuotenimi";
        echo "</td>";
        echo "<td>";
        echo "Yksikköhinta";
        echo "</td>";
        echo "<td>";
        echo "Yhteishinta";
        echo "</td>";
        echo "<td>";
        echo "Määrä";
        echo "</td>";
        echo "<td>";
        echo "Päivitä";
        echo "</td>";
        echo "</tr>";


        foreach ($this->getTilausrivit() as $rivi) {


            echo "<tr>";
            echo "<td>";
            echo $rivi->getTuote()->getId();
            echo "</td>";
            echo "<td>";
            echo $rivi->getTuote()->getNimi();
            echo "</td>";


            echo "<td>";
            echo $rivi->getTuote()->getHinta();
            echo "</td>";

            echo "<td>";
            echo $rivi->getTilausrivinKokonaishinta();
            echo "</td>";

            echo "<form action=\"muutaTilausrivinKplMaaraa.php\" method=\"post\">";
            echo "<td>";

            echo "<input type=\"text\" name=\"maara\" value=\"" . $rivi->getKplMaara() . "\" size=\"1\" />";
            echo "<input type=\"hidden\" name=\"tuoteId\" value=\"" . $rivi->getTuote()->getId() . "\" />";
            echo "</td>";

            echo "<td>";
            echo "<input type=\"submit\" value=\"Päivitä\" />";
            echo "</td>";
            echo "</form>";
            echo "</tr>";
        }


        echo "</table>";
        echo "<p>Yhteensä maksaa: " . $this->getTilauksenSumma() . "</p>";
    }

    public function loytyykoTuoteTilausriveista($tuoteId) {
        foreach ($this->getTilausrivit() as $tl) {
            if ($tl->getTuote()->getId() == $tuoteId) {
                return true;
            }
        }
        return false;
    }

    public static function etsiTilaus($id) {
        $yhteys = TietokantaAvustaja::avaaSQL();
        if (!$yhteys) {
            die("Tietokantayhteyttä ei voida muodostaa.");
        }
        $kysely = "SELECT * FROM tilaus WHERE id = '$id'";
        $tulos = $yhteys->query($kysely);

        if (!$tulos) {
            die("Tietokannasta ei löytynyt tilausta antamallasi tilausnumerolla '$id'");
        }
        $tilaus = new Tilaus();
        while ($r = $tulos->fetch_assoc()) {

            $tilaus->setId($r['id']);
            $tilaus->setStatus($r['status']);
            $tilaus->setToimitusosoite($r['toimitusosoite']);
            $tilaus->setAika($r['aika']);
        }

        $tilId = $tilaus->getId();
        $kysely2 = "SELECT * FROM tilausrivi WHERE tilausId = '$tilId'";
        var_dump($kysely2);
        $tulos = $yhteys->query($kysely2);
        if (!$tulos) {
            echo "Yhteydessä on jotain vikaa.";
        }

        while ($row = $tulos->fetch_assoc()) {
            $tl = new Tilausrivi();
            $tl->setTilaus($tilaus);
            $tl->setId($row['id']);
            $tl->setKplMaara($row['kplMaara']);

            $t = Tuote::etsiTuoteIdLla($row['tuoteId'], $yhteys);
            $tl->setTuote($t);
            $tilaus->tilausrivit[] = $tl;
        }

        $yhteys->close();
        return $tilaus;
    }

    public function getTilauksenTuotteidenYhteismaara() {
        $yht = 0;
        foreach ($this->getTilausrivit() as $tl) {
            $yht += $tl->getKplMaara();
        }
        return $yht;
    }

    /**
     * 
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @param integer $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * 
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * 
     * @param integer $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * 
     * @return integer
     */
    public function getAika() {
        return $this->aika;
    }

    /**
     * 
     * @param integer $aika
     */
    public function setAika($aika) {
        $this->aika = $aika;
    }

    /**
     * 
     * @return string
     */
    public function getToimitusosoite() {
        return $this->toimitusosoite;
    }

    /**
     * 
     * @param string $toimitusosoite
     */
    public function setToimitusosoite($toimitusosoite) {
        $this->toimitusosoite = $toimitusosoite;
    }

    /**
     * 
     * @return Tilausrivi[];
     */
    public function getTilausrivit() {
        return $this->tilausrivit;
    }

    /**
     * 
     * @param \Verkkokauppa2\Entity\Tilausrivi[] $tilausrivit
     */
    public function setTilausrivit(array $tilausrivit) {
        $this->tilausrivit = $tilausrivit;
    }

}

?>
