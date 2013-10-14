<?php

namespace Verkkokauppa2\Entity;

/**
 * Description of Tuote
 *
 * @author Make
 */
class Tuote {

    private $id;
    private $nimi;
    private $kuvaus;
    private $hinta;
    private $varastoTilanne;
    private $tagit;
    private $kuvaURL;
    private $tarjoushinta;
    private $paino;

    function __construct($nimi, $kuvaus, $hinta, $varastoTilanne, $tagit, $kuvaURL, $tarjoushinta, $paino, $id = null) {
        $this->id = $id;
        $this->nimi = $nimi;
        $this->kuvaus = $kuvaus;
        $this->hinta = $hinta;
        $this->varastoTilanne = $varastoTilanne;
        $this->tagit = $tagit;
        $this->kuvaURL = $kuvaURL;
        $this->tarjoushinta = $tarjoushinta;
        $this->paino = $paino;
    }

    public static function etsiTuoteIdLla($id, $yhteys) {

        if (!$yhteys) {
            die("Yhteydessä on jotain vikaa taas.");
        }
        $t = null;
        $kysely = "SELECT * FROM tuote WHERE id = '$id' LIMIT 1";

        $tulos = $yhteys->query($kysely);
        if (!$tulos) {
            die("Yhteydessä on jotain vikaa taas.");
        }

        while ($r = $tulos->fetch_assoc()) {

            $t = new Tuote($r['nimi'], $r['kuvaus'], $r['hinta'], $r['varastotilanne'], $r['tagit'], $r['kuvaURL'], $r['tarjoushinta'], $r['paino'], $r['id']);
        }
        return $t;
    }



    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getNimi() {
        return $this->nimi;
    }

    public function setNimi($nimi) {
        $this->nimi = $nimi;
    }

    public function getKuvaus() {
        return $this->kuvaus;
    }

    public function setKuvaus($kuvaus) {
        $this->kuvaus = $kuvaus;
    }

    public function getHinta() {
        return $this->hinta;
    }

    public function setHinta($hinta) {
        $this->hinta = $hinta;
    }

    public function getVarastoTilanne() {
        return $this->varastoTilanne;
    }

    public function setVarastoTilanne($varastoTilanne) {
        $this->varastoTilanne = $varastoTilanne;
    }

    public function getTagit() {
        return $this->tagit;
    }

    public function setTagit($tagit) {
        $this->tagit = $tagit;
    }

    public function getKuvaURL() {
        return $this->kuvaURL;
    }

    public function setKuvaURL($kuvaURL) {
        $this->kuvaURL = $kuvaURL;
    }

    public function getTarjoushinta() {
        return $this->tarjoushinta;
    }

    public function setTarjoushinta($tarjoushinta) {
        $this->tarjoushinta = $tarjoushinta;
    }

    public function getPaino() {
        return $this->paino;
    }

    public function setPaino($paino) {
        $this->paino = $paino;
    }

}

?>
