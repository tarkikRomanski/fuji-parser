<?php

require_once( 'vendor/autoload.php' );

use Sunra\PhpSimple\HtmlDomParser;

$categories_href = [
    'https://fotoservice.fuji.ch/de/fotos-poster',
    'https://fotoservice.fuji.ch/de/Fotobuch',
    'https://fotoservice.fuji.ch/de/handy-tablet',
    'https://fotoservice.fuji.ch/de/wandbilder',
    'https://fotoservice.fuji.ch/de/Fotokalender',
    'https://fotoservice.fuji.ch/de/Fotokarten',
    'https://fotoservice.fuji.ch/de/Fotogeschenke'
];

$product_array = [];

foreach ( $categories_href as $category_href ) {

    $dom = HtmlDomParser::file_get_html( $category_href );
    
    $items = $dom->find( '.teaser-content' );
    $category = $dom->find( '.breadcrump a', -1 )->plaintext;
    
    $product_href_array = [];
    
    foreach ( $items as $item ) {
        
        $href = [];
        
        $href['href'] = 'https://fotoservice.fuji.ch' . $item->find( 'a ', 0 )->href;
        $href['price'] = $item->find( '[data-price]', 0 )->attr['data-price'];
        
        if( $href == 'https://fotoservice.fuji.ch' ) {
            continue;
        }
        
        $product_href_array[] = $href;
    }
    
    
    
    foreach ( $product_href_array as $item ) {
        
        $dom = HtmlDomParser::file_get_html( $item['href'] );
    
        $product = [];
        
        if( $dom === false ) {
            continue;
        }
        
        $product['href'] = $item['href'] ?? false;
        $product['price'] = ( $dom->find( '.pricerow strong', 0 )->plaintext == ' ' )
            ?trim($item['price'])
            :trim($dom->find( '.pricerow strong', 0 )->plaintext);
        $product['name'] = $dom->find( '.producthead', 0 )->plaintext ?? false;
        $product['imgs'][] = $dom->find( '[property="og:image"]', 0 )->attr['content'] ?? false;
        $product['desc'] = htmlspecialchars( $dom->find( '.detail-wrapper', 0 )->innertext ) ?? false;
        
        $productId = explode( 'productId=', $dom->find( '.breadcrump [href^="/products/details.html?productId="]', 0 )->href )[1];
        
        $product['product-id'] = $productId ?? explode( 'productId%3D', $dom->find( '.targetPage', 0 )->attr['data-href'] )[1];
        $product['category'] = $category ?? false;
        
        $product_array[] = $product;
        
    }

}

var_dump( $product_array );