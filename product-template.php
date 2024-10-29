<?php
$amz_options=get_option( 'amz_short_options' );
$amz_title_length=trim($amz_options["amz_title_length"]); if ($amz_title_length=="") $amz_title_length=60;
$amz_description_length=trim($amz_options["amz_description_length"]); if ($amz_description_length=="") $amz_description_length=25;	
$amzButtonLabel=trim($amz_options["amz_button_label"]);
$amz_description_length = (int)$amz_description_length;
$productContent=get_the_content();
$productContent=str_replace("<br>", " ", $productContent );
if ($amz_description_length!=0) $varExcerpt=amzafs_truncateAmzString(strip_tags($productContent), $amz_description_length);
else $varExcerpt="";

if (empty($amzButtonLabel)) $amzButtonLabel="View Product";
$amazonLink = get_post_meta(get_the_ID(), '_wpamz_meta_affilink', true);
$amazonPrice = get_post_meta(get_the_ID(), '_wpamz_meta_price', true);
$select_price_yesno=$amz_options["select_price_yesno"];
if ($amz_title_length!=0) $varTitle=amzafs_truncateAmzString(get_the_title(),$amz_title_length);
if ($select_price_yesno==2) $mostrarYesNo='style="display:none"';
$imagenAmazon=get_post_meta(get_the_ID(),"_wpamz_meta_imgurl")[0];
$select_wholeblock_yesno=$amz_options["select_wholeblock_yesno"];
if ($select_wholeblock_yesno==1) $wholeLinkBlock="<a href='$amazonLink'><span class='link-spanner'></span></a>";
$frontEndContent.='
<div class="item" id="post-'.get_the_ID().'">
'.$wholeLinkBlock.'
	<img id="'. $postid.'" src="'. $imagenAmazon.'" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="" srcset="'. $imagenAmazon.' 600w, '. $imagenAmazon.' 300w" sizes="(max-width: 600px) 100vw, 600px">
    <div class="amz_product-title">	    
		'. $varTitle .'
	</div>
    <div class="amz_product-description">	    
		'. $varExcerpt.'
	</div>
	<div class="amz_price" '.$mostrarYesNo.'>'. $amazonPrice.'</div>
	<div class="button-effect"><a class="effect effect-2" href="'. $amazonLink.'" rel="nofollow" target="_blank" title="'. $amzButtonLabel.'"><i class="fa fa-shopping-cart" aria-hidden="true"></i> '. $amzButtonLabel.'</a></div>
';
if( current_user_can('edit_pages')  ) { 
	$frontEndContent.='<span class="edit-link" style="font-size:12px;display: block; clear: both;"><a target="_blank" href="'.get_edit_post_link().'">Edit</a></span>';
}
$frontEndContent.='</div>';
?>
