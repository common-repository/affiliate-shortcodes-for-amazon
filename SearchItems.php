<?php

/**
 * Copyright 2019 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

/*
 * ProductAdvertisingAPI
 *
 * https://webservices.amazon.com/paapi5/documentation/index.html
 */

/*
 * This sample code snippet is for ProductAdvertisingAPI 5.0's SearchItems API
 *
 * For more details, refer: https://webservices.amazon.com/paapi5/documentation/search-items.html
 */

use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\Configuration;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductAdvertisingAPIClientException;

require_once(__DIR__ . '/vendor/autoload.php'); // change path as needed

function amzafs_searchItemsAmz($keyword, $categoryId)
{
    $config = new Configuration();
	$categoryId=$categoryId;
	$amz_options=get_option( 'amz_short_options' );
	$access_key=trim($amz_options["access_key"]);
	$secret_key=trim($amz_options["secret_key"]);
	$asoc_tag=trim($amz_options["asoc_tag"]);
	$product_number=trim($amz_options["product_number"]);
	$localeAmazon=trim($amz_options["select_field_locale"]);	
	$post_id="";
    /*
     * Add your credentials
     * Please add your access key here
     */
	$config->setAccessKey($access_key);
    # Please add your secret key here
	$config->setSecretKey($secret_key);	
    # Please add your partner tag (store/tracking id) here
    $partnerTag = $asoc_tag;
    /*
     * PAAPI host and region to which you want to send request
     * For more details refer: https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
     */

	if ($localeAmazon=="us"){
		$config->setHost('webservices.amazon.com');
		$config->setRegion('us-east-1');
	} elseif ($localeAmazon=="uk"){
		$config->setHost('webservices.amazon.co.uk');
		$config->setRegion('eu-west-1');
	} elseif ($localeAmazon=="es"){
	    $config->setHost('webservices.amazon.es');
		$config->setRegion('eu-west-1');	
	} elseif ($localeAmazon=="mx"){
	    $config->setHost('webservices.amazon.com.mx');
		$config->setRegion('us-east-1');	
	} elseif ($localeAmazon=="jp"){
	    $config->setHost('webservices.amazon.co.jp');
		$config->setRegion('us-west-2');	
	} elseif ($localeAmazon=="it"){
	    $config->setHost('webservices.amazon.it');
		$config->setRegion('eu-west-1');	
	} elseif ($localeAmazon=="in"){
	    $config->setHost('webservices.amazon.in');
		$config->setRegion('eu-west-1');	
	} elseif ($localeAmazon=="de"){
	    $config->setHost('webservices.amazon.de');
		$config->setRegion('eu-west-1');	
	} elseif ($localeAmazon=="fr"){
	    $config->setHost('webservices.amazon.fr');
		$config->setRegion('eu-west-1');	
	} elseif ($localeAmazon=="ca"){
	    $config->setHost('webservices.amazon.ca');
		$config->setRegion('us-east-1');	
	} elseif ($localeAmazon=="br"){
	    $config->setHost('webservices.amazon.com.br');
		$config->setRegion('us-east-1');	
	} elseif ($localeAmazon=="au"){
		$config->setHost('webservices.amazon.com.au');
		$config->setRegion('us-west-2');
	} elseif ($localeAmazon=="sg"){
		$config->setHost('webservices.amazon.sg');
		$config->setRegion('us-west-2');
	} elseif ($localeAmazon=="tr"){
		$config->setHost('webservices.amazon.com.tr');
		$config->setRegion('eu-west-1');
	} elseif ($localeAmazon=="ae"){
		$config->setHost('webservices.amazon.ae');
		$config->setRegion('eu-west-1');
	} else {
		$config->setHost('webservices.amazon.com');
		$config->setRegion('us-east-1');
	} 				
	
	


    $apiInstance = new DefaultApi(
    /*
     * If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
     * This is optional, `GuzzleHttp\Client` will be used as default.
     */
        new GuzzleHttp\Client(), $config);

    # Request initialization

    # Specify keywords
    $keyword = $keyword;

    /*
     * Specify the category in which search request is to be made
     * For more details, refer: https://webservices.amazon.com/paapi5/documentation/use-cases/organization-of-items-on-amazon/search-index.html
     */
    $searchIndex = "All";

    # Specify item count to be returned in search result
	//I need to convert text to number
	$itemCount = (int)$product_number;
	
	

    /*
     * Choose resources you want from SearchItemsResource enum
     * For more details, refer: https://webservices.amazon.com/paapi5/documentation/search-items.html#resources-parameter
     */
    $resources = array(
        SearchItemsResource::ITEM_INFOTITLE,
		"Images.Primary.Medium","Images.Primary.Large","ItemInfo.Features",
        SearchItemsResource::OFFERSLISTINGSPRICE);

    # Forming the request
    $searchItemsRequest = new SearchItemsRequest();
    $searchItemsRequest->setSearchIndex($searchIndex);
    $searchItemsRequest->setKeywords($keyword);
    $searchItemsRequest->setItemCount($itemCount);
    $searchItemsRequest->setPartnerTag($partnerTag);
    $searchItemsRequest->setPartnerType(PartnerType::ASSOCIATES);
    $searchItemsRequest->setResources($resources);

    # Validating request
    $invalidPropertyList = $searchItemsRequest->listInvalidProperties();
    $length = count($invalidPropertyList);
    if ($length > 0) {
        echo "Error forming the request", PHP_EOL;
        foreach ($invalidPropertyList as $invalidProperty) {
            echo $invalidProperty, PHP_EOL;
        }
        return;
    }

    # Sending the request
    try {
        $searchItemsResponse = $apiInstance->searchItems($searchItemsRequest);
     //   echo 'API called successfully', PHP_EOL;
     //   echo 'Complete Response: ', $searchItemsResponse, PHP_EOL;
        # Parsing the response
        if ($searchItemsResponse->getSearchResult() != null) {
    //       echo '<BR><BR>Printing first item information in SearchResult:', PHP_EOL;
            //$item = $searchItemsResponse->getSearchResult()->getItems()[0];

			$productLink="";
			$productImage="";
				/******************************************************************/			
				// BORRA TODOS LOS POSTS DE LA CATEGORÍA (SI SETTING ACTIVADA)
				/******************************************************************/	
				$options = get_option( 'amz_short_options' );
				$opcionBuena=$options["select_field_0"];
				if ($opcionBuena==1){
					//Opcional, borra TODOS los posts de la categoría (por si existían otros con otros titles distintos a los del update que no se van a borrar abajo)
					$query2 = new WP_Query(array(
					'post_type' => 'amz_product',          // name of post type.
					'tax_query' => array(
						array(
							'taxonomy' => 'amazon_shortcode',   // taxonomy name
							'field' => 'term_id',
							'terms' => $categoryId,                  // term id, term slug or term name
							)
						)
					));
					if ( $query2->have_posts() ) :
							while ( $query2->have_posts() ) : $query2->the_post();	
								$idPostParaBorrar=get_the_ID();
								//echo get_the_title();
								wp_delete_post($idPostParaBorrar);
							endwhile;
					endif;					
				}
				/******************************************************************/			
				// BORRA TODOS LOS POSTS DE LA CATEGORÍA (SI SETTING ACTIVADA)
				/******************************************************************/			
	
			$amazonContents="";			
			foreach ($searchItemsResponse->getSearchResult()->getItems() as $item){
				$pageExistente="";	
				$productLink="";
				$amazonFeatures="";
				$productPrice="";
				$productImage="";
				$postTitle="";			
				/*
				print "<pre>";
				print_r($item);
				print "</pre>";
				*/
				if ($item != null) {
					$amazonContents.="<br>";
					if ($item->getASIN() != null) {
		   //             echo "<BR><BR>ASIN: ", $item->getASIN(), PHP_EOL;
					}
					if ($item->getDetailPageURL() != null) {
						$amazonContents.="<BR><BR>DetailPageURL: ". $item->getDetailPageURL();
						$productLink=$item->getDetailPageURL();
					}
					if ($item->getImages() != null) {
						$imagenesSacadas=$item->getImages();
			//			echo "<BR><BR>Image Medium: ", $imagenesSacadas->getPrimary()->getMedium()->getUrl(), PHP_EOL;
			//			echo "<BR><BR>Image Large: ", $imagenesSacadas->getPrimary()->getLarge()->getUrl(), PHP_EOL;
			//			echo "<img src='".$imagenesSacadas->getPrimary()->getLarge()->getUrl()."'>";
						$amazonContents.="<BR><BR>Image Large: ". $imagenesSacadas->getPrimary()->getLarge()->getUrl();
						$amazonContents.="<img src='".$imagenesSacadas->getPrimary()->getLarge()->getUrl()."'>";
						$productImage=$imagenesSacadas->getPrimary()->getLarge()->getUrl();
						
					}				
					if ($item->getItemInfo() != null
						and $item->getItemInfo()->getTitle() != null
						and $item->getItemInfo()->getTitle()->getDisplayValue() != null) {
						$amazonContents.="<BR><BR>Title: ". $item->getItemInfo()->getTitle()->getDisplayValue();
					}										
					if ($item->getItemInfo() != null
						and $item->getItemInfo()->getFeatures() != null
						and $item->getItemInfo()->getFeatures()->getDisplayValues() != null) {
						$amazonContents.="<BR><BR>Features: ". $item->getItemInfo()->getFeatures()->getDisplayValues()[0];
						$amazonContents.="ALL: ".implode('<br>', $item->getItemInfo()->getFeatures()->getDisplayValues());
						$amazonFeatures=implode('<br>', $item->getItemInfo()->getFeatures()->getDisplayValues());
					}					
					if ($item->getOffers() != null
						and $item->getOffers() != null
						and $item->getOffers()->getListings() != null
						and $item->getOffers()->getListings()[0]->getPrice() != null
						and $item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount() != null) {
						$productPrice=$item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount();
					}
				}		 				 
				 //This should get you an array of all posts with 'Foo Bar' as the title
				 $pageExistente = amzafs_get_posts_by_title($item->getItemInfo()->getTitle()->getDisplayValue());
				 $postTitle=$item->getItemInfo()->getTitle()->getDisplayValue();				 
				 if (is_array($pageExistente)) { //Existen posts para borrar
					 //Borro post con el mismo título de este array			 
					 foreach ($pageExistente as $postExistente) {										 
						 if ($postExistente->ID>0){
							//ya existe el post, lo borramos antes de insertarlo debajo porque estamos actualizando
							//como ya hemos obtenido respuesta de Amazon, no hay peligro de que falle API si hacemos checks pertinentes
							wp_delete_post($postExistente->ID);
						 }						 						 
					 }					 
				 }
				//echo "<hr>pageExistente:<hr>";					 
				//print_r($pageExistente);
				//echo "<hr><hr>";
				if ((!empty($postTitle))&&(!empty($productLink))&&(!empty($productImage))&&(!empty($categoryId))){ //&&(!empty($amazonFeatures))
					/*Solo si la página no existe, creamos el nuevo post, válido para insert inicial y updates pq hemos borrado post previamente*/
					$post_id = wp_insert_post(array (
					   'post_type' => 'amz_product',
					   'post_title' => $postTitle,
					   'post_content' => "$amazonFeatures",
					   'post_status' => 'publish',
					   'comment_status' => 'closed',   // if you prefer
					   'ping_status' => 'closed',      // if you prefer
					));		
					if ($post_id) {
					   // insert post meta
					   //error_log("<br>$post_id<br>", 3, "postinsertado.log");
					   update_post_meta( $post_id, '_wpamz_meta_price', $productPrice );
					   update_post_meta( $post_id, '_wpamz_meta_affilink', $productLink );					   
					   update_post_meta( $post_id, '_wpamz_meta_imgurl', $productImage );					   
							$taxonomy = 'amazon_shortcode';
							$termObj  = get_term_by( 'id', $categoryId, $taxonomy);
							wp_set_object_terms($post_id, $termObj->slug, $taxonomy);						 
					}										
				}
			}			
        }
        if ($searchItemsResponse->getErrors() != null) {
         //   echo PHP_EOL, 'Printing Errors:', PHP_EOL, 'Printing first error object from list of errors', PHP_EOL;
         //   echo 'Error code: ', $searchItemsResponse->getErrors()[0]->getCode(), PHP_EOL;
        //    echo 'Error message: ', $searchItemsResponse->getErrors()[0]->getMessage(), PHP_EOL;
        }
    } catch (ApiException $exception) {
    //    echo "Error calling PA-API 5.0!", PHP_EOL;
    //    echo "HTTP Status Code: ", $exception->getCode(), PHP_EOL;
    //    echo "Error Message: ", $exception->getMessage(), PHP_EOL;
        if ($exception->getResponseObject() instanceof ProductAdvertisingAPIClientException) {
            $errors = $exception->getResponseObject()->getErrors();
            foreach ($errors as $error) {
               //echo "Error Type: ", $error->getCode(), PHP_EOL;
               //echo "<br>Error Message: ", $error->getMessage(), PHP_EOL;
            }
			//is_admin wont work in gutenberg REST API save
			//https://wordpress.stackexchange.com/questions/343583/is-admin-returning-false-in-backend-in-server-side-rendered-block
			if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
				//return 'Backend';
			} else {
				//return 'Frontend';
				if ( ! is_admin() ) {
					echo '<script language="javascript">';
					echo 'alert("AMAZON SETTINGS ERROR\n-------------------------------\nPlease, check your ACCESS KEY, SECRET KEY, TAG & AMAZON LOCALE in the plugin settings.\n-------------------------------\nError: '.$error->getCode().'\n'.$error->getMessage().'")';
					echo '</script>';
				}
			}
        } else {
     //       echo "Error response body: ", $exception->getResponseBody(), PHP_EOL;
        }
    } catch (Exception $exception) {
     //   echo "Error Message: ", $exception->getMessage(), PHP_EOL;
    }
	
	return $post_id;
}