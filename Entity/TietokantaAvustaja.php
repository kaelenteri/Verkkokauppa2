<?php

namespace Verkkokauppa2\Entity;

/**
 * Description of TietokantaAvustaja
 *
 * @author Make
 */
class TietokantaAvustaja {
    public static function avaaSQL(){
        return $yhteys = new \mysqli('localhost', 'make', 'toppi', 'verkkokauppa2');
    }
}

?>
