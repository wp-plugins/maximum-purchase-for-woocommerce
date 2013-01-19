<?php
/*
VarkTech Maximum Purchase for WooCommerce
Woo-specific functions
Parent Plugin Integration
*/


class VTMAX_Parent_Cart_Validation {
	
	public function __construct(){
     global $vtmax_info, $woocommerce; //$woocommerce_checkout = $woocommerce->checkout();
     /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++   
     *        Apply Maximum Amount Rules to ecommerce activity
     *                                                          
     *          WOO-Specific Checkout Logic and triggers 
     *                                               
     *  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++   */
                                
    //  add actions for early entry into Woo's 3 shopping cart-related pages, and the "place order" button -    
    
    $vtmax_info['woo_cart_url']      =  $this->vtmax_woo_get_url('cart'); 
    $vtmax_info['woo_checkout_url']  =  $this->vtmax_woo_get_url('checkout');
    $vtmax_info['woo_pay_url']       =  $this->vtmax_woo_get_url('pay');   
    $vtmax_info['currPageURL']       =  $this->vtmax_currPageURL();
      
    if ( in_array($vtmax_info['currPageURL'], array($vtmax_info['woo_cart_url'],$vtmax_info['woo_checkout_url'], $vtmax_info['woo_pay_url'] ) ) )  {      
       add_action( 'init', array(&$this, 'vtmax_woo_apply_checkout_cntl'),99 );                                                            
    }  
     /*   Priority of 99 in the action above, to delay add_action execution. The
          priority delays us in the exec sequence until after any quantity change has
          occurred, so we pick up the correct altered state. */

    //if "place order" button hit, this action catches and errors as appropriate
    add_action( 'woocommerce_before_checkout_process', array(&$this, 'vtmax_woo_place_order_cntl') );   
    
    //save info to Lifetime tables following purchase       
    add_action('woocommerce_checkout_order_processed', array( &$this, 'vtmax_post_purchase_save_info' ));
    /*  =============+ */      
                                                                                
	}

 
  // from woocommerce/classes/class-wc-cart.php 
  public function vtmax_woo_get_url ($pageName) {            
     global $woocommerce;
      $checkout_page_id = $this->vtmax_woo_get_page_id($pageName);
  		if ( $checkout_page_id ) {
  			if ( is_ssl() )
  				return str_replace( 'http:', 'https:', get_permalink($checkout_page_id) );
  			else
  				return apply_filters( 'woocommerce_get_checkout_url', get_permalink($checkout_page_id) );
  		}
  }
      
  // from woocommerce/woocommerce-core-functions.php 
  public function vtmax_woo_get_page_id ($pageName) { 
    $page = apply_filters('woocommerce_get_' . $pageName . '_page_id', get_option('woocommerce_' . $pageName . '_page_id'));
		return ( $page ) ? $page : -1;
  }    
 /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++    */
    
    
           
  /* ************************************************
  **   Application - Apply Rules at E-Commerce Checkout
  *************************************************** */
	public function vtmax_woo_apply_checkout_cntl(){
    global $vtmax_cart, $vtmax_cart_item, $vtmax_rules_set, $vtmax_rule, $vtmax_info, $woocommerce;
        
    //input and output to the apply_rules routine in the global variables.
    //    results are put into $vtmax_cart
    
    if ( $vtmax_cart->error_messages_processed == 'yes' ) {  
      $woocommerce->add_error(  __('Maximum Purchase error found.', 'vtmax') );  //supplies an error msg and prevents payment from completing 
      return;
    }
    
     $vtmax_apply_rules = new VTMAX_Apply_Rules;   
    
    //ERROR Message Path
    if ( sizeof($vtmax_cart->error_messages) > 0 ) {      
      //insert error messages into checkout page
      add_action( "wp_enqueue_scripts", array($this, 'vtmax_enqueue_error_msg_css') );
      add_action('wp_head', array(&$this, 'vtmax_display_rule_error_msg_at_checkout') );  //JS to insert error msgs   
      $vtmax_cart->error_messages_processed = 'yes';
      $woocommerce->add_error(  __('Maximum Purchase error found.', 'vtmax') );  //supplies an error msg and prevents payment from completing  
    }     
  }
      
           
  /* ************************************************
  **   Application - Apply Rules at Woo E-Commerce  ==> AT Place Order Time <==
  *************************************************** */
	public function vtmax_woo_place_order_cntl(){
    global $vtmax_cart, $vtmax_cart_item, $vtmax_rules_set, $vtmax_rule, $vtmax_info, $woocommerce;
        
    //input and output to the apply_rules routine in the global variables.
    //    results are put into $vtmax_cart
    
    if ( $vtmax_cart->error_messages_processed == 'yes' ) {  
      $woocommerce->add_error(  __('Maximum Purchase error found.', 'vtmax') );  //supplies an error msg and prevents payment from completing 
      return;
    }
    
     $vtmax_apply_rules = new VTMAX_Apply_Rules;   
    
  /*  *********************************************************************************************************
      These two add_actiions cannot be used to display error msgs in this situation, as they are not executed when
      errors are found at "place order" time in woo land.  They depend on a screen refresh, and woo doesn't do one...
         // add_action( "wp_enqueue_scripts", array($this, 'vtmax_enqueue_error_msg_css') );
         // add_action('wp_head', array(&$this, 'vtmax_display_rule_error_msg_at_checkout') );  //JS to insert error msgs 
      *********************************************************************************************************  
    */
    
    //ERROR Message Path
    if ( sizeof($vtmax_cart->error_messages) > 0 ) {  
        
      //insert error messages into checkout page
      //this echo may result in multiple versions of the css file being called for, can't be helped.
      echo '<link rel="stylesheet" type="text/css" media="all" href="'.VTMAX_URL.'/core/css/vtmax-error-style.css" />' ;     //mwnt
            
      /* WOO crazy error display, in this situation only:
          {"result":"failure","messages":"
            \n\t\t\t
            Maximum Purchase error found.<\/li>\n\t<\/ul>","refresh":"false"}     
      */
      //  These are the incorrectly displayed contens of the 'add_error' function below, and are only a problem in this particular situation
      echo '<div class="woo-apply-checkout-cntl">';  // This 'echo' allows the incorrectly displayed error msg to fall within the 'woo-apply-checkout-cntl' div, and be deleted by following JS
      $woo_apply_checkout_cntl = 'yes';
      
      //display VTMAX error msgs   
      $this->vtmax_display_rule_error_msg_at_checkout();      
      
      $vtmax_cart->error_messages_processed = 'yes';
      
      //tell WOO that an error has occurred, and not to proceed further
      $woocommerce->add_error(  __('Maximum Purchase error found.', 'vtmax') );  //supplies an error msg and prevents payment from completing        //mwnt
    } 
    // else { $woocommerce->add_error(  __('FAKE Purchase error found.', 'vtmam') ); } //Test lifetime history max rule logic    
  }  

  
  /* ************************************************
  **   Application - On Error Display Message on E-Commerce Checkout Screen  
  *************************************************** */ 
  public function vtmax_display_rule_error_msg_at_checkout($woo_apply_checkout_cntl = null){
    global $vtmax_info, $vtmax_cart, $vtmax_setup_options;
    //error messages are inserted just above the checkout products, and above the checkout form
      //In this situation, this 'id or class Selector' may not be blank, supply woo checkout default - must include '.' or '#'
    if ( $vtmax_setup_options['show_error_before_checkout_products_selector']  <= ' ' ) {
       $vtmax_setup_options['show_error_before_checkout_products_selector'] = VTMAX_CHECKOUT_PRODUCTS_SELECTOR_BY_PARENT;             
    }
      //In this situation, this 'id or class Selector' may not be blank, supply woo checkout default - must include '.' or '#'
    if ( $vtmax_setup_options['show_error_before_checkout_address_selector']  <= ' ' ) {
       $vtmax_setup_options['show_error_before_checkout_address_selector'] = VTMAX_CHECKOUT_ADDRESS_SELECTOR_BY_PARENT;             
    }
    
      /*   **  WOO changes **
        remove previous onscreen error msgs: 
                <php if ($woo_apply_checkout_cntl == 'yes')  { >
                $('</div>').insertBefore('<php echo $vtmax_setup_options['show_error_before_checkout_address_selector'] >');  //ends the 'woo-apply-checkout-cntl' div, allows for incorrect error msg display to fall within it and be deleted by the next statement
                <php } >
                $('.woo-apply-checkout-cntl').remove();  //removes the stray error msg displays at checkout place order time
                $('.vtmax-error').remove();  //removes old error msgs at checkout place order time
           previous error msgs normally removed at screen refresh, but at "place order" time,
           messages are returned via Ajax, and error messages will stack up.
      */    
     ?>     
        <script type="text/javascript">
        
        jQuery(document).ready(function($) {
          <?php if ($woo_apply_checkout_cntl == 'yes')  { ?>
          $('</div>').insertBefore('<?php echo $vtmax_setup_options['show_error_before_checkout_address_selector'] ?>');  //ends the 'woo-apply-checkout-cntl' div, allows for incorrect error msg display to fall within it and be deleted by the next statement
          <?php } ?>
          $('.woo-apply-checkout-cntl').remove();  //removes the stray error msg displays at checkout place order time (if included in the 'if', doesn't work for some reason)
          $('.vtmax-error').remove();  //removes old error msgs at checkout place order time
    <?php 
    //loop through all of the error messages 
    //          $vtmax_info['line_cnt'] is used when table formattted msgs come through.  Otherwise produces an inactive css id. 
    for($i=0; $i < sizeof($vtmax_cart->error_messages); $i++) { 
     ?>
        <?php 
          //default selector for products area (".shop_table") is used on BOTH cart page and checkout page. Only use on cart page
          if ( ( $vtmax_setup_options['show_error_before_checkout_products'] == 'yes' ) &&  ($vtmax_info['currPageURL'] == $vtmax_info['woo_cart_url']) ){ 
        ?>
           $('<div class="vtmax-error" id="line-cnt<?php echo $vtmax_info['line_cnt'] ?>"><h3 class="error-title">Maximum Purchase Error</h3><p> <?php echo $vtmax_cart->error_messages[$i]['msg_text'] ?> </p></div>').insertBefore('<?php echo $vtmax_setup_options['show_error_before_checkout_products_selector'] ?>');
        <?php 
          } 
          //Only message which shows up on actual checkout page.
          if ( $vtmax_setup_options['show_error_before_checkout_address'] == 'yes' ){ 
        ?>
           $('<div class="vtmax-error" id="line-cnt<?php echo $vtmax_info['line_cnt'] ?>"><h3 class="error-title">Maximum Purchase Error</h3><p> <?php echo $vtmax_cart->error_messages[$i]['msg_text'] ?> </p></div>').insertBefore('<?php echo $vtmax_setup_options['show_error_before_checkout_address_selector'] ?>');
    <?php 
          }
    }  //end 'for' loop      
     ?>   
            });   
          </script>
     <?php    


     /* ***********************************
        CUSTOM ERROR MSG CSS AT CHECKOUT
        *********************************** */
     if ($vtmax_setup_options[custom_error_msg_css_at_checkout] > ' ' )  {
        echo '<style type="text/css">';
        echo $vtmax_setup_options[custom_error_msg_css_at_checkout];
        echo '</style>';
     }
     
     /*
      Turn off the messages processed switch.  As this function is only executed out
      of wp_head, the switch is only cleared when the next screenful is sent.
     */
     $vtmax_cart->error_messages_processed = 'no';   
 } 
 
   
  /* ************************************************
  **   Application - get current page url
  *************************************************** */ 
 public  function vtmax_currPageURL() {
     $pageURL = 'http';
     if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
        $pageURL .= "://";
     if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
     } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
     }
     return $pageURL;
  } 
 
    

  /* ************************************************
  **   Application - On Error enqueue error style
  *************************************************** */
  public function vtmax_enqueue_error_msg_css() {
    wp_register_style( 'vtmax-error-style', VTMAX_URL.'/core/css/vtmax-error-style.css' );  
    wp_enqueue_style('vtmax-error-style');
  } 
 
 
  
  /* ************************************************
  **   After purchase, store max purchase info for lifetime rules on db
  *************************************************** */ 
  function vtmax_post_purchase_save_info () {
    
    if(defined('VTMAX_PRO_DIRNAME')) {
      require ( VTMAX_PRO_DIRNAME . '/woo-integration/vtmax-save-purchase-info.php');
    }
    
  } // end  function vtmax_store_max_purchaser_info() 
 
} //end class
$vtmax_parent_cart_validation = new VTMAX_Parent_Cart_Validation;