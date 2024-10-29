<?php
class amzafs_MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_init', array( $this, 'page_init' ) );		
		add_action( 'admin_menu', array( $this, 'settings_register_menu' ) );	
    }

    /**
     * Add submenu page
     */
	public function settings_register_menu() {
		add_submenu_page( 'edit.php?post_type=amz_product', 'Settings', 'Settings', 'manage_options', 'amz_shortcodes_settings', array( $this, 'create_admin_page' ));
	}

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'amz_short_options' );
        ?>
        <div class="wrap">
            <h1>Amazon Shortcodes Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'my-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'my_option_group', // Option group
            'amz_short_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );
        add_settings_section(
            'setting_section_id', // ID
            'Amazon Product Advertising API 5.0 Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );  
        add_settings_field(
            'access_key', // ID
            'Amazon Affiliate Access Key *', // Title 
            array( $this, 'access_key_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );      
        add_settings_field(
            'secret_key', 
            'Amazon Affiliate Secret Key *', 
            array( $this, 'title_callback' ), 
            'my-setting-admin', 
            'setting_section_id'
        );      		
        add_settings_field(
            'asoc_tag', // ID
            'Default Associate Tag *', // Title 
            array( $this, 'tag_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );
		add_settings_field( 
			'select_field_locale', 
			'Advertising API locale *', 
			'amzafs_Dropdown_locale_select_field_render', 
			'my-setting-admin', 
			'setting_section_id' 
		);		
        add_settings_field(
            'update_time', // ID
            'Update time (seconds)', // Title 
            array( $this, 'update_time_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );	
        add_settings_field(
            'product_number', // ID
            'Product number (max 10)', // Title 
            array( $this, 'product_number_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );
		add_settings_field( 
			'select_field_0', 
			'Delete all products from category when updating?', 
			'amzafs_Dropdown_select_field_render', 
			'my-setting-admin', 
			'setting_section_id' 
		);
        add_settings_field(
            'amz_title_length', // ID
            'Title Length (chars)', // Title 
            array( $this, 'update_titlelength_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );
        add_settings_field(
            'amz_description_length', // ID
            'Description Length (chars)', // Title 
            array( $this, 'update_deslength_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );
        add_settings_field(
            'amz_button_label', // ID
            'Button Label', // Title 
            array( $this, 'update_buttonlabel_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section           
        );
		add_settings_field( 
			'select_layout', 
			'Product Layout', 
			'amzafs_Dropdown_select_layout_render', 
			'my-setting-admin', 
			'setting_section_id' 
		);
		add_settings_field( 
			'select_price_yesno', 
			'Show Price?', 
			'amzafs_Dropdown_select_field_yesno_render', 
			'my-setting-admin', 
			'setting_section_id' 
		);
		add_settings_field( 
			'select_wholeblock_yesno', 
			'Link whole product block?', 
			'amzafs_Dropdown_select_field_wholeblock_yesno_render', 
			'my-setting-admin', 
			'setting_section_id' 
		);			
    }
	
    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['access_key'] ) )
			$new_input['access_key'] = sanitize_text_field( $input['access_key'] );

        if( isset( $input['secret_key'] ) )
            $new_input['secret_key'] = sanitize_text_field( $input['secret_key'] );
		
		if( isset( $input['asoc_tag'] ) )
            $new_input['asoc_tag'] = sanitize_text_field( $input['asoc_tag'] );
		
		if( isset( $input['update_time'] ) )
            $new_input['update_time'] = sanitize_text_field( $input['update_time'] );		

		if( isset( $input['product_number'] ) )
            $new_input['product_number'] = sanitize_text_field( $input['product_number'] );

		if( isset( $input['select_field_0'] ) )
            $new_input['select_field_0'] = sanitize_text_field( $input['select_field_0'] );		
		
		if( isset( $input['select_field_locale'] ) )
            $new_input['select_field_locale'] = sanitize_text_field( $input['select_field_locale'] );	

		if( isset( $input['amz_description_length'] ) )
            $new_input['amz_description_length'] = sanitize_text_field( $input['amz_description_length'] );
		
		if( isset( $input['amz_button_label'] ) )
            $new_input['amz_button_label'] = sanitize_text_field( $input['amz_button_label'] );		
						
		if( isset( $input['amz_title_length'] ) )
            $new_input['amz_title_length'] = sanitize_text_field( $input['amz_title_length'] );
		
		if( isset( $input['select_layout'] ) )
            $new_input['select_layout'] = sanitize_text_field( $input['select_layout'] );		

		if( isset( $input['select_price_yesno'] ) )
            $new_input['select_price_yesno'] = sanitize_text_field( $input['select_price_yesno'] );

		if( isset( $input['select_wholeblock_yesno'] ) )
            $new_input['select_wholeblock_yesno'] = sanitize_text_field( $input['select_wholeblock_yesno'] );

        return $new_input;
    }
	
	

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function access_key_callback()
    {
        printf(
            '<input type="text" id="access_key" placeholder="Your access key" name="amz_short_options[access_key]" value="%s" /> <p>Your Amazon Associates Access Key ID.</p>',
            isset( $this->options['access_key'] ) ? esc_attr( $this->options['access_key']) : ''
        );
    }

    public function tag_callback()
    {
        printf(
            '<input type="text" id="asoc_tag" placeholder="Your associate tag" name="amz_short_options[asoc_tag]" value="%s" /> <p>Your Amazon Associates Tag.</p>',
            isset( $this->options['asoc_tag'] ) ? esc_attr( $this->options['asoc_tag']) : ''
        );
    }	
	
	
	
    public function update_time_callback()
    {
        printf(
            '<input type="number" min="0" max="999999999999" id="update_time" name="amz_short_options[update_time]" value="%s" /><p>Default: <strong>604800</strong> seconds (1 week).</p><p>The plugin will update the products if they are older than this setting. 0 means never update.</p>',
            isset( $this->options['update_time'] ) ? esc_attr( $this->options['update_time']) : ''
        );
    }


    public function product_number_callback()
    {
        printf(
            '<input type="number" min="1" max="10" id="product_number" name="amz_short_options[product_number]" value="%s" /><p>Default: <strong>10</strong> products.</p><p>The products that will be saved in every search.</p>',
            isset( $this->options['product_number'] ) ? esc_attr( $this->options['product_number']) : ''
        );
    }	

    public function update_titlelength_callback()
    {
        printf(
            '<input type="number" min="0" max="9999" id="amz_title_length" name="amz_short_options[amz_title_length]" value="%s" /><p>Default: <strong>60</strong> chars.</p><p>The number of characters in the product title. Set 0 to hide the title.</p>',
            isset( $this->options['amz_title_length'] ) ? esc_attr( $this->options['amz_title_length']) : ''
        );
    }

	
    public function update_deslength_callback()
    {
        printf(
            '<input type="number" min="0" max="9999" id="amz_description_length" name="amz_short_options[amz_description_length]" value="%s" /><p>Default: <strong>120</strong> chars.</p><p>The number of words in the product description. Set 0 to hide the description.</p>',
            isset( $this->options['amz_description_length'] ) ? esc_attr( $this->options['amz_description_length']) : ''
        );
    }	
	


    public function update_buttonlabel_callback()
    {
		global $premiumURL;
        printf(
			/*PREMIUM VERSION*/
            //'<input type="text" id="amz_button_label" name="amz_short_options[amz_button_label]" value="%s" /><p>Set the label of the product button.</p>',
			/*FREE VERSION*/
			'<input type="text" id="amz_button_label" name="amz_short_options[amz_button_label]" value="%s" disabled/><p>Set the label of the product button. <a target=\'_blank\' href=\''.$premiumURL.'\'>Purchase premium version</a> to unlock it and make me happy!</p>',
            isset( $this->options['amz_button_label'] ) ? esc_attr( $this->options['amz_button_label']) : ''
        );
    }




    /** 
     * Get the settings option array and print one of its values
     */
    public function title_callback()
    {
        printf(
            '<input type="text" id="secret_key" placeholder="Your secret key" name="amz_short_options[secret_key]" value="%s" /> <br>  <p>Your Amazon Associates Secret Key ID.</p>',
            isset( $this->options['secret_key'] ) ? esc_attr( $this->options['secret_key']) : ''
        );
    }
}

if( is_admin() )
		$my_settings_page = new amzafs_MySettingsPage();


	
function amzafs_Dropdown_select_field_render(  ) { 
    $options = get_option( 'amz_short_options' );
	$opcionBuena=$options["select_field_0"];
    ?>
    <select name='amz_short_options[select_field_0]'>
        <option value='1' <?php selected( $opcionBuena, 1 ); ?>>Yes</option>
        <option value='2' <?php selected( $opcionBuena, 2 ); ?>>No</option>
    </select> <p>Default: <strong>Yes</strong>.</p><p>It will delete old products from a shortcode when updating them using Amazon API.</p>
	<?php
	}
	
function amzafs_Dropdown_select_field_yesno_render(  ) { 
    $options = get_option( 'amz_short_options' );
	$opcionYesNo=$options["select_price_yesno"];
    ?>
    <select name='amz_short_options[select_price_yesno]'>
        <option value='1' <?php selected( $opcionYesNo, 1 ); ?>>Yes</option>
        <option value='2' <?php selected( $opcionYesNo, 2 ); ?>>No</option>
    </select> <p>Default: <strong>No</strong>.</p><p>It will show or hide the price in the products frontend.</p>
	<?php
	}	





/*PREMIUM VERSION*/
/*
function amzafs_Dropdown_select_field_wholeblock_yesno_render(  ) { 
    $options = get_option( 'amz_short_options' );
	$opcionLinkYesNo=$options["select_wholeblock_yesno"];
    ?>
    <select name='amz_short_options[select_wholeblock_yesno]'>
        <option value='2' <?php selected( $opcionLinkYesNo, 2 ); ?>>No</option>
		<option value='1' <?php selected( $opcionLinkYesNo, 1 ); ?>>Yes</option>
    </select> <p>It will link the whole product block increasing clicks to Amazon store.</p>
	<?php
	}
*/

/*FREE VERSION*/
function amzafs_Dropdown_select_field_wholeblock_yesno_render(  ) {
	global $premiumURL;	
    $options = get_option( 'amz_short_options' );
	$opcionLinkYesNo=$options["select_wholeblock_yesno"];
    ?>
    <select name='amz_short_options[select_wholeblock_yesno]' disabled>
        <option value='2' <?php selected( $opcionLinkYesNo, 2 ); ?>>No</option>
    </select> <p>It will link the whole product block increasing clicks to Amazon store. <a target='_blank' href='<?php echo $premiumURL; ?>'>Purchase premium version</a> to unlock it and make me happy!</p>
	<?php
	}


	

function amzafs_Dropdown_settings_section_callback(  ) { 
		echo __( 'This section description', 'dropdown' );
	}





	
function amzafs_Dropdown_locale_select_field_render(  ) { 
    $options = get_option( 'amz_short_options' );
	$opcionBuena=$options["select_field_locale"];
    ?>
    <select name='amz_short_options[select_field_locale]'>
        <option value='us' <?php selected( $opcionBuena, 'us' ); ?>>US</option>
        <option value='uk' <?php selected( $opcionBuena, 'uk' ); ?>>UK</option>
		<option value='es' <?php selected( $opcionBuena, 'es' ); ?>>ES</option>
		<option value='de' <?php selected( $opcionBuena, 'de' ); ?>>DE</option>
		<option value='jp' <?php selected( $opcionBuena, 'jp' ); ?>>JP</option>
		<option value='fr' <?php selected( $opcionBuena, 'fr' ); ?>>FR</option>
		<option value='it' <?php selected( $opcionBuena, 'it' ); ?>>IT</option>
		<option value='ca' <?php selected( $opcionBuena, 'ca' ); ?>>CA</option>
		<option value='br' <?php selected( $opcionBuena, 'br' ); ?>>BR</option>
		<option value='in' <?php selected( $opcionBuena, 'in' ); ?>>IN</option>
		<option value='mx' <?php selected( $opcionBuena, 'mx' ); ?>>MX</option>
		<option value='au' <?php selected( $opcionBuena, 'au' ); ?>>AU</option>	
		<option value='cn' <?php selected( $opcionBuena, 'cn' ); ?>>CN</option>
		<option value='sg' <?php selected( $opcionBuena, 'sg' ); ?>>SG</option>
		<option value='tr' <?php selected( $opcionBuena, 'tr' ); ?>>TR</option>
		<option value='ae' <?php selected( $opcionBuena, 'ae' ); ?>>AE</option>		
    </select> <p>The locale of Amazon Advertising API. Each locale requires a separate registration.</p>
	<?php
	}




function amzafs_Dropdown_select_layout_render(  ) { 
    $options = get_option( 'amz_short_options' );
	$opcionBuena=$options["select_layout"];
    ?>
    <select name='amz_short_options[select_layout]'>
        <option value='1' <?php selected( $opcionBuena, 1 ); ?>>Grid</option>
        <option value='2' <?php selected( $opcionBuena, 2 ); ?>>Row</option>
    </select> <p>Select between grid and row layout.</p>
	<?php
	}
