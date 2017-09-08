<?php

require_once( 'vendor/autoload.php' );

use Sunra\PhpSimple\HtmlDomParser;

class Parser {
    
    private static $instance;
    
    private function __construct(){}
    
    public static function getInstance() {
        if( self::$instance == null ) {
            self::$instance = new Parser();
        }
        
        return self::$instance;
    }
    
    private static $categories_href_de = [
        ['href' => 'https://fotoservice.fuji.ch/de/fotos-poster', 'lng' => 'de'],
        ['href' => 'https://fotoservice.fuji.ch/de/Fotobuch', 'lng' => 'de'],
        ['href' => 'https://fotoservice.fuji.ch/de/handy-tablet', 'lng' => 'de'],
        ['href' => 'https://fotoservice.fuji.ch/de/wandbilder', 'lng' => 'de'],
        ['href' => 'https://fotoservice.fuji.ch/de/Fotokalender', 'lng' => 'de'],
        ['href' => 'https://fotoservice.fuji.ch/de/Fotokarten', 'lng' => 'de'],
        ['href' => 'https://fotoservice.fuji.ch/de/Fotogeschenke', 'lng' => 'de']
    ];
     
    private static $categories_href_fr = [   
        ['href' => 'https://fotoservice.fuji.ch/de/fotos-poster?locale=fr_CH', 'lng' => 'fr'],
        ['href' => 'https://fotoservice.fuji.ch/de/Fotobuch?locale=fr_CH', 'lng' => 'fr'],
        ['href' => 'https://fotoservice.fuji.ch/de/handy-tablet?locale=fr_CH', 'lng' => 'fr'],
        ['href' => 'https://fotoservice.fuji.ch/de/wandbilder?locale=fr_CH', 'lng' => 'fr'],
        ['href' => 'https://fotoservice.fuji.ch/de/Fotokalender?locale=fr_CH', 'lng' => 'fr'],
        ['href' => 'https://fotoservice.fuji.ch/de/Fotokarten?locale=fr_CH', 'lng' => 'fr'],
        ['href' => 'https://fotoservice.fuji.ch/de/Fotogeschenke?locale=fr_CH', 'lng' => 'fr']
    ];
    
    public function process ( $lang ) {
        
        $product_array = [];
        
        $categories_href = ( $lang == 'fr' ) ? self::$categories_href_fr : self::$categories_href_de;
        
        foreach ( $categories_href as $category_href ) {
        
            $dom = HtmlDomParser::file_get_html( $category_href['href'] );
            $items = $dom->find( '.teaser-content' );
            $category = $dom->find( '.breadcrump a', -1 )->plaintext;
            $language = $category_href['lng'];
            $product_href_array = [];
            
            foreach ( $items as $item ) {
                
                $href = [];
                
                $href['href'] = 'https://fotoservice.fuji.ch' . $item->find( 'a ', 0 )->href;
                $href['price'] = $item->find( '[data-price]', 0 )->attr['data-price'];
                $href['desc'] = $item->find( '.copy', 0 )->plaintext;
                
                if( $href['href'] == 'https://fotoservice.fuji.ch' ) {
                    continue;
                }
                
                if( $lang == 'fr' ) {
                    $href['href'] .= '?locale=fr_CH';
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
                // $product['price'] = ( $dom->find( '.pricerow strong', 0 )->plaintext == ' ' )
                    // ?trim($item['price'])
                    // :trim($dom->find( '.pricerow strong', 0 )->plaintext);
                $product['name'] = $dom->find( '.producthead', 0 )->plaintext ?? false;
                
                // $product['imgs'][] = $dom->find( '[property="og:image"]', 0 )->attr['content'] ?? false;
                // $product['desc'] = htmlspecialchars( $dom->find( '.detail-wrapper', 0 )->innertext ) ?? false;
                // $product['desc'] = $item['desc'] ?? false;
                
                $productId = explode( 'productId=', $dom->find( '.breadcrump [href^="/products/details.html?productId="]', 0 )->href )[1];
                
                // $product['product-id'] = $productId ?? 
                //                             ( $lang == 'fr' 
                //                                 ? explode( '%26locale%3Dfr_CH', explode( 'productId%3D', $dom->find( '.targetPage', 0 )->attr['data-href'] )[1] )[0]
                //                                 : explode( 'productId%3D', $dom->find( '.targetPage', 0 )->attr['data-href'] )[1] );
                // $product['category'] = $category ?? false;
                // $product['language'] = $language ?? 'de';
                $product_array[] = $product;
                
            }
        
        }
        
        return $product_array;
    }
}