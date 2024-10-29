<?php
/*
Plugin Name: Affiliate Shortcodes for Amazon
Plugin URI: https://netgrows.com/product/amazon-affiliate-shortcodes-pro/
Description: Amazon Affiliate Shortcodes, easy and fast Amazon store integration in your WordPress site. Insert the Amazon shortcodes anywhere.
Version: 1.0
Author: Juanma Rodríguez
Author URI: https://netgrows.com/
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/
/*********************************************************************************/
/*Include Amz*/
/*********************************************************************************/
require_once(__DIR__ . '/SearchItems.php');
/*********************************************************************************/
/*Register styles*/
/*********************************************************************************/
$plugin_url = plugin_dir_url( __FILE__ );
wp_enqueue_style( 'style',  $plugin_url . "/css/styles.css");
wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css', array(), '3', true);
$premiumURL="https://netgrows.com/product/amazon-affiliate-shortcodes-pro/";
/*********************************************************************************/
/*Plugin activation*/
/*********************************************************************************/
function amzafs_AMZPLUGIN_activation(){	
	//Debug
	delete_option( 'amz_short_options' );
	if ( get_option( 'amz_short_options' ) === false ) {
		$options = get_option( 'amz_short_options' );
		$options["access_key"]="";
		$options["secret_key"]="";
		$options["asoc_tag"]="";
		$options["update_time"]="604800";
		$options["select_field_0"]="1";
		$options["select_field_locale"]="us";		
		$options["amz_title_length"]="60";
		$options["amz_description_length"]="120";	
		$options["amz_button_label"]="View Product";
		$options["select_layout"]="1";		
		$options["select_price_yesno"]="2";
		$options["product_number"]="10";		
		$options["select_wholeblock_yesno"]="2";		
		update_option( "amz_short_options", $options );		
	}
}
register_activation_hook( __FILE__, 'amzafs_AMZPLUGIN_activation' );
/*********************************************************************************/
/*Include Settings*/
/*********************************************************************************/
include plugin_dir_path( __FILE__ ) . 'settings-page.php';	
function amzafs_get_posts_by_title($page_title, $post_type = false, $output = OBJECT ) {
    global $wpdb;
    //Handle specific post type?
    $post_type_where = $post_type ? 'AND post_type = %s' : '';
    //Query all columns so as not to use get_post()
    $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_title = %s $post_type_where AND post_status = 'publish'", $page_title, $post_type ? $post_type : '' ) );

    if ( $results ){
        $output = array();
        foreach ( $results as $post ){
            $output[] = $post;
        }
        return $output;
    }
    return null;
}
/*********************************************************************************/
/*amzafs_truncateAmzString*/
/*********************************************************************************/
function amzafs_truncateAmzString($string, $length, $stopanywhere=false) {
    //truncates a string to a certain char length, stopping on a word if not specified otherwise.
    if (strlen($string) > $length) {
        //limit hit!
        $string = mb_substr($string,0,($length -3));
        if ($stopanywhere) {
            //stop anywhere
            $string .= '...';
        } else{
            //stop on a word.
            $string = mb_substr($string,0,strrpos($string,' ')).'...';
        }
    }
    return $string;
}

/*********************************************************************************/
/*Add shortcode*/
/*********************************************************************************/
add_shortcode("amzcode","amzafs_amzshortcodemain");
function amzafs_amzshortcodemain( $attributes, $content = null ) {
	$amazonContents="";
	extract( shortcode_atts( array(
		'class' => '',
		'lang' => '',
		'autoplay' => ''
	), $attributes ) );	
	$idamzshortcode=$attributes[0];//Get id del shortcode
	$keyword = get_term_meta( $idamzshortcode, '__term_meta_text', true ); //Get keyword del shortcode (cat) from id
	
	$overrideLayout = get_term_meta( $idamzshortcode, '__term_meta_layout', true );
	$overrideCount = get_term_meta( $idamzshortcode, '__term_meta_productcount', true );
	
	if (($overrideCount=="")||(empty($overrideCount))) $overrideCount=99;
		
	$slugcategoria=get_term_by('id', $idamzshortcode, 'amazon_shortcode');
	$slugfinal=$slugcategoria->slug;
	
	$obj_term = get_term($idamzshortcode, 'amazon_shortcode');
	$cuentatotal=$obj_term->count;
	/*Search product by keyword in Amazon API*/
	
	
	//DEBUG FORZAR AMAZON
	//$cuentatotal=0;
	
	$cssRow="
			<style>
			.container_amz_sc_products.row{
				display: table-row;
			}
			.container_amz_sc_products.row img {
				float: left;
				margin: 0px 10px;
			}
			.container_amz_sc_products.row .amz_product-title, .container_amz_sc_products.row .amz_product-description{
				text-align: left;
				padding-left:15px;
				float: left;
				width: calc(100% - 320px);			
			}
			.container_amz_sc_products.row .button-effect{
				float: right;
			}
			.container_amz_sc_products.row .amz_product-description{			
				float: left;	
			}
			.container_amz_sc_products.row .button-effect{
				float: right;		
			}
			.container_amz_sc_products.row .effect {
				width: 100px;
				font-size: 12px;
			}	
			.container_amz_sc_products.row .amz_price{
				text-align: right;
			}
			.container_amz_sc_products.row .edit-link{
				float: right;
				margin-top: -30px;
			}
			.container_amz_sc_products.row img {
				width: 120px!important;
				height: auto;
				max-height: 120px;
			} 
			.container_amz_sc_products.row .button-effect{
				margin-top: 0px!important;
			}
			</style>
			";
	
	$amz_options=get_option( 'amz_short_options' );
	$select_layout=trim($amz_options["select_layout"]);
	//Layout grid (1) or row (2)
	$frontEndContent="";
	$layoutTypeClass="";
	if ($select_layout==1) {
	//Grid layout
		$layoutTypeClass="grid";
	} elseif ($select_layout==2) {
	//Table layout
		if ( ! is_admin() ) {
			$frontEndContent= $cssRow;
			$layoutTypeClass="row";			
		}
	}
	//Layout override in shortcode settings
	if ($overrideLayout==1) {
	//Grid layout
		$layoutTypeClass="grid";
	} elseif ($overrideLayout==2) {
	//Table layout
		if ( ! is_admin() ) {
			$frontEndContent= $cssRow;
			$layoutTypeClass="row";
		}
	}	
	
	$svgAmazon='<svg xmlns="http://www.w3.org/2000/svg" width="240" height="240" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm.919 7.433c-.224-.338-.656-.478-1.035-.478-.703 0-1.328.36-1.482 1.107-.032.167-.153.331-.321.339l-1.786-.194c-.151-.033-.319-.155-.275-.385.411-2.168 2.368-2.822 4.122-2.822.897 0 2.069.239 2.776.918.897.838.812 1.955.812 3.172v2.872c0 .864.359 1.243.695 1.708.119.167.144.368-.006.49-.377.316-1.046.897-1.413 1.224-.122.11-.304.113-.439.039-.614-.509-.722-.744-1.058-1.229-1.012 1.031-1.728 1.34-3.039 1.34-1.552 0-2.76-.957-2.76-2.873 0-1.497.811-2.513 1.966-3.012 1-.44 2.397-.52 3.466-.639v-.239c0-.439.035-.958-.223-1.338zm4.366 9.529c-1.43 1.33-3.503 2.038-5.289 2.038-2.502 0-4.755-1.166-6.46-3.107-.134-.152-.015-.36.146-.242 1.84 1.349 4.115 2.162 6.464 2.162 1.585 0 3.328-.413 4.931-1.272.242-.13.445.201.208.421zm.363.957c-.12.1-.234.047-.181-.086.176-.438.569-1.421.382-1.659-.186-.239-1.23-.113-1.7-.057-.142.017-.164-.107-.036-.197.833-.585 2.198-.416 2.357-.22.161.197-.041 1.566-.822 2.219zm-4.506-7.432v.399c0 .719.017 1.316-.345 1.955-.293.52-.759.839-1.275.839-.708 0-1.121-.539-1.121-1.337-.001-1.571 1.408-1.856 2.741-1.856z"/></svg>';


	if ($cuentatotal<=0){ 
		$amazonContents=amzafs_searchItemsAmz($keyword, $idamzshortcode);
		$frontEndContent.="<div class='warning_preload'> $svgAmazon <br>Please, <strong>reload the page</strong> to see the Amazon products. If you still not see them, check the plugin settings.</div>";	
	}
	else {
		//Show database stored products
			global $post;$backup=$post;
			$queryCustom = new WP_Query(array(
				'post_type' => 'amz_product',          // name of post type.
				'posts_per_page' => $overrideCount,          // name of post type.												
				'tax_query' => array(
					array(
						'taxonomy' => 'amazon_shortcode',   // taxonomy name
						'field' => $slugfinal,           // term_id, slug or name
						'terms' => $idamzshortcode,                  // term id, term slug or term name
					)
				)
			));
			if ( $queryCustom->have_posts() ) : 
			if ( ! is_admin() ) {
				$frontEndContent.="<div class='container_amz_sc_products $layoutTypeClass'>";								
					while ( $queryCustom->have_posts() ) : $queryCustom->the_post(); 				
						include plugin_dir_path( __FILE__ ) . 'product-template.php';			
						$lastProductTimestamp=get_post_time();								
					 endwhile; 			
				$frontEndContent.="</div>";
				$post=$backup;
				wp_reset_query();				
			} else $lastProductTimestamp=time()+999999999999;
			else: $frontEndContent="<div class='warning_preload'> $svgAmazon <br> Please, <strong>reload the page</strong> to see the Amazon products. If you still not see them, check the plugin settings.</div>";
			endif;
			
			//Si han caducado, vuelvo a sacar productos de amazon con searchItems
			//¿los borro después de haber comprobado que he sacado resultados nuevos?
			$currentTimestamp=time();
			$amz_options=get_option( 'amz_short_options' );
			$access_key=$amz_options["access_key"];
			$secret_key=$amz_options["secret_key"];
			$asoc_tag=$amz_options["asoc_tag"];
			$update_time=$amz_options["update_time"];
			$diferenciaTiempoSegundos=$currentTimestamp-$lastProductTimestamp;
			//echo "<hr> $diferenciaTiempoSegundos > = $update_time <br>";
			if (($diferenciaTiempoSegundos>=$update_time)&&($update_time!=0)) {
				//ACTUALIZANDO RESULTADOS
				$post_id=amzafs_searchItemsAmz($keyword, $idamzshortcode);	
				//echo "<HR>ACTUALIZANDO RESULTADOS <HR>";
			}			
	}	
	//return "<BR>OUTPUT ||| $cuentatotal productos ||| ID shortcode: $idamzshortcode ||| keyword: $keyword ||| ".$amazonContents;
	return $frontEndContent;	

}
/*********************************************************************************/
/*Create custom post types*/
/*********************************************************************************/
function amzafs_create_post_type() {
	register_post_type( 'amz_product',	
    array(
		'labels' => array(
			'name' => __( 'AMZ Shortcodes' ),
			/*'all_items' => 'Products',
			'add_new_item' => 'Add new',*/
			'add_new'                  => __( 'Add Product', 'text_domain' ),
			'all_items'                  => __( 'All Products', 'text_domain' ),
			'parent_item'                => __( 'Parent Item', 'text_domain' ),
			'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
			'new_item_name'              => __( 'New Item Name', 'text_domain' ),
			'add_new_item'               => __( 'Add Product', 'text_domain' ),
			'edit_item'                  => __( 'Edit Item', 'text_domain' ),
			'update_item'                => __( 'Update Item', 'text_domain' ),
			'view_item'                  => __( 'View Item', 'text_domain' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
			'popular_items'              => __( 'Popular Items', 'text_domain' ),
			'search_items'               => __( 'Search Items', 'text_domain' ),
			'not_found'                  => __( 'Not Found', 'text_domain' ),
			'no_terms'                   => __( 'No items', 'text_domain' ),
			'items_list'                 => __( 'Items list', 'text_domain' ),
			'singular_name' => __( 'Amazon Shortcode' )
		),
		'public' => true,
		'publicly_queryable'  => false,
		'menu_icon' => 'dashicons-cart',
		'taxonomies' => array( 'amazon_shortcode' ),
		'supports' => array( 'title', 'editor'),
		'has_archive' => true,
		));
	}		
add_action( 'init', 'amzafs_create_post_type' );
/*********************************************************************************/
/*Show metaboxes in backend*/
/*********************************************************************************/
function amzafs_wpamz_custom_box_imgurl($post)
{
    $amzImg = get_post_meta($post->ID, '_wpamz_meta_imgurl', true);
    ?>
	<input style="padding: 0 8px; line-height: 2; min-height: 30px;box-shadow: 0 0 0 transparent; border-radius: 4px; border: 1px solid #7e8993; background-color: #fff; color: #32373c;width:50%;" name="wpamz_field_imgurl" id="wpamz_field_imgurl" value="<?php echo $amzImg; ?>"></input>
    <?php
}
function amzafs_wpamz_custom_box_price($post)
{
    $amzkeyword = get_post_meta($post->ID, '_wpamz_meta_price', true);
    ?>
	<input style="padding: 0 8px; line-height: 2; min-height: 30px;box-shadow: 0 0 0 transparent; border-radius: 4px; border: 1px solid #7e8993; background-color: #fff; color: #32373c;width:50%;" name="wpamz_field_price" id="wpamz_field_price" value="<?php echo $amzkeyword; ?>"></input>
    <?php
}
function amzafs_wpamz_custom_box_affilink($post)
{
    $amzlink = get_post_meta($post->ID, '_wpamz_meta_affilink', true);
    ?>
	<input style="padding: 0 8px; line-height: 2; min-height: 30px;box-shadow: 0 0 0 transparent; border-radius: 4px; border: 1px solid #7e8993; background-color: #fff; color: #32373c;width:50%;" name="wpamz_field_affilink" id="wpamz_field_affilink" value="<?php echo $amzlink; ?>"></input>
    <?php
}
/*
function amzafs_wpamz_custom_box_html($post)
{
    $value = get_post_meta($post->ID, '_wpamz_meta_key', true);
    ?>
    <label for="wpamz_field">Description for this field</label>
    <select name="wpamz_field" id="wpamz_field" class="postbox">
        <option value="">Select something...</option>
        <option value="something" <?php selected($value, 'something'); ?>>Something</option>
        <option value="else" <?php selected($value, 'else'); ?>>Else</option>
    </select>
    <?php
}*/
/*********************************************************************************/
/*Save metaboxes*/
/*********************************************************************************/
function amzafs_wpamz_save_postdata($post_id)
{
	$post_id=intval($post_id);
	$imgURLAmz=esc_url_raw($_POST['wpamz_field_imgurl']);
	$priceAmz=sanitize_text_field($_POST['wpamz_field_price']);
	$afiURLAmz=esc_url_raw($_POST['wpamz_field_affilink']);
	$metaKeyAmz=sanitize_text_field($_POST['wpamz_field']);
	if (array_key_exists('wpamz_field_imgurl', $_POST)) { update_post_meta($post_id,'_wpamz_meta_imgurl',$imgURLAmz); }
	if (array_key_exists('wpamz_field_price', $_POST)) { update_post_meta($post_id,'_wpamz_meta_price',$priceAmz); }
	if (array_key_exists('wpamz_field_affilink', $_POST)) { update_post_meta($post_id,'_wpamz_meta_affilink',$afiURLAmz); }	
    if (array_key_exists('wpamz_field', $_POST)) { update_post_meta($post_id,'_wpamz_meta_key',$metaKeyAmz); }
						
}
add_action('save_post', 'amzafs_wpamz_save_postdata');
/*********************************************************************************/
/*Add metaboxes*/
/*********************************************************************************/
function amzafs_amzafs_wpamz_add_custom_box_price()
{
        add_meta_box(
            'wpamz_box_price',           // Unique ID
            'Product Price',  				// Keyword
            'amzafs_wpamz_custom_box_price',  // Content callback, must be of type callable
            'amz_product'          // Post type
        );
}
add_action('add_meta_boxes', 'amzafs_amzafs_wpamz_add_custom_box_price');
function amzafs_amzafs_wpamz_add_custom_box_affilink()
{
        add_meta_box(
            'wpamz_box_affilink',           // Unique ID
            'Product Link',  				// Keyword
            'amzafs_wpamz_custom_box_affilink',  // Content callback, must be of type callable
            'amz_product'          // Post type
        );
}
add_action('add_meta_boxes', 'amzafs_amzafs_wpamz_add_custom_box_affilink');
/*
function amzafs_wpamz_add_custom_box()
{
        add_meta_box(
            'wpamz_box_zid',           // Unique ID
            'Custom Meta Box Title',  // Box title
            'wpamz_custom_box_html',  // Content callback, must be of type callable
            'amz_product'          // Post type
        );

}

add_action('add_meta_boxes', 'amzafs_wpamz_add_custom_box');
*/
function amzafs_wpamz_add_custom_box_imgurl()
{
        add_meta_box(
            'wpamz_box_imgurl',           // Unique ID
            'Product image URL',  				// Keyword
            'amzafs_wpamz_custom_box_imgurl',  // Content callback, must be of type callable
            'amz_product'          // Post type
        );

}
add_action('add_meta_boxes', 'amzafs_wpamz_add_custom_box_imgurl');
/*********************************************************************************/
/*Add shortcode blue info boxex*/
/*********************************************************************************/
function amzafs_options_instructions_example() {
    global $my_admin_page;
    $screen = get_current_screen();	
	//error_log("<br>".$screen->id."<br>", 3, "milog.log");
    if ( is_admin() && ($screen->id == 'edit-amazon_shortcode') ) {
			$queried_object = get_queried_object();
			$term_id = $queried_object->term_id;
			$idCategory = $_REQUEST['tag_ID'];
			$slug=$queried_object->slug;
		if (isset($idCategory)){
            echo '<div class="postbox" style="background:#f1f1f1;color:#23282d;margin-top:40px;padding:10px;font-style: italic;">Copy & paste this <strong>shortcode</strong> where you want the products to appear:<div class="inside" style="font-style: normal;margin-top:10px;background-color:#007cba;padding-top: 10px;color:#fff;">';
            echo '[amzcode "'.$idCategory.'"]';
            echo '</div>Delete all the products linked to a shortcode and publish the shortcode to regenerate them. The shortcodes with 0 products will search and save them using Amazon Product Advertising API 5.0.</div>';	
		}		
	?>
        <script type="text/javascript">
            jQuery(document).ready(function($)
            {     
                $('.term-parent-wrap').remove();  
				$('.term-description-wrap').remove();
				$('.term-slug-wrap').remove();					
            });
        </script>
    <?php
    }
}
add_action( 'admin_notices', 'amzafs_options_instructions_example' );





function amzafs_options_instructions_example2() {
    global $my_admin_page;
    $screen = get_current_screen();
	//echo $screen->id;
    if ( is_admin() && ($screen->id == 'amz_product') ) {
        function add_content_after_editor() {
			global $post;
            $id = $post->ID;
			$imageURL = get_post_meta($id, '_wpamz_meta_imgurl', true);	
			if (!empty($imageURL)){
						echo '<div><div class="inside">';
						echo "<img style='width: 120px;' src='$imageURL'>";
						echo '</div></div>';		
			}
        }
        add_action( 'edit_form_after_title', 'add_content_after_editor' );
    }
}
add_action( 'admin_notices', 'amzafs_options_instructions_example2' );





/*********************************************************************************/
/*Product custom post type admin columns*/
/*********************************************************************************/

// Add the custom columns to the post type:
add_filter( 'manage_amz_product_posts_columns', 'amzafs_set_custom_edit_amz_product_columns' );
function amzafs_set_custom_edit_amz_product_columns($columns) {
    //unset( $columns['author'] );
    $columns['book_author'] = __( 'Image', 'your_text_domain' );
    //$columns['publisher'] = __( 'Publisher', 'your_text_domain' );
    return $columns;
}


// Add the data to the custom columns for the post type:
add_action( 'manage_amz_product_posts_custom_column' , 'amzafs_custom_amz_product_column', 1, 2 );
function amzafs_custom_amz_product_column( $column, $post_id ) {
    switch ( $column ) {
        case 'book_author' :
			$imageURL = get_post_meta($post_id, '_wpamz_meta_imgurl', true);
			print_r($terms);
            if ( is_string( $imageURL ) )
                echo "<img style='width: 80px;max-height: 120px;' src='$imageURL'>";
            else
                _e( 'Unable to get image(s)', 'your_text_domain' );
            break;

        case 'publisher' :
            echo get_post_meta( $post_id , 'publisher' , true ); 
            break;
    }
}







/*********************************************************************************/
/*Reorder colums post type admin*/
/*********************************************************************************/
/*
function add_ourteam_columns ( $columns ) {
	unset($columns['title']);
	unset($columns['tags']);
	unset($columns['date']);
	return array_merge ( $columns, array ( 
	   'name' => __ ('name'),
	   'category' => __ ( 'Shortcode' ),
	   'image'   => __ ( 'Image' ),
	   'date' => __('Date')
	));
}
add_filter ( 'manage_amazon_shortcode_posts_columns', 'add_ourteam_columns' );
*/





/*********************************************************************************/
// Register & fill the column Shortcode & Keyword in categories (Shortcodes)
/*********************************************************************************/

function amzafs_department_add_dynamic_hooks() {
	$taxonomy = 'amazon_shortcode';
	add_filter( 'manage_' . $taxonomy . '_custom_column', 'amzafs_department_taxonomy_rows',15, 3 );
	add_filter( 'manage_edit-' . $taxonomy . '_columns',  'amzafs_department_taxonomy_columns' );
}
add_action( 'admin_init', 'amzafs_department_add_dynamic_hooks' );

function amzafs_department_taxonomy_columns( $original_columns ) {
	$new_columns = $original_columns;
	array_splice( $new_columns, 1 );
	$new_columns['frontpage'] = esc_html__( 'Shortcode', 'taxonomy-images' );
	$new_columns['frontpage2'] = esc_html__( 'Keyword', 'taxonomy-images2' );
	return array_merge( $new_columns, $original_columns );
}

function amzafs_department_taxonomy_rows( $row, $column_name, $term_id ) {
$t_id = $term_id;
$meta = get_option( "taxonomy_$t_id" );
	if ( 'frontpage' === $column_name ) {	
		return '[amzcode "'.$term_id.'"]';
	}
	if ( 'frontpage2' === $column_name ) {			
		$value  = amzafs___get_term_meta_text( $term_id );
		return $value;
	}	
}







/********************************/
/*Shortcode (category) settings*/
/******************************/

// REGISTER TERM META
add_action( 'init', 'amzafs____register_term_meta_text' );
add_action( 'init', 'amzafs___register_term_meta_layout' );
add_action( 'init', 'amzafs___register_term_meta_productcount' );
function amzafs____register_term_meta_text() {
    register_meta( 'term', '__term_meta_text', 'amzafs___sanitize_term_meta_text' );
}
function amzafs___register_term_meta_layout() {
    register_meta( 'term', '__term_meta_layout', 'amzafs___sanitize_term_meta_layout' );	
}
function amzafs___register_term_meta_productcount() {
    register_meta( 'term', '__term_meta_productcount', 'amzafs___sanitize_term_meta_productcount' );	
}


//SANITIZE 
function amzafs___sanitize_term_meta_text ( $value ) {
    return sanitize_text_field ($value);
}
function amzafs___sanitize_term_meta_layout ( $value ) {
    return sanitize_text_field ($value);
}
function amzafs___sanitize_term_meta_productcount ( $value ) {
    return sanitize_text_field ($value);
}


// GETTER (will be sanitized)
function amzafs___get_term_meta_text( $term_id ) {
  $value = get_term_meta( $term_id, '__term_meta_text', true );
  $value = amzafs___sanitize_term_meta_text( $value );
  return $value;
}
function amzafs___get_term_meta_layout ($term_id ) {
  $value = get_term_meta( $term_id, '__term_meta_layout', true );
  $value = amzafs___sanitize_term_meta_layout( $value );
  return $value;
}
function amzafs___get_term_meta_productcount ($term_id ) {
  $value = get_term_meta( $term_id, '__term_meta_productcount', true );
  $value = amzafs___sanitize_term_meta_productcount( $value );
  return $value;
}




// ADD FIELD TO CATEGORY TERM PAGE
add_action( 'amazon_shortcode_add_form_fields', 'amzafs___add_form_field_term_meta_text' );
function amzafs___add_form_field_term_meta_text() { ?>
    <?php wp_nonce_field( basename( __FILE__ ), 'term_meta_text_nonce' ); ?>
    <div class="form-field form-required term-meta-text-wrap">
        <label for="term-meta-text"><?php _e( 'Amazon Keyword', 'text_domain' ); ?></label>
        <input type="text" name="term_meta_text" id="term-meta-text" class="term-meta-text-field" aria-required="true"/>
		<p>The keyword of the product that you want to search in Amazon.</p>
    </div>
<?php }





/*FREE VERSION*/
add_action( 'amazon_shortcode_add_form_fields', 'amzafs___add_form_field_term_meta_layout' );
function amzafs___add_form_field_term_meta_layout(  ) { ?>
	<?php wp_nonce_field( basename( __FILE__ ), 'term_meta_layout_nonce' ); 
	global $premiumURL; ?>
	<div class="form-field term-meta-layout-wrap">
	<label for="term-meta-layout"><?php _e( 'Products Layout', 'text_domain' ); ?></label>
    <select name='term_meta_layout' disabled>
		<option value='0' <?php selected( esc_attr( $value ), 1 ); ?>>Default</option>
    </select> <p>Override default (main settings) products layout. <a target='_blank' href='<?php echo $premiumURL; ?>'>Purchase premium version</a> to unlock it and make me happy!</p>
	</div>
<?php }




/*FREE VERSION*/
add_action( 'amazon_shortcode_add_form_fields', 'amzafs___add_form_field_term_meta_productcount' );
function amzafs___add_form_field_term_meta_productcount() { ?>
    <?php wp_nonce_field( basename( __FILE__ ), 'term_meta_productcount_nonce' ); 
	global $premiumURL;?>
    <div class="form-field term-meta-productcount-wrap">
        <label for="term-meta-productcount"><?php _e( 'Product Count', 'text_domain' ); ?></label>
        <input style="max-width: 80px;" type="number" min="0" max="99" name="term_meta_productcount" id="term-meta-productcount" value="" class="term-meta-productcount-field" disabled/>
		<p>The max amount of products that you want to show. 0 or empty will show ALL stored items. <a target='_blank' href='<?php echo $premiumURL; ?>'>Purchase premium version</a> to unlock it and make me happy!</p>
    </div>
<?php }





// ADD FIELD TO CATEGORY EDIT PAGE
add_action( 'amazon_shortcode_edit_form_fields', 'amzafs___edit_form_field_term_meta_text' );
function amzafs___edit_form_field_term_meta_text( $term ) {
    $value  = amzafs___get_term_meta_text( $term->term_id );
    if ( ! $value )
        $value = ""; ?>
    <tr class="form-field form-required term-meta-text-wrap">
        <th scope="row"><label for="term-meta-text"><?php _e( 'Amazon Keyword', 'text_domain' ); ?></label></th>
        <td>		
            <?php wp_nonce_field( basename( __FILE__ ), 'term_meta_text_nonce' ); ?>
            <input type="text" name="term_meta_text" id="term-meta-text" value="<?php echo esc_attr( $value ); ?>" class="term-meta-text-field" aria-required="true"/>
			<p>The keyword of the product that you want to search in Amazon.</p>
        </td>
    </tr>
<?php }





/*FREE VERSION*/
add_action( 'amazon_shortcode_edit_form_fields', 'amzafs___edit_form_field_term_meta_layout' );
function amzafs___edit_form_field_term_meta_layout( $term ) {
    $value  = amzafs___get_term_meta_layout( $term->term_id );
    if ( ! $value )
        $value = ""; ?>
    <tr class="form-field term-meta-layout-wrap">
        <th scope="row"><label for="term-meta-layout"><?php _e( 'Products Layout', 'text_domain' ); ?></label></th>
        <td>
            <?php wp_nonce_field( basename( __FILE__ ), 'term_meta_layout_nonce' ); 
			global $premiumURL; ?>		
			<div class="form-field term-meta-layout-wrap">
			<select name='term_meta_layout' disabled>
				<option value='0' <?php selected( esc_attr( $value ), 1 ); ?>>Default</option>
			</select> <p>Override default (main settings) products layout. <a target='_blank' href='<?php echo $premiumURL; ?>'>Purchase premium version</a> to unlock it and make me happy!</p>
			</div>         
        </td>
    </tr>
<?php }




/*FREE VERSION*/
add_action( 'amazon_shortcode_edit_form_fields', 'amzafs___edit_form_field_term_meta_productcount' );
function amzafs___edit_form_field_term_meta_productcount( $term ) {
    $value  = amzafs___get_term_meta_productcount( $term->term_id );
    if ( ! $value )
        $value = ""; ?>
    <tr class="form-field term-meta-productcount-wrap">
        <th scope="row"><label for="term-meta-productcount"><?php _e( 'Product Count', 'text_domain' ); ?></label></th>
        <td>		
            <?php wp_nonce_field( basename( __FILE__ ), 'term_meta_productcount_nonce' ); 
			global $premiumURL; ?>
            <input style="max-width: 80px;" type="number" min="0" max="99" name="term_meta_productcount" id="term-meta-productcount" value="<?php echo esc_attr( $value ); ?>" class="term-meta-productcount-field"  disabled/>
			<p>The max amount of products that you want to show. 0 or empty will show ALL stored items. <a target='_blank' href='<?php echo $premiumURL; ?>'>Purchase premium version</a> to unlock it and make me happy!</p>
        </td>
    </tr>
<?php }


// SAVE TERM META (on term edit & create)
add_action( 'edit_amazon_shortcode',   'amzafs___save_term_meta_text' );
add_action( 'create_amazon_shortcode', 'amzafs___save_term_meta_text' );

add_action( 'edit_amazon_shortcode',   'amzafs___save_term_meta_layout' );
add_action( 'create_amazon_shortcode', 'amzafs___save_term_meta_layout' );

add_action( 'edit_amazon_shortcode',   'amzafs___save_term_meta_productcount' );
add_action( 'create_amazon_shortcode', 'amzafs___save_term_meta_productcount' );

function amzafs___save_term_meta_text( $term_id ) {
    // verify the nonce
    if ( ! isset( $_POST['term_meta_text_nonce'] ) || ! wp_verify_nonce( $_POST['term_meta_text_nonce'], basename( __FILE__ ) ) )
        return;
    $old_value  = amzafs___get_term_meta_text( $term_id );
    $new_value = isset( $_POST['term_meta_text'] ) ? amzafs___sanitize_term_meta_text ( $_POST['term_meta_text'] ) : '';
    if ( $old_value && '' === $new_value )
        delete_term_meta( $term_id, '__term_meta_text' );

    else if ( $old_value !== $new_value )
        update_term_meta( $term_id, '__term_meta_text', $new_value );	
}


function amzafs___save_term_meta_layout( $term_id ) {
    // verify the nonce
    if ( ! isset( $_POST['term_meta_layout_nonce'] ) || ! wp_verify_nonce( $_POST['term_meta_layout_nonce'], basename( __FILE__ ) ) )
        return;
    $old_value2  = amzafs___get_term_meta_layout( $term_id );
    $new_value2 = isset( $_POST['term_meta_layout'] ) ? amzafs___sanitize_term_meta_layout ( $_POST['term_meta_layout'] ) : '';		
    if ( $old_value2 && '' === $new_value2 )
        delete_term_meta( $term_id, '__term_meta_layout' );

    else if ( $old_value2 !== $new_value2 )
        update_term_meta( $term_id, '__term_meta_layout', $new_value2 );		
}


function amzafs___save_term_meta_productcount( $term_id ) {
    // verify the nonce
    if ( ! isset( $_POST['term_meta_productcount_nonce'] ) || ! wp_verify_nonce( $_POST['term_meta_productcount_nonce'], basename( __FILE__ ) ) )
        return;
    $old_value2  = amzafs___get_term_meta_productcount( $term_id );
    $new_value2 = isset( $_POST['term_meta_productcount'] ) ? amzafs___sanitize_term_meta_productcount ( $_POST['term_meta_productcount'] ) : '';		
    if ( $old_value2 && '' === $new_value2 )
        delete_term_meta( $term_id, '__term_meta_productcount' );

    else if ( $old_value2 !== $new_value2 )
        update_term_meta( $term_id, '__term_meta_productcount', $new_value2 );		
}





// Add the custom columns to the book post type:
add_filter( 'manage_edit-amazon_shortcode_columns', 'amzafs_set_custom_edit_amz_product_columns2' );
function amzafs_set_custom_edit_amz_product_columns2($columns) {
    unset( $columns['description'] );
	unset( $columns['slug'] );
    return $columns;
}



// Add the data to the custom columns for the post type:
add_action( 'manage_amazon_shortcode_posts_custom_column' , 'amzafs_custom_amz_shortcode_column', 1, 2 );
function amzafs_custom_amz_shortcode_column( $column, $post_id ) {
	echo $column;
    switch ( $column ) {
        case 'keyword_del_shortcode' :
			$imageURL = get_post_meta($post_id, '_wpamz_meta_imgurl', true);
			print_r($terms);
			//echo "aaaa";
            if ( is_string( $imageURL ) )
                echo "<img style='width: 80px;' src='$imageURL'>";
            else
                _e( 'Unable to get image(s)', 'your_text_domain' );
            break;

        case 'publisher' :
            echo get_post_meta( $post_id , 'publisher' , true ); 
            break;
    }
}


 
 
/*********************************************************************************/
// Register Custom Taxonomy
/*********************************************************************************/ 
function amzafs_custom_taxonomy() {
	
	$labels = array(
		'name'                       => _x( 'Shortcodes', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'AMZ shortcode', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Shortcodes', 'text_domain' ),
		'all_items'                  => __( 'All Items', 'text_domain' ),
		'parent_item'                => __( 'Parent Item', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New Item Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New Item', 'text_domain' ),
		'edit_item'                  => __( 'Edit Item', 'text_domain' ),
		'update_item'                => __( 'Update Item', 'text_domain' ),
		'view_item'                  => __( 'View Item', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular Items', 'text_domain' ),
		'search_items'               => __( 'Search Items', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
		'no_terms'                   => __( 'No items', 'text_domain' ),
		'items_list'                 => __( 'Items list', 'text_domain' ),
		'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'parent_item'  				 => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'amazon_shortcode', array( 'amz_shortcode' ), $args );

}
add_action( 'init', 'amzafs_custom_taxonomy', 0 );

/*Plugin settings link*/
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'amzafs_my_plugin_action_links' );
function amzafs_my_plugin_action_links( $links ) {
   global $premiumURL;
   $links[] = '<a href="'. esc_url( get_admin_url(null, 'edit.php?post_type=amz_product&page=amz_shortcodes_settings') ) .'">Settings</a>';
   $links[] = '<a href="'.$premiumURL.'" target="_blank">Go Premium</a>';
   return $links;
}
