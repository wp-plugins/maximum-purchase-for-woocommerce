<?php

/**
 *   based on code from the following:  (example is a tabbed settings page)
 *  http://wp.tutsplus.com/series/the-complete-guide-to-the-wordpress-settings-api/   
 *    (code at    https://github.com/tommcfarlin/WordPress-Settings-Sandbox) 
 *  http://www.chipbennett.net/2011/02/17/incorporating-the-settings-api-in-wordpress-themes/?all=1 
 *  http://www.presscoders.com/2010/05/wordpress-settings-api-explained/  
 */
class VTMAX_Setup_Plugin_Options { 
	
	public function __construct(){ 
  
    add_action( 'admin_init', array( &$this, 'vtmax_initialize_options' ) );
    add_action( 'admin_menu', array( &$this, 'vtmax_add_admin_menu_setup_items' ) );
    
  } 


function vtmax_add_admin_menu_setup_items() {
 // add items to the maximum purchase custom post type menu structure
	add_submenu_page(
		'edit.php?post_type=vtmax-rule',	// The ID of the top-level menu page to which this submenu item belongs
		__( 'Rules Options Settings', 'vtmax' ), // The value used to populate the browser's title bar when the menu page is active                           
		__( 'Rules Options Settings', 'vtmax' ),					// The label of this submenu item displayed in the menu
		'administrator',					// What roles are able to access this submenu item
		'vtmax_setup_options_page',	// The slug used to represent this submenu item
		array( &$this, 'vtmax_setup_options_cntl' ) 				// The callback function used to render the options for this submenu item
	);
  
 if(!defined('VTMAX_PRO_DIRNAME')) {  //update to pro version...
       add_submenu_page(
    		'edit.php?post_type=vtmax-rule',	// The ID of the top-level menu page to which this submenu item belongs
    		__( 'Upgrade to Maximum Purchase Pro', 'vtmax' ), // The value used to populate the browser's title bar when the menu page is active                           
    		__( 'Upgrade to Pro', 'vtmax' ),					// The label of this submenu item displayed in the menu
    		'administrator',					// What roles are able to access this submenu item
    		'vtmax_pro_upgrade',	// The slug used to represent this submenu item
    		array( &$this, 'vtmax_pro_upgrade_cntl' ) 				// The callback function used to render the options for this submenu item
    	);
  } 


  //v1.07 begin
  //Add a DUPLICATE custom tax URL to be in the main Pricing Deals menu as well as in the PRODUCT menu
  //post_type=product => PARENT plugin post_type
    add_submenu_page(
		'edit.php?post_type=vtmax-rule',	// The ID of the top-level menu page to which this submenu item belongs
		__( 'Maximum Purchase Categories', 'vtmax' ), // The value used to populate the browser's title bar when the menu page is active                           
		__( 'Maximum Purchase Categories', 'vtmax' ),					// The label of this submenu item displayed in the menu
		'administrator',					// What roles are able to access this submenu item
		'edit-tags.php?taxonomy=vtmax_rule_category&post_type=product',	// The slug used to represent this submenu item
    //                                          PARENT PLUGIN POST TYPE      
		''  				// NO CALLBACK FUNCTION REQUIRED
	);
  //v1.07 end  
    
  
} 

function vtmax_pro_upgrade_cntl() {

    //PRO UPGRADE PAGE
 ?>
  <style type="text/css">
      #upgrade-div {
                float: left;
                margin:40px 0 0 100px;
               /* width: 2.5%;     */
                border: 1px solid #CCCCCC;
                border-radius: 5px 5px 5px 5px;
                padding: 15px;
                font-size:14px;
                width:500px;
            }
      #upgrade-div h3, #upgrade-div h4 {margin-left:20px;}
      #upgrade-div ul {list-style-type: square;margin-left:50px;}
      #upgrade-div ul li {font-size:16px;}
      #upgrade-div a {font-size:16px; margin-left:23%;font-weight: bold;} 
  </style>
   
	<div class="wrap">
		<div id="icon-themes" class="icon32"></div>
    
		<h2><?php esc_attr_e('Upgrade to Maximum Purchase Pro', 'vtmax'); ?></h2>
    
    <div id="upgrade-div">
        <h3><?php _e('Maximum Purchase Pro offers considerable versatility in creating maximum purchase rules.', 'vtmax') ?></h3>
        <h4><?php _e('In Maximum Purchase Pro, you can choose to apply the rule to:', 'vtmax') ?></h4>
        <ul>
          <li><?php _e('the entire contents of the cart.', 'vtmax') ?></li>
          <li><?php _e('an individual product.', 'vtmax') ?></li>
          <li><?php _e('the variations for an individual product.', 'vtmax') ?></li>
          <li><?php _e('those products in a particular Product category or group of categories.', 'vtmax') ?></li>
          <li><?php _e('those products in a particular Minumum Purchase category or group of categories. (particularly useful if you need to define a group outside of existing Product Categories)', 'vtmax') ?></li>
          <li><?php _e('Membership Status, inclusive or exclusive of category participation.', 'vtmax') ?></li>
          <li><?php _e('<em>Set cumulative lifetime limits on rule purchases by customer.</em>', 'vtmax') ?></li>
        </ul>
        <a  href=" <?php echo VTMAX_PURCHASE_PRO_VERSION_BY_PARENT ; ?> "  title="Access Plugin Documentation"> Upgrade to Maximum Purchase Pro</a>                  
    </div>  
  </div>
 
 <?php
}

/**
 * Renders a simple page to display for the menu item added above.
 */
function vtmax_setup_options_cntl() {
  //add help tab to this screen...
  //$vtmax_backbone->vtmax_add_help_tab ();
    $content = '<br><a  href="' . VTMAX_DOCUMENTATION_PATH . '"  title="Access Plugin Documentation">Access Plugin Documentation</a>';
    $screen = get_current_screen();
    $screen->add_help_tab( array( 
       'id' => 'vtmax-help-options',            //unique id for the tab
       'title' => 'Maximum Purchase Options Help',      //unique visible title for the tab
       'content' => $content  //actual help text
      ) );

    //OPTIONS PAGE
?>
  <style type="text/css">
      .form-table th {
          width: 350px;
      }
      .form-table td {
          padding: 8px 30px;
      }
      #help-all {font-size: 12px; text-decoration:none; border: 1px solid #DFDFDF; padding:3px;}
      #help-all span {font-style:normal; text-decoration:underline; font-weight:normal;}
      .help-anchor {margin-left:30px;}
      .help-text {display:none; font-style:italic; }
       h3 {margin-top:40px;}
       h4 {font-style:italic;}
      .form-table, h4 {margin-left:30px;font-size:14px;}
      .form-table td p {width: 95%;}
      #nuke-rules-button, #nuke-cats-button, #nuke-hist-button, #repair-button {color:red; margin-left:30px}
      #nuke-rules-button:hover, #nuke-cats-button:hover, #nuke-hist-button:hover, #repair-button:hover {cursor:hand; cursor:pointer; font-weight:bold;}
      
      #system-info-title {float:left; margin-top:70px;}
      .system-info-subtitle {clear:left;float:left;}
      .system-info {float:left;margin-bottom:15px; margin-left:30px;}
      .system-info-line {width:95%; float:left; margin-bottom:10px;}
      .system-info-label {width:40%; float:left; font-style:italic;}
      .system-info-data  {width:60%; float:left; font-weight:bold;}
      #custom_error_msg_css_at_checkout {width:500px;height:100px;}
  </style>
   
  <script type="text/javascript" language="JavaScript"> 
      jQuery(document).ready(function($) {
          $("#help-all").click(function(){
              $(".help-text").toggle("slow");                         
          });
          $("#help1").click(function(){
              $("#help1-text").toggle("slow");                           
          });
          $("#help2").click(function(){
              $("#help2-text").toggle("slow");                           
          });
          $("#help3").click(function(){
              $("#help3-text").toggle("slow");                           
          });  
          $("#help4").click(function(){
              $("#help4-text").toggle("slow");                           
          });
          $("#help5").click(function(){
              $("#help5-text").toggle("slow");                           
          });
          $("#help6").click(function(){
              $("#help6-text").toggle("slow");                           
          }); 
          $("#help7").click(function(){
              $("#help7-text").toggle("slow");                           
          }); 
          $("#help8").click(function(){
              $("#help8-text").toggle("slow");                           
          }); 
          $("#help9").click(function(){
              $("#help9-text").toggle("slow");                           
          }); 
          $("#help10").click(function(){
              $("#help10-text").toggle("slow");                           
          });
          $("#help11").click(function(){
              $("#help11-text").toggle("slow");                           
          });
          $("#help12").click(function(){
              $("#help12-text").toggle("slow");                           
          });
          $("#help13").click(function(){
              $("#help13-text").toggle("slow");                           
          });
          $("#help14").click(function(){
              $("#help14-text").toggle("slow");                           
          });
          $("#help15").click(function(){
              $("#help15-text").toggle("slow");                           
          });
          $("#help16").click(function(){
              $("#help16-text").toggle("slow");                           
          });
          $("#help17").click(function(){
              $("#help17-text").toggle("slow");                           
          });
          $("#help18").click(function(){
              $("#help18-text").toggle("slow");                           
          });
          $("#help19").click(function(){
              $("#help19-text").toggle("slow");                           
          });
          $("#help20").click(function(){
              $("#help20-text").toggle("slow");                           
          });
                
      });  
  
  </script>
  
  <?php
  if(!defined('VTMAX_PRO_DIRNAME')) {  
        // **********************************************
      // also disable and grey out options on free version
      // **********************************************
        ?>
        <style type="text/css">
             #show_prodcat,
             #show_rulecat,
             #vtmax-lifetime-limit-by-ip,
             #vtmax-lifetime-limit-by-email,
             #vtmax-lifetime-limit-by-billto-name,
             #vtmax-lifetime-limit-by-billto-addr,
             #vtmax-lifetime-limit-by-shipto-name,             
             #vtmax-lifetime-limit-by-shipto-addr
             {color:#aaa;}  /*grey out unavailable choices*/
        </style>
        <script type="text/javascript">
            jQuery.noConflict();
            jQuery(document).ready(function($) {                                                        
              // To disable 
              //  $('.someElement').attr('disabled', 'disabled');  
              $('#show_prodcat').attr('disabled', 'disabled');
              $('#show_rulecat').attr('disabled', 'disabled');
              $('#vtmax-lifetime-limit-by-ip').attr('disabled', 'disabled');
              $('#vtmax-lifetime-limit-by-email').attr('disabled', 'disabled');
              $('#vtmax-lifetime-limit-by-billto-name').attr('disabled', 'disabled');             
              $('#vtmax-lifetime-limit-by-billto-addr').attr('disabled', 'disabled');
              $('#vtmax-lifetime-limit-by-shipto-name').attr('disabled', 'disabled');
              $('#vtmax-lifetime-limit-by-shipto-addr').attr('disabled', 'disabled');
            }); //end ready function 
        </script>
  <?php } ?>
  
	<div class="wrap">
		<div id="icon-themes" class="icon32"></div>
    
		<h2>
      <?php 
        if(defined('VTMAX_PRO_DIRNAME')) { 
          esc_attr_e('Maximum Purchase Pro Options', 'vtmax'); 
        } else {
          esc_attr_e('Maximum Purchase Options', 'vtmax'); 
        }    
      ?>    
    </h2>
    
		<?php settings_errors(); ?>
    
    <?php 
    /*if ( isset( $_GET['settings-updated'] ) ) {
         echo "<div class='updated'><p>Theme settings updated successfully.</p></div>";
    } */
    ?>
		
		<form method="post" action="options.php">
			<?php
          //WP functions to execute the registered settings!
					settings_fields( 'vtmax_setup_options_group' );     //activates the field settings setup below
					do_settings_sections( 'vtmax_setup_options_page' );   //activates the section settings setup below 
				
				submit_button();        			
			?>
      
      <input name="vtmax_setup_options[options-reset]"      type="submit" class="button-secondary"  value="<?php esc_attr_e('Reset to Defaults', 'vtmax'); ?>" />
      
      <p id="system-buttons">
        <h3><?php esc_attr_e('Maximum Purchase Rules Repair and Delete Buttons', 'vtmax'); ?></h3> 
        <h4><?php esc_attr_e('Repair reknits the Rules Custom Post Type with the Maximum Purchase rules option array, if out of sync.', 'vtmax'); ?></h4>        
        <input id="repair-button"       name="vtmax_setup_options[rules-repair]"  type="submit" class="button-fourth"     value="<?php esc_attr_e('Repair Rules Structures', 'vtmax'); ?>" /> 
        <h4><?php esc_attr_e('Nuke Rules deletes all Maximum Purchase Rules.', 'vtmax'); ?></h4>
        <input id="nuke-rules-button"   name="vtmax_setup_options[rules-nuke]"     type="submit" class="button-third"      value="<?php esc_attr_e('Nuke all Rules', 'vtmax'); ?>" />
        <h4><?php esc_attr_e('Nuke Rule Cats deletes all Maximum Purchase Rule Categories', 'vtmax'); ?></h4>
        <input id="nuke-cats-button"    name="vtmax_setup_options[cats-nuke]"      type="submit" class="button-fifth"      value="<?php esc_attr_e('Nuke all Rule Cats', 'vtmax'); ?>" />
        <h4><?php esc_attr_e('Nuke Max Purchase History Tables', 'vtmax'); ?></h4>
        <input id="nuke-hist-button"    name="vtmax_setup_options[hist-nuke]"      type="submit" class="button-fifth"      value="<?php esc_attr_e('Nuke Max Purchase History Tables', 'vtmax'); ?>" />
      </p>       
		</form>
    
    
    <?php 
    global $vtmax_setup_options, $wp_version;
    $vtmax_setup_options = get_option( 'vtmax_setup_options' );	  
    $vtmax_functions = new VTMAX_Functions;
    $your_system_info = $vtmax_functions->vtmax_getSystemMemInfo();
    ?>
    
    <h3 id="system-info-title">Plugin Info</h3>
    
    <h4 class="system-info-subtitle">System Info</h4>
    <span class="system-info">
       <span class="system-info-line"><span class="system-info-label">FREE_VERSION: </span> <span class="system-info-data"><?php echo VTMAX_VERSION;  ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">FREE_LAST_UPDATE_DATE: </span> <span class="system-info-data"><?php echo VTMAX_LAST_UPDATE_DATE;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">FREE_DIRNAME: </span> <span class="system-info-data"><?php echo VTMAX_DIRNAME;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">URL: </span> <span class="system-info-data"><?php echo VTMAX_URL;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">EARLIEST_ALLOWED_WP_VERSION: </span> <span class="system-info-data"><?php echo VTMAX_EARLIEST_ALLOWED_WP_VERSION;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">WP VERSION: </span> <span class="system-info-data"><?php echo $wp_version; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">EARLIEST_ALLOWED_PHP_VERSION: </span> <span class="system-info-data"><?php echo VTMAX_EARLIEST_ALLOWED_PHP_VERSION ;?></span> </span>
       <span class="system-info-line"><span class="system-info-label">FREE_PLUGIN_SLUG: </span> <span class="system-info-data"><?php echo VTMAX_PLUGIN_SLUG;  ?></span></span>
     </span> 
    
    <h4 class="system-info-subtitle">Parent Plugin Info</h4>
    <span class="system-info">
       <span class="system-info-line"><span class="system-info-label">PARENT_PLUGIN_NAME: </span> <span class="system-info-data"><?php echo VTMAX_PARENT_PLUGIN_NAME;  ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">EARLIEST_ALLOWED_PARENT_VERSION: </span> <span class="system-info-data"><?php echo VTMAX_EARLIEST_ALLOWED_PARENT_VERSION;  ?></span></span>
       
       <?php if(defined('WPSC_VERSION')        && (VTMAX_PARENT_PLUGIN_NAME == 'WP E-Commerce') ) { ?>
       <span class="system-info-line"><span class="system-info-label">PARENT_VERSION (WPSC): </span> <span class="system-info-data"><?php echo WPSC_VERSION;  ?></span></span>
       <?php } ?>
       
       <?php if(defined('WOOCOMMERCE_VERSION') && (VTMAX_PARENT_PLUGIN_NAME == 'WooCommerce')) { ?>
       <span class="system-info-line"><span class="system-info-label">PARENT_VERSION (WOOCOMMERCE): </span> <span class="system-info-data"><?php echo WOOCOMMERCE_VERSION;  ?></span></span>
       <?php } ?>
       
       <?php if(defined('JIGOSHOP_VERSION') && (VTMAX_PARENT_PLUGIN_NAME == 'JigoShop')) {  ?>
       <span class="system-info-line"><span class="system-info-label">PARENT_VERSION (JIGOSHOP): </span> <span class="system-info-data"><?php echo JIGOSHOP_VERSION;  ?></span></span>
       <?php } ?>
       
       <span class="system-info-line"><span class="system-info-label">TESTED_UP_TO_PARENT_VERSION: </span> <span class="system-info-data"><?php echo VTMAX_TESTED_UP_TO_PARENT_VERSION;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">CHECKOUT_PRODUCTS_SELECTOR_BY_PARENT: </span> <span class="system-info-data"><?php echo VTMAX_CHECKOUT_PRODUCTS_SELECTOR_BY_PARENT;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">CHECKOUT_ADDRESS_SELECTOR_BY_PARENT: </span> <span class="system-info-data"><?php echo VTMAX_CHECKOUT_ADDRESS_SELECTOR_BY_PARENT;  ?></span></span>
        
     </span> 

     <?php   if (defined('VTMAX_PRO_DIRNAME')) {  ?> 
      <h4 class="system-info-subtitle">Pro Info</h4>
      <span class="system-info">      
       <span class="system-info-line"><span class="system-info-label">PRO_PLUGIN_NAME: </span> <span class="system-info-data"><?php echo VTMAX_PRO_PLUGIN_NAME; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">PRO_FREE_PLUGIN_NAME: </span> <span class="system-info-data"><?php echo VTMAX_PRO_FREE_PLUGIN_NAME; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">PRO_VERSION: </span> <span class="system-info-data"><?php echo VTMAX_PRO_VERSION; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">PRO_LAST_UPDATE_DATE: </span> <span class="system-info-data"><?php echo VTMAX_PRO_LAST_UPDATE_DATE;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">PRO_DIRNAME: </span> <span class="system-info-data"><?php echo VTMAX_PRO_DIRNAME;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">PRO_MINIMUM_REQUIRED_FREE_VERSION: </span> <span class="system-info-data"><?php echo VTMAX_PRO_MINIMUM_REQUIRED_FREE_VERSION;  ?></span></span>
       <span class="system-info-line"><span class="system-info-label">PRO_BASE_NAME: </span> <span class="system-info-data"><?php echo VTMAX_PRO_BASE_NAME; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">PRO_PLUGIN_SLUG: </span> <span class="system-info-data"><?php echo VTMAX_PLUGIN_SLUG; ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">PRO_REMOTE_VERSION_FILE: </span> <span class="system-info-data"><?php echo VTMAX_PRO_REMOTE_VERSION_FILE; ?></span> </span>
      </span> 
     <?php   }  ?>   

        
     <?php   if ( $vtmax_setup_options['debugging_mode_on'] == 'yes' ){  ?> 
     <h4 class="system-info-subtitle">Debug Info</h4>
      <span class="system-info">                  
       <span class="system-info-line"><span class="system-info-label">PHP VERSION: </span> <span class="system-info-data"><?php echo phpversion(); ?></span> </span>
       <span class="system-info-line"><span class="system-info-label">SYSTEM MEMORY: </span> <span class="system-info-data"><?php echo '<pre>'.print_r( $your_system_info , true).'</pre>' ;  ?></span> </span>
     </span> 
     <?php   }  ?>
  
	</div><!-- /.wrap -->

<?php
} // end vtmax_display  


/* ------------------------------------------------------------------------ *
 * Setting Registration
 * ------------------------------------------------------------------------ */ 

/**
 * Initializes the theme's display options page by registering the Sections,
 * Fields, and Settings.
 *
 * This function is registered with the 'admin_init' hook.
 */ 

function vtmax_initialize_options() {
  
	// If the theme options don't exist, create them.
	if( false == get_option( 'vtmax_setup_options' ) ) {
		add_option( 'vtmax_setup_options', $this->vtmax_get_default_options() );  //add the option into the table based on the default values in the function.
	} // end if


  //****************************
  //  DISPLAY OPTIONS Area
  //****************************

	// First, we register a section. This is necessary since all future options must belong to a 
	add_settings_section(
		'general_settings_section',			// ID used to identify this section and with which to register options
		__( 'Display Options', 'vtmax' ),	// Title to be displayed on the administration page
		array(&$this, 'vtmax_general_options_callback'),	// Callback used to render the description of the section
		'vtmax_setup_options_page'		// Page on which to add this section of options
	);
		
	// show error msg = yes/no
	add_settings_field(	           //opt1
		'show_error_messages_in_table_form',						// ID used to identify the field throughout the theme
		__( 'Show Error Messages in Table Format ("no" = text format)', 'vtmax' ),		// The label to the left of the option interface element        
		array(&$this, 'vtmax_error_in_table_format_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Error messages can be shown in table formats.', 'vtmax' )
		)
	);                                                        
	// show error msg = yes/no
	add_settings_field(	           //opt2
		'show_error_before_checkout_products',						// ID used to identify the field throughout the theme
		__( 'Show Error Messages Just Before Checkout Products List', 'vtmax' ),							// The label to the left of the option interface element    
		array(&$this, 'vtmax_before_checkout_products_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'Error messages are shown in one place at checkout by default.', 'vtmax' )
		)
	);
       // customize error selector 1
    add_settings_field(	         //opt11
		'show_error_before_checkout_products_selector',						// ID used to identify the field throughout the theme
		__( 'Show Error Messages Just Before Checkout Products List - HTML Selector <em>(see => "more info")</em>', 'vtmax' ),							// The label to the left of the option interface element
		array(&$this, 'vtmax_before_checkout_products_selector_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'For the Product area, Supplies the ID or Class HTML selector this message appears before', 'vtmax' )
		)
	);
  	// show error msg = yes/no
    add_settings_field(	         //opt3
		'show_error_before_checkout_address',						// ID used to identify the field throughout the theme
		__( 'Show 2nd Set of Error Messages at Checkout Address Area', 'vtmax' ),							// The label to the left of the option interface element
		array(&$this, 'vtmax_before_checkout_address_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Error messages are shown in one place at checkout by default.', 'vtmax' )
		)
	);
         // customize error selector 2
    add_settings_field(	         //opt12
		'show_error_before_checkout_address_selector',						// ID used to identify the field throughout the theme
		__( 'Show Error Messages Just Before Checkout Address List - HTML Selector <em>(see => "more info")</em>', 'vtmax' ),							// The label to the left of the option interface element
		array(&$this, 'vtmax_before_checkout_address_selector_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'For the Address area, Supplies the ID or Class HTML selector this message appears before', 'vtmax' )
		)
	);
    	// show vtmax ID = yes/no
    add_settings_field(	         //opt10
		'show_rule_ID_in_errmsg',						// ID used to identify the field throughout the theme
		__( 'Show Rule ID in Error Message', 'vtmax' ),							// The label to the left of the option interface element
		array(&$this, 'vtmax_rule_ID_in_errmsg_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Show maximum amount rule id in error message.', 'vtmax' )
		)
	);
  	// show prod cats = yes/no
    add_settings_field(	         //opt4
		'show_prodcat_names_in_errmsg',						// ID used to identify the field throughout the theme
		__( 'Show Product Category Names in Maximum Purchase Error Message (Pro Only)', 'vtmax' ),							// The label to the left of the option interface element
		array(&$this, 'vtmax_prodcat_names_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'If Product Categories are used, show their names in any error messages based on the search criteria.', 'vtmax' )
		)                         
	);
    	// show rule cats = yes/no
    add_settings_field(	         //opt5
		'show_rulecat_names_in_errmsg',						// ID used to identify the field throughout the theme
		__( 'Show Rule Category Names in Maximum Purchase Error Message (Pro Only)', 'vtmax' ),							// The label to the left of the option interface element
		array(&$this, 'vtmax_rulecat_names_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'If Rule Categories are used, show their names in any error messages based on the search criteria.', 'vtmax' )
		)
	);                        
     // custom error msg css at checkout time
    add_settings_field(	         //opt9
		'custom_error_msg_css_at_checkout',						// ID used to identify the field throughout the theme
		__( 'Custom Maximum Purchase Error Message CSS, used at checkout time', 'vtmax' ),							// The label to the left of the option interface element
		array(&$this, 'vtmax_custom_error_msg_css_at_checkout_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'general_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'Realtime CSS override for maximum amount error messages shown at checkout time.  Supply CSS statements only.', 'vtmax' )
		)
	);
       

 
      
  //****************************
  //  PROCESSING OPTIONS Area
  //****************************
  
  	add_settings_section(
		'processing_settings_section',			// ID used to identify this section and with which to register options
		__( 'Processing Options', 'vtmax' ),// Title to be displayed on the administration page
		array(&$this, 'vtmax_processing_options_callback'), // Callback used to render the description of the section
		'vtmax_setup_options_page'		// Page on which to add this section of options
	);
	
  /* v1.07 
    add_settings_field(	         //opt6
		'use_this_currency_sign',						// ID used to identify the field throughout the theme
		__( 'Select a Currency Sign', 'vtmax' ),			// The label to the left of the option interface element
		array(&$this, 'vtmax_currency_sign_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'processing_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'Select a Currency Sign.', 'vtmax' )
		)
	);    
 */ 
    add_settings_field(	        //opt7
		'apply_multiple_rules_to_product',						// ID used to identify the field throughout the theme
		__( 'Apply More Than 1 Rule to Each Product', 'vtmax' ),			// The label to the left of the option interface element
		array(&$this, 'vtmax_mult_rules_processing_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'processing_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Do we apply multiple rules to a given product?', 'vtmax' )
		)
	);                
 
 
 
      
  //****************************
  //  LIFETIME RULE OPTIONS Area
  //****************************
  
  	add_settings_section(
		'lifetime_rule_settings_section',			// ID used to identify this section and with which to register options
		__( 'Lifetime Rule Options', 'vtmax' ),// Title to be displayed on the administration page
		array(&$this, 'vtmax_lifetime_rule_options_callback'), // Callback used to render the description of the section
		'vtmax_setup_options_page'		// Page on which to add this section of options
	);    
      add_settings_field(	        //opt13
		'max_purch_rule_lifetime_limit_by_ip',						// ID used to identify the field throughout the theme
		__( 'Check if a Customer has Rule Purchase History, <br>&nbsp; <i>by IP</i>', 'vtmax' ),			// The label to the left of the option interface element
		array(&$this, 'vtmax_lifetime_limit_by_ip_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Do we Check if a Customer has Rule Purchase History, by IP?', 'vtmax' )
		)
	);   
     
      add_settings_field(	        //opt14
		'max_purch_rule_lifetime_limit_by_email',						// ID used to identify the field throughout the theme
		__( 'Check if a Customer has Rule Purchase History, <br>&nbsp; <i>by Email</i>', 'vtmax' ),			// The label to the left of the option interface element
		array(&$this, 'vtmax_lifetime_limit_by_email_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Do we Check if a Customer has Rule Purchase History, by Email?', 'vtmax' )
		)
	);

          add_settings_field(	        //opt15
		'max_purch_rule_lifetime_limit_by_billto_name',						// ID used to identify the field throughout the theme
		__( 'Check if a Customer has Rule Purchase History, <br>&nbsp; <i>by BillTo Name</i>', 'vtmax' ),			// The label to the left of the option interface element
		array(&$this, 'vtmax_lifetime_limit_by_billto_name_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Do we Check if a Customer has Rule Purchase History, by BillTo Name?', 'vtmax' )
		)
	);

          add_settings_field(	        //opt16
		'max_purch_rule_lifetime_limit_by_billto_addr',						// ID u<br>&nbsp; sed to identify the field throughout the theme
		__( 'Check if a Customer has Rule Purchase History, <br>&nbsp; <i>by BillTo Address</i>', 'vtmax' ),			// The label to the left of the option interface element
		array(&$this, 'vtmax_lifetime_limit_by_billto_addr_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Check if a Customer has Rule Purchase History, by BillTo Address?', 'vtmax' )
		)
	);

          add_settings_field(	        //opt17
		'max_purch_rule_lifetime_limit_by_shipto_name',						// ID used to identify the field throughout the theme
		__( 'Check if a Customer has Rule Purchase History, <br>&nbsp; <i>by ShipTo Name</i>', 'vtmax' ),			// The label to the left of the option interface element
		array(&$this, 'vtmax_lifetime_limit_by_shipto_name_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Do we Check if a Customer has Rule Purchase History, by ShipTo Name?', 'vtmax' )
		)
	);

          add_settings_field(	        //opt18
		'max_purch_rule_lifetime_limit_by_shipto_addr',						// ID u<br>&nbsp; sed to identify the field throughout the theme
		__( 'Check if a Customer has Rule Purchase History, <br>&nbsp; <i>by ShipTo Address</i>', 'vtmax' ),			// The label to the left of the option interface element
		array(&$this, 'vtmax_lifetime_limit_by_shipto_addr_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Check if a Customer has Rule Purchase History, by ShipTo Address?', 'vtmax' )
		)
	);

 /*
            add_settings_field(	        //opt19
		'max_purch_checkout_forms_set',						// ID used to identify the field throughout the theme
		__( 'Primary Checkout Form Set => default set to "0"', 'vtmax' ),			// The label to the left of the option interface element
		array(&$this, 'vtmax_checkout_forms_set_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'lifetime_rule_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			 __( 'Primary Checkout Form Set', 'vtmax' )
		)
	);
 */
  //****************************
  //  SYSTEM AND DEBUG OPTIONS Area
  //****************************
  
  	add_settings_section(
		'internals_settings_section',			// ID used to identify this section and with which to register options
		__( 'System and Debug Options', 'vtmax' ),		// Title to be displayed on the administration page
		array(&$this, 'vtmax_internals_options_callback'), // Callback used to render the description of the section
		'vtmax_setup_options_page'		// Page on which to add this section of options
	);
	
    add_settings_field(	        //opt8
		'debugging_mode_on',						// ID used to identify the field throughout the theme
		__( 'Test Debugging Mode Turned On <br>(Use Only during testing)', 'vtmax' ),							// The label to the left of the option interface element
		array(&$this, 'vtmax_debugging_mode_callback'), // The name of the function responsible for rendering the option interface
		'vtmax_setup_options_page',	// The page on which this option will be displayed
		'internals_settings_section',			// The name of the section to which this field belongs
		array(								// The array of arguments to pass to the callback. In this case, just a description.
			__( 'Show any built-in debug info for Rule processing.', 'vtmax' )
		)
	);                    
  /*	
  
 */
	
	// Finally, we register the fields with WordPress
	register_setting(
		'vtmax_setup_options_group',
		'vtmax_setup_options' ,
    array(&$this, 'vtmax_validate_setup_input')
	);
	
} // end vtmax_initialize_options

   
  //****************************
  //  DEFAULT OPTIONS INITIALIZATION
  //****************************
function vtmax_get_default_options() {
     $options = array(
          'show_error_messages_in_table_form' => 'yes',  //opt1
          'show_error_before_checkout_products' => 'yes', //opt2
          'show_error_before_checkout_address' => 'yes', //opt3
          'show_prodcat_names_in_errmsg' => 'no',  //opt4
          'show_rulecat_names_in_errmsg' => 'no',  //opt5
          'use_this_currency_sign' => 'USD',  //opt6
          'apply_multiple_rules_to_product' => 'no', //opt7
          'debugging_mode_on' => 'no',  //opt8
          'custom_error_msg_css_at_checkout'  => '',  //opt9
          'show_rule_ID_in_errmsg' => 'yes',  //opt10
          'show_error_before_checkout_products_selector' => VTMAX_CHECKOUT_PRODUCTS_SELECTOR_BY_PARENT,  //opt11
          'show_error_before_checkout_address_selector'  => VTMAX_CHECKOUT_ADDRESS_SELECTOR_BY_PARENT,  //opt12
          'max_purch_rule_lifetime_limit_by_ip' => 'yes',  //opt13
          'max_purch_rule_lifetime_limit_by_email' => 'yes',  //opt14
          'max_purch_rule_lifetime_limit_by_billto_name' => 'yes',  //opt15
          'max_purch_rule_lifetime_limit_by_billto_addr' => 'yes',  //opt16
          'max_purch_rule_lifetime_limit_by_shipto_name' => 'yes',  //opt17
          'max_purch_rule_lifetime_limit_by_shipto_addr' => 'yes'   //opt18
       //   'max_purch_checkout_forms_set' => '0'  //opt19           
           
     );
     return $options;
}
   
function vtmax_processing_options_callback () {
    ?>
    <h4><?php esc_attr_e('These options control rule error processing during checkout.', 'vtmax'); ?></h4>
    <?php                                                                                                                                                                                      
}
   
function vtmax_lifetime_rule_options_callback () {
    ?>
    <h4><?php esc_attr_e('Lifetime rule Options apply to Lifetime Customer Max Purchases. (Lifetime processing rules are available with the Pro version)', 'vtmax'); ?></h4>
    <h4><?php esc_attr_e('These options control how comparisons are made, to see if a customer has purchased products associated with a given rule prior to the current purchase.', 'vtmax'); ?></h4>
    
    <?php                                                                                                                                                                                      
}

function vtmax_general_options_callback () {
    ?>
    <h4><?php esc_attr_e('These options control rule error message display at checkout time.', 'vtmax'); ?> 
      <a id="help-all" class="help-anchor" href="javascript:void(0);" >
      <?php esc_attr_e('Show All:', 'vtmax'); ?> 
      &nbsp; <span> <?php esc_attr_e('More Info', 'vtmax'); ?> </span></a> 
    </h4> 
    <?php
}

function vtmax_internals_options_callback () {
    ?>
    <h4><?php esc_attr_e('These options control internal functions within the plugin.', 'vtmax'); ?></h4>
    <?php  
}




function vtmax_before_checkout_products_callback() {   //opt2
	$options = get_option( 'vtmax_setup_options' );	
	$html = '<select id="before_checkout_products" name="vtmax_setup_options[show_error_before_checkout_products]">';
	$html .= '<option value="yes"' . selected( $options['show_error_before_checkout_products'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_error_before_checkout_products'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
  
	$more_info = __('More Info', 'vtmax');
  $html .= '<a id="help2" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
  
  $html .= '<p id="help2-text" class = "help-text" >'; 
  $help = __('"Show Error Messages Just Before Checkout Products List" => This is the standard place to show the error messages, just above the product list area.', 'vtmax'); 
  $html .= $help;
  $html .= '</p>';
    
	echo $html;
}

function vtmax_before_checkout_address_callback() {    //opt3
	$options = get_option( 'vtmax_setup_options' );	
	$html = '<select id="before_checkout_adress" name="vtmax_setup_options[show_error_before_checkout_address]">';
	$html .= '<option value="yes"' . selected( $options['show_error_before_checkout_address'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_error_before_checkout_address'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
  
	$more_info = __('More Info', 'vtmax');
  $html .= '<a id="help3" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
  
  $html .= '<p id="help3-text" class = "help-text" >'; 
  $help = __('"Show Error Messages Just Before Checkout Address Area" => This is the second 
  (duplicate) place to show error messages, just above the address area. It is particularly useful 
  if your checkout has multiple panes or pages, rather than a single full-display screen', 'vtmax'); 
  $html .= $help;
  $html .= '</p>';
  	
	echo $html;
}

function vtmax_rulecat_names_callback () {    //opt5
	$options = get_option( 'vtmax_setup_options' );	
	$html = '<select id="show_rulecat" name="vtmax_setup_options[show_rulecat_names_in_errmsg]">';
	$html .= '<option value="yes"' . selected( $options['show_rulecat_names_in_errmsg'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_rulecat_names_in_errmsg'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
	
	$more_info = __('More Info', 'vtmax');
  $html .= '<a id="help5" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
  
  $html .= '<p id="help5-text" class = "help-text" >'; 
  $help = __('"Show Maximum Purchase Rule Category Names in Error Message (Pro Only)" => 
  If you choose to use the group input search criteria option, and if you employ a Maximum Purchase Category to group the products, you can choose here 
  whether to include that Rule category name in any error messages produced.', 'vtmax'); 
  $html .= $help;
  $html .= '</p>'; 
  
	echo $html;
}


function vtmax_debugging_mode_callback () {    //opt8
	$options = get_option( 'vtmax_setup_options' );	
	$html = '<select id="debugging-mode" name="vtmax_setup_options[debugging_mode_on]">';
	$html .= '<option value="yes"' . selected( $options['debugging_mode_on'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['debugging_mode_on'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
	
	$more_info = __('More Info', 'vtmax');
  $html .= '<a id="help8" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
  
  $html .= '<p id="help8-text" class = "help-text" >'; 
  $help = __('"Test Debugging Mode Turned On" => 
  Set this to "yes" if you want to see the full rule structures which produce any error messages. **ONLY** should be used during testing.
  <br><br>NB => IF this switch is SET and the "purchase" button is depressed, the following warning may result:
  <br> "Warning: Cannot modify header information - headers already sent by" ... You will still have debug info available, however.
  ', 'vtmax'); 
  $html .= $help;
  $html .= '</p>';  
  
	echo $html;
}

function vtmax_prodcat_names_callback () {    //opt4
	$options = get_option( 'vtmax_setup_options' );	
	$html = '<select id="show_prodcat" name="vtmax_setup_options[show_prodcat_names_in_errmsg]">';
	$html .= '<option value="yes"' . selected( $options['show_prodcat_names_in_errmsg'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_prodcat_names_in_errmsg'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
	
	$more_info = __('More Info', 'vtmax');
  $html .= '<a id="help4" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';

  $html .= '<p id="help4-text" class = "help-text" >'; 
  $help = __('"Show Maximum Purchase Product Category Names in Error Message (Pro Only)" => 
  If you choose to use the group input search criteria option, and if you employ a Maximum Purchase Category to group the products, you can choose here 
  whether to include that Product category name in any error messages produced.', 'vtmax'); 
  $html .= $help;
  $html .= '</p>';  
  
	echo $html;
}
  
function vtmax_mult_rules_processing_callback() {   //opt7
	$options = get_option( 'vtmax_setup_options' );	
	$html = '<select id="before_checkout_products" name="vtmax_setup_options[apply_multiple_rules_to_product]">';
	$html .= '<option value="yes"' . selected( $options['apply_multiple_rules_to_product'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['apply_multiple_rules_to_product'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
  
	$more_info = __('More Info', 'vtmax');
  $html .= '<a id="help7" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
 
  $html .= '<p id="help7-text" class = "help-text" >'; 
  $help = __('"Apply More Than 1 Rule to Each Product" => Do we apply multiple maximum purchase rules to EACH product in the cart?  If not,
  we apply the FIRST rule we process which applies to a given product.  <strong>It is ***Strongly Suggested*** that this option be set to "NO", as otherwise the compounding error messages
  could be quite confusing for the ecommerce customer.</strong>', 'vtmax'); 
  $html .= $help;
  $html .= '</p>';
  
	echo $html;   
}
  
function vtmax_error_in_table_format_callback() {   //opt1
	$options = get_option( 'vtmax_setup_options' );	
	$html = '<select id="table_format" name="vtmax_setup_options[show_error_messages_in_table_form]">';
	$html .= '<option value="yes"' . selected( $options['show_error_messages_in_table_form'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_error_messages_in_table_form'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
  
	$more_info = __('More Info', 'vtmax');
  $html .= '<a id="help1" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';

  $html .= '<p id="help1-text" class = "help-text" >'; 
  $help = __('"Show Error Messages in Table Format" => Error messages can be shown in text or table format ("yes" = table format, "no" = text format).  If table format is desired,
  set this option to "yes". ', 'vtmax'); 
  $html .= $help;
  $html .= '</p>';
  
	echo $html;
}
/* v1.07
function vtmax_currency_sign_callback() {    //opt6
  $options = get_option( 'vtmax_setup_options' );
  $html = '<select id="currency_sign" name="vtmax_setup_options[use_this_currency_sign]">';
	$html .= '<option value="USD"' .  selected( $options['use_this_currency_sign'], 'USD', false) . '>$ &nbsp;&nbsp;(Dollar Sign) &nbsp;</option>';
  $html .= '<option value="EUR"' .  selected( $options['use_this_currency_sign'], 'EUR', false) . '>&euro; &nbsp;&nbsp;(Euro) &nbsp;</option>';
  $html .= '<option value="GBP"' .  selected( $options['use_this_currency_sign'], 'GBP', false) . '>&pound; &nbsp;&nbsp;(Pound Sterling) &nbsp;</option>';
  $html .= '<option value="JPY"' .  selected( $options['use_this_currency_sign'], 'JPY', false) . '>&yen; &nbsp;&nbsp;(Yen) &nbsp;</option>';
  $html .= '<option value="CZK"' .  selected( $options['use_this_currency_sign'], 'CZK', false) . '>&#75;&#269; &nbsp;&nbsp;(Czech Koruna) &nbsp;</option>';
  $html .= '<option value="DKK"' .  selected( $options['use_this_currency_sign'], 'DKK', false) . '>&#107;&#114; &nbsp;&nbsp;(Danish Krone) &nbsp;</option>';
  $html .= '<option value="HUF"' .  selected( $options['use_this_currency_sign'], 'HUF', false) . '>&#70;&#116; &nbsp;&nbsp;(Hungarian Forint) &nbsp;</option>';
  $html .= '<option value="ILS"' .  selected( $options['use_this_currency_sign'], 'ILS', false) . '>&#8362; &nbsp;&nbsp;(Israeli Shekel) &nbsp;</option>';
  $html .= '<option value="MYR"' .  selected( $options['use_this_currency_sign'], 'MYR', false) . '>&#82;&#77; &nbsp;&nbsp;(Malaysian Ringgits) &nbsp;</option>';
  $html .= '<option value="NOK"' .  selected( $options['use_this_currency_sign'], 'NOK', false) . '>&#107;&#114; &nbsp;&nbsp;(Norwegian Krone) &nbsp;</option>';
  $html .= '<option value="PHP"' .  selected( $options['use_this_currency_sign'], 'PHP', false) . '>&#8369; &nbsp;&nbsp;(Philippine Pesos) &nbsp;</option>';
  $html .= '<option value="PLN"' .  selected( $options['use_this_currency_sign'], 'PLN', false) . '>&#122;&#322; &nbsp;&nbsp;(Polish Zloty) &nbsp;</option>';
  $html .= '<option value="SEK"' .  selected( $options['use_this_currency_sign'], 'SEK', false) . '>&#107;&#114; &nbsp;&nbsp;(Swedish Krona) &nbsp;</option>';
  $html .= '<option value="CHF"' .  selected( $options['use_this_currency_sign'], 'CHF', false) . '>&#67;&#72;&#70; &nbsp;&nbsp;(Swiss Franc) &nbsp;</option>';
  $html .= '<option value="TWD"' .  selected( $options['use_this_currency_sign'], 'TWD', false) . '>&#78;&#84;&#36; &nbsp;&nbsp;(Taiwan New Dollars) &nbsp;</option>';
  $html .= '<option value="THB"' .  selected( $options['use_this_currency_sign'], 'THB', false) . '>&#3647; &nbsp;&nbsp;(Thai Baht) &nbsp;</option>';
  $html .= '<option value="TRY"' .  selected( $options['use_this_currency_sign'], 'TRY', false) . '>&#84;&#76; &nbsp;&nbsp;(Turkish Lira) &nbsp;</option>';
  $html .= '<option value="ZAR"' .  selected( $options['use_this_currency_sign'], 'ZAR', false) . '>&#82; &nbsp;&nbsp;(South African Rand) &nbsp;</option>';
  $html .= '<option value="RON"' .  selected( $options['use_this_currency_sign'], 'RON', false) . '>lei &nbsp;&nbsp;(Romanian Leu) &nbsp;</option>';
	$html .= '</select>';
  
  $more_info = __('More Info', 'vtmax');
  $html .= '<a id="help6" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
   
  $html .= '<p id="help6-text" class = "help-text" >'; 
  $help = __('"Select the Currncy Sign for Error Messages" => 
  This currency sign is used whend displaying Maximum Amount rule error messages. If the desired currency symbol is not available, please inform Varktech and 
  it will be added.', 'vtmax'); 
  $html .= $help;
  $html .= '</p>';  
  
	echo $html;
}
*/
function vtmax_custom_error_msg_css_at_checkout_callback() {    //opt9
  $options = get_option( 'vtmax_setup_options' );
  $html = '<textarea type="text" id="custom_error_msg_css_at_checkout"  rows="200" cols="40" name="vtmax_setup_options[custom_error_msg_css_at_checkout]">' . $options['custom_error_msg_css_at_checkout'] . '</textarea>';
  
  $more_info = __('More Info', 'vtmax');
  $html .= '<a id="help9" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
   
  $html .= '<p id="help9-text" class = "help-text" >'; 
  $help = __('"Custom Error Message CSS at Checkout Time" => 
  The CSS used for maximum amount error messages is supplied.  If you want to override any of the css, supply just your overrides here. <br>For Example => 
   div.vtmax-error .red-font-italic {color: green;}', 'vtmax'); 
  $html .= $help;
  $html .= '</p>';  
  
	echo $html;
}

  
function vtmax_rule_ID_in_errmsg_callback() {   //opt10
	$options = get_option( 'vtmax_setup_options' );	
	$html = '<select id="vtmax-id" name="vtmax_setup_options[show_rule_ID_in_errmsg]">';
	$html .= '<option value="yes"' . selected( $options['show_rule_ID_in_errmsg'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['show_rule_ID_in_errmsg'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
  
	$more_info = __('More Info', 'vtmax');
  $html .= '<a id="help10" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';

  $html .= '<p id="help10-text" class = "help-text" >'; 
  $help = __('"Show Rule ID in Error Message" => Append the Maximum Amount Rule ID (from the rule entry screen) at the end of
  an error message, to help identify what rule generated the message. ', 'vtmax'); 
  $html .= $help;
  $html .= '</p>';
  
	echo $html;
}


function vtmax_before_checkout_products_selector_callback() {    //opt11
  $options = get_option( 'vtmax_setup_options' );
  $html = '<textarea type="text" id="show_error_before_checkout_products_selector"  rows="1" cols="20" name="vtmax_setup_options[show_error_before_checkout_products_selector]">' . $options['show_error_before_checkout_products_selector'] . '</textarea>';
  
  $more_info = __('More Info', 'vtmax');
  $html .= '<a id="help11" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
   
  $html .= '<p id="help11-text" class = "help-text" >'; 
  $help = __('"Show Error Messages Just Before Checkout Products List - HTML Selector" => 
  <strong>This option controls the location of the message display, ***handle with care***.</strong>  For the Product area error message, this option supplies the ID  or Class HTML selector this message appears before.  This selector would appear in your theme"s checkout area,
  just above the products display area.  Be sure to include the "." or "#" selector identifier before the selector name. Default = "' .VTMAX_CHECKOUT_PRODUCTS_SELECTOR_BY_PARENT . '".  If you"ve changed this value and can"t get it to work, you can use the "reset to defaults" button (just below the "save changes" button) to get the value back (snapshot your other settings first to help you quickly set the other settings back the way to what you had before.)', 'vtmax'); 
  $html .= $help;
  $html .= '</p>';  
  
	echo $html;
}

function vtmax_before_checkout_address_selector_callback() {    //opt12
  $options = get_option( 'vtmax_setup_options' );
  $html = '<textarea type="text" id="show_error_before_checkout_address_selector"  rows="1" cols="20" name="vtmax_setup_options[show_error_before_checkout_address_selector]">' . $options['show_error_before_checkout_address_selector'] . '</textarea>';
  
  $more_info = __('More Info', 'vtmax');
  $html .= '<a id="help12" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';
   
  $html .= '<p id="help12-text" class = "help-text" >'; 
  $help = __('"Show Error Messages Just Before Checkout Address List - HTML Selector" => 
  <strong>This option controls the location of the message display, ***handle with care***.</strong>  For the Product area error message, this option supplies the ID  or Class HTML selector this message appears before.  This selector would appear in your theme"s checkout area,
  just above the address display area.  Be sure to include the "." or "#" selector identifier before the selector name. Default = "' .VTMAX_CHECKOUT_ADDRESS_SELECTOR_BY_PARENT . '".  If you"ve changed this value and can"t get it to work, you can use the "reset to defaults" button (just below the "save changes" button) to get the value back (snapshot your other settings first to help you quickly set the other settings back the way to what you had before.)', 'vtmax'); 
  $html .= $help;
  $html .= '</p>';  
  
	echo $html;
}

  
function vtmax_lifetime_limit_by_ip_callback () {   //opt13
	$options = get_option( 'vtmax_setup_options' );	
	$html = '<select id="vtmax-lifetime-limit-by-ip" name="vtmax_setup_options[max_purch_rule_lifetime_limit_by_ip]">';
	$html .= '<option value="yes"' . selected( $options['max_purch_rule_lifetime_limit_by_ip'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['max_purch_rule_lifetime_limit_by_ip'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
  
	$more_info = __('More Info', 'vtmax');
  $html .= '<a id="help13" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';

  $html .= '<p id="help13-text" class = "help-text" >'; 
  $help = __('"Check if a Customer has Rule Purchase History, by IP" => When using lifetime limits, use IP to identify the customer.', 'vtmax'); 
  $html .= $help;
  $html .= '</p>';  
	echo $html;
}
  
function vtmax_lifetime_limit_by_email_callback () {   //opt14
	$options = get_option( 'vtmax_setup_options' );	
	$html = '<select id="vtmax-lifetime-limit-by-email" name="vtmax_setup_options[max_purch_rule_lifetime_limit_by_email]">';
	$html .= '<option value="yes"' . selected( $options['max_purch_rule_lifetime_limit_by_email'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['max_purch_rule_lifetime_limit_by_email'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
  
	$more_info = __('More Info', 'vtmax');
  $html .= '<a id="help14" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';

  $html .= '<p id="help14-text" class = "help-text" >'; 
  $help = __('"Check if a Customer has Rule Purchase History, by Email" => When using lifetime limits, use email to identify the customer.', 'vtmax'); 
  $html .= $help;
  $html .= '</p>';  
	echo $html;
}
  
function vtmax_lifetime_limit_by_billto_name_callback () {   //opt15
  $options = get_option( 'vtmax_setup_options' );	
	$html = '<select id="vtmax-lifetime-limit-by-billto-name" name="vtmax_setup_options[max_purch_rule_lifetime_limit_by_billto_name]">';
	$html .= '<option value="yes"' . selected( $options['max_purch_rule_lifetime_limit_by_billto_name'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['max_purch_rule_lifetime_limit_by_billto_name'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
  
	$more_info = __('More Info', 'vtmax');
  $html .= '<a id="help15" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';

  $html .= '<p id="help15-text" class = "help-text" >'; 
  $help = __('"Check if a Customer has Rule Purchase History, by Billto Name" => When using lifetime limits, use billto name to identify the customer.', 'vtmax'); 
  $html .= $help;
  $html .= '</p>'; 
	echo $html;
}  

  
function vtmax_lifetime_limit_by_billto_addr_callback () {   //opt16
	$options = get_option( 'vtmax_setup_options' );	
	$html = '<select id="vtmax-lifetime-limit-by-billto-addr" name="vtmax_setup_options[max_purch_rule_lifetime_limit_by_billto_addr]">';
	$html .= '<option value="yes"' . selected( $options['max_purch_rule_lifetime_limit_by_billto_addr'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['max_purch_rule_lifetime_limit_by_billto_addr'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
  
	$more_info = __('More Info', 'vtmax');
  $html .= '<a id="help16" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';

  $html .= '<p id="help16-text" class = "help-text" >'; 
  $help = __('"Check if a Customer has Rule Purchase History, by Billto addr" => When using lifetime limits, use billto addr to identify the customer.', 'vtmax'); 
  $html .= $help;
  $html .= '</p>';  
	echo $html;
}
  
function vtmax_lifetime_limit_by_shipto_name_callback () {   //opt17
	$options = get_option( 'vtmax_setup_options' );	
	$html = '<select id="vtmax-lifetime-limit-by-shipto-name" name="vtmax_setup_options[max_purch_rule_lifetime_limit_by_shipto_name]">';
	$html .= '<option value="yes"' . selected( $options['max_purch_rule_lifetime_limit_by_shipto_name'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['max_purch_rule_lifetime_limit_by_shipto_name'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
  
	$more_info = __('More Info', 'vtmax');
  $html .= '<a id="help17" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';

  $html .= '<p id="help17-text" class = "help-text" >'; 
  $help = __('"Check if a Customer has Rule Purchase History, by Shipto Name" => When using lifetime limits, use shipto name to identify the customer.', 'vtmax'); 
  $html .= $help;
  $html .= '</p>';  
	echo $html;
}
  
function vtmax_lifetime_limit_by_shipto_addr_callback () {   //opt18
	$options = get_option( 'vtmax_setup_options' );	
	$html = '<select id="vtmax-lifetime-limit-by-shipto-addr" name="vtmax_setup_options[max_purch_rule_lifetime_limit_by_shipto_addr]">';
	$html .= '<option value="yes"' . selected( $options['max_purch_rule_lifetime_limit_by_shipto_addr'], 'yes', false) . '>Yes &nbsp;</option>';
	$html .= '<option value="no"'  . selected( $options['max_purch_rule_lifetime_limit_by_shipto_addr'], 'no', false) . '>No &nbsp;</option>';
	$html .= '</select>';
  
	$more_info = __('More Info', 'vtmax');
  $html .= '<a id="help18" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';

  $html .= '<p id="help18-text" class = "help-text" >'; 
  $help = __('"Check if a Customer has Rule Purchase History, by Shipto addr" => When using lifetime limits, use shipto addr to identify the customer.', 'vtmax'); 
  $html .= $help;
  $html .= '</p>';  
	echo $html;
}
/*  
function vtmax_checkout_forms_set_callback () {   //opt19 
  $options = get_option( 'vtmax_setup_options' );
  $html = '<textarea type="text" id="max_purch_checkout_forms_set"  rows="1" cols="20" name="vtmax_setup_options[max_purch_checkout_forms_set]">' . $options['max_purch_checkout_forms_set'] . '</textarea>';
                                      
  
	$more_info = __('More Info', 'vtmax');
  $html .= '<a id="help19" class="help-anchor" href="javascript:void(0);" >' ;  $html .= $more_info;   $html .= '</a>';

  $html .= '<p id="help19-text" class = "help-text" >'; 
  $help = __('"Default checkout formset containing "billingemail" etc, is formset "0".  Should you wish to create a custom formset to administer the basic addressing of "billingemail" etc,
  it must duplicate all the internals of the default formset (name column can contain any value, though).', 'vtmax'); 
  $html .= $help;
  $html .= '</p>'; 
  
 
	echo $html;
}
*/
function vtmax_validate_setup_input( $input ) {

  //did this come from on of the secondary buttons?
  $reset        = ( ! empty($input['options-reset']) ? true : false );
  $repair       = ( ! empty($input['rules-repair']) ? true : false );
  $nuke_rules   = ( ! empty($input['rules-nuke']) ? true : false );
  $nuke_cats    = ( ! empty($input['cats-nuke']) ? true : false );
  $nuke_hist    = ( ! empty($input['hist-nuke']) ? true : false );
 
  
  switch( true ) { 
    case $reset        === true :    //reset options
        $output = $this->vtmax_get_default_options();  //load up the defaults
        //as default options are set, no further action, just return
        return apply_filters( 'vtmax_validate_setup_input', $output, $input );
      break;
    case $repair       === true :    //repair rules
        $vtmax_nuke = new VTMAX_Rule_delete;            
        $vtmax_nuke->vtmax_repair_all_rules();
        $output = get_option( 'vtmax_setup_options' );  //fix 2-13-2013 - initialize output, otherwise all Options go away...
      break;
    case $nuke_rules   === true :
        $vtmax_nuke = new VTMAX_Rule_delete;            
        $vtmax_nuke->vtmax_nuke_all_rules();
        $output = get_option( 'vtmax_setup_options' );  //fix 2-13-2013 - initialize output, otherwise all Options go away...
      break;
    case $nuke_cats    === true :    
        $vtmax_nuke = new VTMAX_Rule_delete;            
        $vtmax_nuke->vtmax_nuke_all_rule_cats();
        $output = get_option( 'vtmax_setup_options' );  //fix 2-13-2013 - initialize output, otherwise all Options go away...
      break;
    case $nuke_hist    === true :    
        $vtmax_nuke = new VTMAX_Rule_delete;            
        $vtmax_nuke->vtmax_nuke_max_purchase_history();
        $output = get_option( 'vtmax_setup_options' );  //fix 2-13-2013 - initialize output, otherwise all Options go away...
      break;
    default:   //standard update button hit...                 
        //$output = array();
        $output = get_option( 'vtmax_setup_options' );  //v1.06
      	foreach( $input as $key => $value ) {
      		if( isset( $input[$key] ) ) {
      			$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );	
      		} // end if		
      	} // end foreach        
      break;
  }
   
   /* alternative to add_settings_error
        $message =  __('<strong>Please Download and/or Activate ' .$free_plugin_name.' (the Free version). </strong><br>It must be installed and active, before the Pro version can be activated.  The Free version can be downloaded from '  . $free_plugin_download , 'vtmaxpro');
        $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
        add_action( 'admin_notices', create_function( '', "echo '$admin_notices';" ) );
   */
  
   if(defined('VTMAX_PRO_DIRNAME')) { 
     //one of these switches must be on
     if ( ($input['max_purch_rule_lifetime_limit_by_ip'] == 'no' ) &&
          ($input['max_purch_rule_lifetime_limit_by_email'] == 'no' ) &&
          ($input['max_purch_rule_lifetime_limit_by_billto_name'] == 'no' ) &&
          ($input['max_purch_rule_lifetime_limit_by_billto_addr'] == 'no' ) &&
          ($input['max_purch_rule_lifetime_limit_by_shipto_name'] == 'no' ) &&
          ($input['max_purch_rule_lifetime_limit_by_shipto_addr'] == 'no' ) ) {
        $admin_errorMsg = __(' One of the following switches must also be set to "yes":
           <br> "Check if a Customer has Rule Purchase History, by IP"
           <br> "Check if a Customer has Rule Purchase History, by Email"
           <br> "Check if a Customer has Rule Purchase History, by BillTo Name"
           <br> "Check if a Customer has Rule Purchase History, by BillTo Address"
           <br> "Check if a Customer has Rule Purchase History, by ShipTo Name"
           <br> "Check if a Customer has Rule Purchase History, by ShipTo Address"
        ', 'vtmax');
        add_settings_error( 'VTMAX Options', 'Use Max Purchase Rule Lifetime Customer Limit', $admin_errorMsg , 'error' );  
     }
    }
             
    if ( ($input['show_error_before_checkout_products'] == 'no' ) &&
         ($input['show_error_before_checkout_address']  == 'no' ) ) {
        $admin_errorMsg = __(' One of the following two switches must also be set to "yes":
           <br> "Show Error Messages Just Before Checkout Products List"
           <br> "Show 2nd Set of Error Messages at Checkout Address Area"
        ', 'vtmax');
        add_settings_error( 'VTMAX Options', 'Show Error Messages', $admin_errorMsg , 'error' );  
     } 
     
     if ( ($input['show_error_before_checkout_products'] == 'yes' ) &&
          ($input['show_error_before_checkout_products_selector']  <= ' ' ) ) {
        $admin_errorMsg = __(' If "Show Error Messages Just Before Checkout Products List" = "yes",
           <br> "Show Error Messages Just Before Checkout Products List - HTML Selector" must be filled in.', 'vtmax');
        add_settings_error( 'VTMAX Options', 'Show Error Messages', $admin_errorMsg , 'error' );  
     } 
     
     if ( ($input['show_error_before_checkout_address'] == 'yes' ) &&
          ($input['show_error_before_checkout_address_selector']  <= ' ' ) ) {
        $admin_errorMsg = __(' "Show 2nd Set of Error Messages at Checkout Address Area" = "yes",
           <br> "Show Error Messages Just Before Checkout Address List - HTML Selector" must be filled in.', 'vtmax');
        add_settings_error( 'VTMAX Options', 'Show Error Messages', $admin_errorMsg , 'error' );  
     } 

 
  //NO Object-based code on the apply_filters statement needed or wanted!!!!!!!!!!!!!
  return apply_filters( 'vtmax_validate_setup_input', $output, $input );                       
} 


} //end class
 $vtmax_setup_plugin_options = new VTMAX_Setup_Plugin_Options;
  