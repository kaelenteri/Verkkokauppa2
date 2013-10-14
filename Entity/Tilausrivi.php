<?php

namespace Verkkokauppa2\Entity;

use Verkkokauppa2\Entity\Tuote as Tuote;
use Verkkokauppa2\Entity\Tilaus as Tilaus;

/**
 * Description of Tilausrivi
 *
 * @author Make
 */
class Tilausrivi {

    /**
     *
     * @var $id integer 
     */
    private $id;

    /**
     *
     * @var $tilaus \Verkkokauppa2\Entity\Tilaus 
     */
    private $tilaus;

    /**
     *
     * @var $tuote \Verkkokauppa2\Entity\Tuote
     */
    private $tuote;

    /**
     *
     * @var $kplMaara 
     */
    private $kplMaara;

    /**
     * 
     * @param $id
     * @param \Verkkokauppa2\Entity\Tilaus $tilaus
     * @param \Verkkokauppa2\Entity\Tuote $tuote
     * @param  $kplMaara
     */
    function __construct($kplMaara = null, $id = null, Tuote $tuote = null, Tilaus $tilaus = null) {
        $this->id = $id;
        $this->tilaus = $tilaus;
        $this->tuote = $tuote;
        if($this->getTuote()->getVarastoTilanne() < $kplMaara){
            $kplMaara = $this->getTuote()->getVarastoTilanne();
        }
        $this->kplMaara = $kplMaara;
        
    }

    /**
     * 
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    public function kasvataKplMaaraa($maara) {
        $varastotilanne = $this->getTuote()->getVarastoTilanne();
        if($this->getTuote()->getVarastoTilanne() < $maara){
            $maara = $varastotilanne;
        }
        $this->kplMaara += $maara;
    }

    public function vahennaKplMaaraa($maara) {

        $this->kplMaara -= $maara;
    }

    public function muutaKplMaaraa($maara) {
        if ($maara < 0) {
            $this->vahennaKplMaaraa($maara);
        } else if ($maara > 0) {
            $this->kasvataKplMaaraa($maara);
        }
    }

    public function paivitaVarastoTilanne(\mysqli $yhteys) {
        if (!$yhteys) {
            die("Yhteydessä on jotain vikaa.");
        } else {
            $kplMaara = $this->getKplMaara();
            $tuoteId = $this->getTuote()->getId();
            $kysely = "UPDATE tuote SET varastotilanne = (varastotilanne - $kplMaara) WHERE id = '$tuoteId'";

            $yhteys->query($kysely);
            if ($yhteys->affected_rows == 1) {
                echo "1 rivi muutettu";
                return true;
            } else {
                echo "Jotain meni pieleen tuotteet $tuoteId muuttamisessa.";
                return false;
            }
        }
    }

    /**
     * 
     * @param $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * 
     * @return \Verkkokauppa2\Entity\Tilaus
     */
    public function getTilaus() {
        return $this->tilaus;
    }

    /**
     * 
     * @param \Verkkokauppa2\Entity\Tilaus $tilaus
     */
    public function setTilaus(Tilaus $tilaus) {
        $this->tilaus = $tilaus;
    }

    /**
     * 
     * @return \Verkkokauppa2\Entity\Tuote
     */
    public function getTuote() {
        return $this->tuote;
    }

    /**
     * 
     * @param \Verkkokauppa2\Entity\Tuote $tuote
     */
    public function setTuote($tuote) {
        $this->tuote = $tuote;
    }

    /**
     * 
     * @return integer
     */
    public function getKplMaara() {
        return $this->kplMaara;
    }

    /**
     * 
     * @param integer $kplMaara
     */
    public function setKplMaara($kplMaara) {
        if ($kplMaara < 0) {
            $this->kplMaara = 0;
        } else {
            $this->kplMaara = $kplMaara;
        }
    }

    public function getTilausrivinKokonaishinta() {
        return $this->getKplMaara() * $this->getTuote()->getHinta();
    }

}

?>
