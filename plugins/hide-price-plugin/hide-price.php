<?php

/*
  Plugin Name: Hide Price
  Description: It hides prices from all product page, product with sale price, and hide on product ID. 
  Version 1.0
  Author: Nishi  
*/

// Exit if accessed directly

//error_reporting(E_ERROR | E_PARSE);

 

register_activation_hook(__FILE__, 'some_woocommerce_addon_activate');
add_action('admin_menu',  'register_my_custom_submenu_page');
add_action('admin_init', 'settings');  
    
add_filter( 'woocommerce_get_price_html', 'woo_hide_price_fun' , 10, 2);

function some_woocommerce_addon_activate() {
  
  if( !class_exists( 'WooCommerce' ) ) {
      deactivate_plugins( plugin_basename( __FILE__ ) );
      wp_die( __( 'Please install and Activate WooCommerce.', 'woocommerce-addon-slug' ), 'Plugin dependency check', array( 'back_link' => true ) );
  }
}

function woo_hide_price_fun($price, $product){
  if((is_product()) AND get_option('enablePluginSiteWide') == 1){   
    // Hide Price on All products Radio Button 1
        if(get_option('hide--price-on') == 0){
            if ( is_admin() ) return get_option('replacementText');;
	        return '';
    }
    //Hide Price on product with Sale Radio Button 2
    elseif(get_option('hide--price-on') == 1){
      if( $product->is_on_sale()){
        return get_option('replacementText');
      } 
      return $price;
    }
    //Hide Price on product with Id Radio Button 3
    elseif(get_option('hide--price-on') == 2){
      $product_id = explode(',', get_option('id_text_area'));
	    if ( in_array( $product->get_id(),$product_id ) ) {
		  return get_option('replacementText');
	        }
      return $price;
    }  
    elseif(get_option('hide--price-on') == 3)
    {         
     
      $hideCategories = get_option('hide-on-category');    
      $productcategories = get_the_terms($product->get_id(), 'product_cat' , $hideCategories);
      //loop through current product categories
      foreach($productcategories as $pcat){

        //loop through settings product categories
        foreach($hideCategories as $hcat){

            //if current product cat == listed in settings
            if($pcat->name == $hcat){ return get_option('replacementText'); }
        }
        
      }  
 
    }else{
      return $price;
    } 
  }
return $price;
}         
                          
  function register_my_custom_submenu_page() {
      add_submenu_page( 'woocommerce', 'Hide Price', 'Hide Price', 'manage_options', 'my-custom-submenu-page','hide_product_details' );   
}
    function settings(){
      add_settings_section('hp_firstsection', null , null, 'my-custom-submenu-page');

      add_settings_field('enablePluginSiteWide', 'Enabled Plugin Site Wide' ,'enablepluginHTML','my-custom-submenu-page', 'hp_firstsection');
      register_setting('hidepriceplugin', 'enablePluginSiteWide', array('sanatize_callback' => 'sanitize_text_field', 'default' => '0'));
      
      add_settings_field('hide--price-on', 'Hide Price on ' ,'hidePriceOn','my-custom-submenu-page', 'hp_firstsection');
      register_setting('hidepriceplugin', 'hide--price-on', array('sanatize_callback' => 'sanitize_text_field', 'default' => '1'));

      add_settings_field('id_text_area', 'Enter comma seprated Id to hide-price on those page ' ,'hidebyMentionId','my-custom-submenu-page', 'hp_firstsection');
      register_setting('hidepriceplugin', 'id_text_area', array('sanatize_callback' => 'sanitize_text_field', 'default' => '1'));

      add_settings_field('hide-on-category', 'Select Hide on Category' ,'selectCategory','my-custom-submenu-page', 'hp_firstsection');
      register_setting('hidepriceplugin', 'hide-on-category', array('sanatize_callback' => 'sanitize_text_field', 'default' => '0'));

      add_settings_field('replacementText', 'Enter text which you want to be replace with hide prices' ,'replacementFieldHTML','my-custom-submenu-page', 'hp_firstsection');
      register_setting('hidepriceplugin', 'replacementText', array('sanatize_callback' => 'sanitize_text_field', 'default' => '0'));     
    }

    
    function enablepluginHTML(){?>
      <input type="checkbox" name="enablePluginSiteWide" value="1" <?php if (get_option('enablePluginSiteWide') == '1'){ echo 'checked'; }  ?> >  
      <label for="enable-plugin-site-wide"> </label>
      <?php
          }      
         
    function hidePriceOn(){ ?>
      <input type="radio" id="hide-on-all-product" name="hide--price-on" value="0" onClick="EnableDisableTB();" <?php if (get_option('hide--price-on') == '0'){ echo 'checked'; }  ?> >
      <label for="hide-On-All-Product">Hide On All Products</label><br>
      <input type="radio" id="product-with-sale" name="hide--price-on" value="1" onClick="EnableDisableTB();"<?php if (get_option('hide--price-on') == '1'){ echo 'checked'; }  ?> >
      <label for="Product-With-Sale">Hide On Product with Sale</label><br>
      <input type="radio" id="product-with-id" name="hide--price-on" value="2" onClick="EnableDisableTB();" <?php if (get_option('hide--price-on') == '2'){ echo 'checked'; }  ?>>
      <label for="Product-With-Id">Hide On Products IDs below</label><br>
      <input type="radio" id="product-with-id" name="hide--price-on" value="3" onClick="EnableDisableTB();" <?php if (get_option('hide--price-on') == '3'){ echo 'checked'; }  ?>>
      <label for="Product-With-Id">Hide On Categories below</label><br>
 <?php   }
  
 function hidebyMentionId(){ ?>
      <input type="text" name="id_text_area" id="id_text_area" value="<?php echo get_option('id_text_area'); ?>"> 
    <?php }
    
    function selectCategory(){ ?>      
        
         
      <select id="hide-on-category" name="hide-on-category[]" style="width:330px; height: 120px;" multiple> 
              <option id="" value="">No Category</option>
      <?php
              $catdata = array();    
              $args = array( 'type' => 'product', 'taxonomy' => 'product_cat' ); 
              $categories = get_categories( $args );
              $selected = get_option( 'hide-on-category' ) ?: array('');	
              $hpc = 0;
          foreach ($categories as $cat) {  ?>        
      
          <option id="<?php echo $cat->term_id ; ?>" <?php if(($selected) > 0){ foreach ($selected as $selectedcat) {				
                 if ($selectedcat ==  $cat->name) { 
            echo 'selected'; }} ?>>
                <?php echo $cat->name; ?></option>
                <?php $hpc++; } }  ?>
    </select>
                <?php echo count(get_option('hide-on-category')); ?> 
        
    <br>
   <?php }

function replacementFieldHTML(){ ?>
  <textarea name="replacementText" id="replacementText" ><?php echo esc_attr(get_option('replacementText', '$price'))?></textarea>
<?php }

function handleForm(){
  if (wp_verify_nonce($_POST['ourNonce'], 'hidePricePlugin') AND current_user_can('manage_options')) {
    update_option('replacementText', sanitize_text_field($_POST['replacementText'])); ?>
    <div class="updated">
      
    </div>
  <?php } else { ?>
    <div class="error">
      
    </div>
  <?php } 
}
    
function hide_product_details(){?>
  <div class="wrap">
    <h1> Hide Price </h1><br>
    <?php if ($_POST['justsubmitted'] == "true") $this->handleForm() ?>
    <form action="options.php" method="POST">
      <?php 
        settings_errors();
        settings_fields('hidepriceplugin');
        do_settings_sections('my-custom-submenu-page');
        submit_button();
      ?>
      </form>
    </div>

<?php } 


  


