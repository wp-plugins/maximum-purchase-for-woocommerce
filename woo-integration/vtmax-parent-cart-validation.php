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
      
    if ( in_array($vtmax_info['currPageURL'], array( $vtmax_info['woo_cart_url'],$vtmax_info['woo_checkout_url'], $vtmax_info['woo_pay_url'] ) ) )  {      
       add_action( 'init', array(&$this, 'vtmax_woo_apply_checkout_cntl'),99 );                                                            
    }  
     /*   Priority of 99 in the action above, to delay add_action execution. The
          priority delays us in the exec sequence until after any quantity change has
          occurred, so we pick up the correct altered state. */

    //if "place order" button hit, this action catches and errors as appropriate
    add_action( 'woocommerce_before_checkout_process', array(&$this, 'vtmax_woo_place_order_cntl') );   
    
    //save info to Lifetime tables following purchase       

     add_action('woocommerce_checkout_order_processed', array( &$this, 'vtmax_pre_purchase_save_session' ) );  // v1.07.2
     add_action('woocommerce_thankyou',                 array( &$this, 'vtmax_post_purchase_save_info' ) );    // v1.07.2  

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
    vtmax_debug_options();  //v1.07
        
    //input and output to the apply_rules routine in the global variables.
    //    results are put into $vtmax_cart
    /*v1.07 cart not there yet...
    if ( $vtmax_cart->error_messages_processed == 'yes' ) {  
      wc_add_notice( __('Maximum Purchase error found.', 'vtmin'), $notice_type = 'error' );   //supplies an error msg and prevents payment from completing   v1.07      
      return;
    }
    */
    
     $vtmax_apply_rules = new VTMAX_Apply_Rules;   
    
    //ERROR Message Path
    if ( sizeof($vtmax_cart->error_messages) > 0 ) {      
      
      //v1.07 changes begin
        switch( $vtmax_cart->error_messages_are_custom ) {  
          case 'all':
               $this->vtmax_display_custom_messages();
            break;
          case 'some':    
               $this->vtmax_display_custom_messages();
               $this->vtmax_display_standard_messages();
            break;           
          default:  //'none' / no state set yet
               $this->vtmax_display_standard_messages();
              $current_version =  WOOCOMMERCE_VERSION;
              if( (version_compare(strval('2.1.0'), strval($current_version), '>') == 1) ) {   //'==1' = 2nd value is lower     
                $woocommerce->add_error(  __('Maximum Purchase error found.', 'vtmin') );  //supplies an error msg and prevents payment from completing 
              } else {
               //added in woo 2.1
                wc_add_notice( __('Maximum Purchase error found.', 'vtmin'), $notice_type = 'error' );   //supplies an error msg and prevents payment from completing   v1.07
              }             
            break;                    
        }

      //v1.07 changes end 
    }     
  }

  /* ************************************************
  **   v1.07 New Function
  *************************************************** */
  public function vtmax_display_standard_messages() {
    global $vtmax_cart, $vtmax_cart_item, $vtmax_rules_set, $vtmax_rule, $vtmax_info, $woocommerce;
    //insert error messages into checkout page
    add_action( "wp_enqueue_scripts", array($this, 'vtmax_enqueue_error_msg_css') );
    add_action('wp_head', array(&$this, 'vtmax_display_rule_error_msg_at_checkout') );  //JS to insert error msgs 
    $vtmax_cart->error_messages_processed = 'yes';
  } 

  /* ************************************************
  **   v1.07 New Function
  *************************************************** */
  public function vtmax_display_custom_messages() {
    global $vtmax_cart, $vtmax_cart_item, $vtmax_rules_set, $vtmax_rule, $vtmax_info, $woocommerce;
    
    for($i=0; $i < sizeof($vtmax_cart->error_messages); $i++) { 
       if ($vtmax_cart->error_messages[$i]['msg_is_custom'] == 'yes') {  //v1.08 ==>> show custom messages here...
          
              //v1.07 begin
              $current_version =  WOOCOMMERCE_VERSION;
              if( (version_compare(strval('2.1.0'), strval($current_version), '>') == 1) ) {   //'==1' = 2nd value is lower     
                $woocommerce->add_error(  $vtmax_cart->error_messages[$i]['msg_text'] );  //supplies an error msg and prevents payment from completing 
              } else {
               //added in woo 2.1
                wc_add_notice( $vtmax_cart->error_messages[$i]['msg_text'], $notice_type = 'error' );
              } 
              //v1.07 end               
       } //end if
    }  //end 'for' loop    
  }   
  
        
           
  /* ************************************************
  **   Application - Apply Rules at Woo E-Commerce  ==> AT Place Order Time <==
  *************************************************** */
	public function vtmax_woo_place_order_cntl(){
    global $vtmax_cart, $vtmax_cart_item, $vtmax_rules_set, $vtmax_rule, $vtmax_info, $woocommerce;
        
    //input and output to the apply_rules routine in the global variables.
    //    results are put into $vtmax_cart
    
    /*  v1.07 $vtmax_cart not there yet!
    if ( $vtmax_cart->error_messages_processed == 'yes' ) {  
      wc_add_notice( __('Maximum Purchase error found.', 'vtmin'), $notice_type = 'error' );   //supplies an error msg and prevents payment from completing   v1.07
      return;
    }
    */
     vtmax_debug_options();  //v1.07    
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
      //  These are the incorrectly displayed contens of the 'add_notice' function below, and are only a problem in this particular situation
      echo '<div class="woo-apply-checkout-cntl">';  // This 'echo' allows the incorrectly displayed error msg to fall within the 'woo-apply-checkout-cntl' div, and be deleted by following JS
      $woo_apply_checkout_cntl = 'yes';
      
      //display VTMAX error msgs   
      $this->vtmax_display_rule_error_msg_at_checkout();      
      
      $vtmax_cart->error_messages_processed = 'yes';
      
      //tell WOO that an error has occurred, and not to proceed further
      
      //v1.07 changes begin
        switch( $vtmax_cart->error_messages_are_custom ) {  
          case 'all':
               $this->vtmax_display_custom_messages();
            break;
          case 'some':    
               $this->vtmax_display_custom_messages();
               $this->vtmax_display_standard_messages();
            break;           
          default:  //'none' / no state set yet
               $this->vtmax_display_standard_messages();
              $current_version =  WOOCOMMERCE_VERSION;
              if( (version_compare(strval('2.1.0'), strval($current_version), '>') == 1) ) {   //'==1' = 2nd value is lower     
                $woocommerce->add_error(  __('Maximum Purchase error found.', 'vtmin') );  //supplies an error msg and prevents payment from completing 
              } else {
               //added in woo 2.1
                wc_add_notice( __('Maximum Purchase error found.', 'vtmin'), $notice_type = 'error' );   //supplies an error msg and prevents payment from completing   v1.07
              }                             
            break;                    
        }
        //v1.07 end 
     } //end-if
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
       if ($vtmax_cart->error_messages[$i]['msg_is_custom'] != 'yes') {  //v1.07 ==>> don't show custom messages here...
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
       } //v1.07 end if
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
 
   //v1.07 begin
  /* ************************************************
  **   Application - get current page url
  *       
  *       The code checking for 'www.' is included since
  *       some server configurations do not respond with the
  *       actual info, as to whether 'www.' is part of the 
  *       URL.  The additional code balances out the currURL,
  *       relative to the Parent Plugin's recorded URLs           
  *************************************************** */ 
 public  function vtmax_currPageURL() {
     global $vtmax_info;
     $currPageURL = $this->vtmax_get_currPageURL();
     $www = 'www.';
     
     $curr_has_www = 'no';
     if (strpos($currPageURL, $www )) {
         $curr_has_www = 'yes';
     }
     
     //use checkout URL as an example of all setup URLs
     $checkout_has_www = 'no';
     if (strpos($vtmax_info['woo_checkout_url'], $www )) {
         $checkout_has_www = 'yes';
     }     
         
     switch( true ) {
        case ( ($curr_has_www == 'yes') && ($checkout_has_www == 'yes') ):
        case ( ($curr_has_www == 'no')  && ($checkout_has_www == 'no') ): 
            //all good, no action necessary
          break;
        case ( ($curr_has_www == 'no') && ($checkout_has_www == 'yes') ):
            //reconstruct the URL with 'www.' included.
            $currPageURL = $this->vtmax_get_currPageURL($www); 
          break;
        case ( ($curr_has_www == 'yes') && ($checkout_has_www == 'no') ): 
            //all of the woo URLs have no 'www.', and curr has it, so remove the string 
            $currPageURL = str_replace($www, "", $currPageURL);
          break;
     } 
 
     return $currPageURL;
  } 
 public  function vtmax_get_currPageURL($www = null) {
     global $vtmax_info;
     $pageURL = 'http';
     //if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
     if ( isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" ) { $pageURL .= "s";}
     $pageURL .= "://";
     $pageURL .= $www;   //mostly null, only active rarely, 2nd time through - see above
     
     //NEVER create the URL with the port name!!!!!!!!!!!!!!
     $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
     /* 
     if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
     } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
     }
     */
     return $pageURL;
  }  
   //v1.07 end 

  /* ************************************************
  **   Application - On Error enqueue error style
  *************************************************** */
  public function vtmax_enqueue_error_msg_css() {
    wp_register_style( 'vtmax-error-style', VTMAX_URL.'/core/css/vtmax-error-style.css' );  
    wp_enqueue_style('vtmax-error-style');
  } 
 
 
  // v1.07.2 begin
  /* ************************************************
  **   before purchase, save info to session
  *************************************************** */ 
  function vtmax_pre_purchase_save_session() { 
    global $post, $wpdb, $vtmax_setup_options, $vtmax_cart, $vtmax_rules_set, $vtmax_rule, $vtmax_info;
              
      if(!isset($_SESSION)){
        session_start();
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");
      } 
      $data_chain = array();
      $data_chain[] = $vtmax_rules_set;
      $data_chain[] = $vtmax_cart;
      $data_chain[] = $vtmax_info;
      $_SESSION['data_chain'] = serialize($data_chain);  
    
    return; 
    
  } 
  // v1.07.2 end
  
  /* ************************************************
  **   After purchase, store max purchase info for lifetime rules on db
  *************************************************** */ 
  function vtmax_post_purchase_save_info() { 
          
    if(defined('VTMAX_PRO_DIRNAME')) {
      require ( VTMAX_PRO_DIRNAME . '/woo-integration/vtmax-save-purchase-info.php');
    }
    
    return; 
    
  } // end  function vtmax_store_max_purchaser_info() 
 
 
   // v1.07.2 begin
   function vtmax_get_data_chain() {
         
      if(!isset($_SESSION)){
        session_start();
        header("Cache-Control: no-cache");
        header("Pragma: no-cache");
      }   
      global $vtmax_rules_set, $vtmax_cart, $vtmax_info;
      
      if (isset($_SESSION['data_chain'])) {
        $data_chain      = unserialize($_SESSION['data_chain']);
      } else {
        $data_chain = array();
      }
         
      if ($vtmax_rules_set == '') {        
        $vtmax_rules_set = $data_chain[0];
        $vtmax_cart      = $data_chain[1];
        $vtmax_info      = $data_chain[2];
      }

      return $data_chain;
   }
   // v1.07.2  end
   
} //end class
$vtmax_parent_cart_validation = new VTMAX_Parent_Cart_Validation;