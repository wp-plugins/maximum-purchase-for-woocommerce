<?php
/*
VarkTech Maximum Purchase for WooCommerce
Woo-specific functions
Parent Plugin Integration
*/


class VTMAX_Parent_Definitions {
	
	public function __construct(){
    
    define('VTMAX_PARENT_PLUGIN_NAME',                      'WooCommerce');
    define('VTMAX_EARLIEST_ALLOWED_PARENT_VERSION',         '1.0');
    define('VTMAX_TESTED_UP_TO_PARENT_VERSION',             '1.6.6');
    define('VTMAX_DOCUMENTATION_PATH_PRO_BY_PARENT',        'http://www.varktech.com/woocommerce/maximum-purchase-pro-for-woocommerce/?active_tab=tutorial');                                                                                                     //***
    define('VTMAX_DOCUMENTATION_PATH_FREE_BY_PARENT',       'http://www.varktech.com/woocommerce/maximum-purchase-for-woocommerce/?active_tab=tutorial');      
    define('VTMAX_INSTALLATION_INSTRUCTIONS_BY_PARENT',     'http://www.varktech.com/woocommerce/maximum-purchase-for-woocommerce/?active_tab=instructions');
    define('VTMAX_PRO_INSTALLATION_INSTRUCTIONS_BY_PARENT', 'http://www.varktech.com/woocommerce/maximum-purchase-pro-for-woocommerce/?active_tab=instructions');
    define('VTMAX_PURCHASE_PRO_VERSION_BY_PARENT',          'http://www.varktech.com/woocommerce/maximum-purchase-pro-for-woocommerce/');
    define('VTMAX_DOWNLOAD_FREE_VERSION_BY_PARENT',         'http://wordpress.org/extend/plugins/maximum-purchase-for-woocommerce/');
    
    //html default selector locations in checkout where error message will display before.
    define('VTMAX_CHECKOUT_PRODUCTS_SELECTOR_BY_PARENT',    '.shop_table');        // PRODUCTS TABLE on BOTH cart page and checkout page
    define('VTMAX_CHECKOUT_ADDRESS_SELECTOR_BY_PARENT',     '#customer_details');      //  address area on checkout page    default = on


    global $vtmax_info;      
    $vtmax_info = array(                                                                    
      	'parent_plugin' => 'woo',
      	'parent_plugin_taxonomy' => 'product_cat',
        'parent_plugin_taxonomy_name' => 'Product Categories',
        'parent_plugin_cpt' => 'product',
        'applies_to_post_types' => 'product', //rule cat only needs to be registered to product, not rule as well...
        'rulecat_taxonomy' => 'vtmax_rule_category',
        'rulecat_taxonomy_name' => 'Maximum Purchase Rules',
        
        /* *************************************************** */
        /*
                        THE FOLLOWING ELEMENTS                      
          are used as temporary iterative processing storage
          in vtmax-apply-rules.php
                                                               */
        /* *************************************************** */
        //elements used at the ruleset level
        'error_message_needed' => 'no',
        'cart_grp_info' => '',
          /*  cart_grp_info will contain the following:
            array(
              'qty'    => '',
              'price'    => ''
            )
          */
        'cart_color_cnt' => '',
        'rule_id_list' => '',
        'line_cnt' => 0,
        'action_cnt'  => 0,
        'bold_the_error_amt_on_detail_line'  => 'no',
        'currPageURL'  => '',
        'woo_cart_url'  => '',
        'woo_checkout_url'  => '',
        'woo_pay_url'  => '',
        
        //elements used at the ruleset/product level 
        'purch_hist_product_row_id'  => '',              
        'purch_hist_product_price_total'  => '',      
        'purch_hist_product_qty_total'  => '',          
        'get_purchaser_info' => '',          
        'purch_hist_done' => ''          
      );

	}

} //end class
$vtmax_parent_definitions = new VTMAX_Parent_Definitions;