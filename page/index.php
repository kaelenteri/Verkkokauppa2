<?php 

use Verkkokauppa2\Entity\Tilaus as Tilaus;


?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <h1>Tervetuloa verkkokauppaan</h1>
        <?php
        session_start();
        $_SERVER['tilaus'] = new Tilaus();
        
        ?>
    </body>
</html>
