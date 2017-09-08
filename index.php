<?php

require_once( 'parser.php' );

$resultat['fr'] = Parser::getInstance()->process( 'fr' );
$resultat['de'] = Parser::getInstance()->process( 'de' );



$handle = fopen("./ffr.csv", "w");
foreach ( $resultat as $item )
    foreach ( $item as $fields ) 
        fputcsv($handle, $fields);

fclose($handle);

echo 'done';