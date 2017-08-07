<?php

require_once( 'parser.php' );

$resultat = Parser::getInstance()->process('de');

var_dump($resultat);