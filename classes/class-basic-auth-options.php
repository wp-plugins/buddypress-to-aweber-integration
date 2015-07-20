<?php

/**
 * Buddypress to Aweber
 *
 * @package   buddypress-to-aweber
 * @author    vimes1984 <churchill.c.j@gmail.com>
 * @license   GPL-2.0+
 * @link      http://buildawebdoctor.com
 * @copyright 5-15-2015 BAWD
 */

/**
 * Buddypress to Aweber class.
 *
 * @package BuddypressToAweber
 * @author  vimes1984 <churchill.c.j@gmail.com>
 */
class BasicAuthOptions{
	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = "1.0.0";


  public $options           = '';
  public $accessToken       = '';
  public $accessTokenSecret = '';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = "buddypress-to-aweber";

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

    add_action( 'admin_init', array($this, 'aweber_settings_main' ) );
    add_action( 'init', array($this, 'set_cookies' ) );

    $this->options            = get_option( 'aweber_buddypress_options' );
    $this->accessToken        = get_option('accessToken');
    $this->accessTokenSecret  = get_option('accessTokenSecret');

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn"t been set, set it now.
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}
  /**
   *Set aweber cookies and auth stuff
   */
   public function set_cookies(){
     // Replace with the keys of your application
     // NEVER SHARE OR DISTRIBUTE YOUR APPLICATIONS'S KEYS!
     $options = get_option( 'aweber_buddypress_options' );

     if(isset($options['consumersecret']) && isset($options['consumerkey'])  && $options['consumersecret'] != NULL && $options['consumerkey'] != NULL && (!isset($this->accessToken) || $this->accessToken == NULL) && (!isset($this->accessTokenSecret) || $this->accessTokenSecret == NULL) ){

     require_once(dirname(__FILE__)  . '/../includes/aweber_api/aweber_api.php');
     $consumerKey    = $options['consumerkey'];
     $consumerSecret = $options['consumersecret'];
     $aweber = new AWeberAPI($consumerKey, $consumerSecret);

       if (empty($_COOKIE['accessToken'])) {
           if (empty($_GET['oauth_token'])) {
               $callbackUrl = 'http://'.$_SERVER['HTTP_HOST']. str_replace('&tab=settings', '&tab=fields', $_SERVER['REQUEST_URI']);
               list($requestToken, $requestTokenSecret) = $aweber->getRequestToken($callbackUrl);
               setcookie('requestTokenSecret', $requestTokenSecret);
               setcookie('callbackUrl', $callbackUrl);
               wp_redirect($aweber->getAuthorizeUrl());
               exit();
           }

           $aweber->user->tokenSecret = $_COOKIE['requestTokenSecret'];
           $aweber->user->requestToken = $_GET['oauth_token'];
           $aweber->user->verifier = $_GET['oauth_verifier'];
           list($accessToken, $accessTokenSecret) = $aweber->getAccessToken();
           setcookie('accessToken', $accessToken);
           setcookie('accessTokenSecret', $accessTokenSecret);

           wp_redirect($_COOKIE['callbackUrl'] );
           exit();
       }



     }

   }
  /**
   * Settings init
   */
   public function aweber_settings_main(){

     //Default Aweber api settings
       register_setting(
           'buddypress_to_aweber_group', // Option group
           'aweber_buddypress_options', // Option name
           array( $this, 'sanitize' ) // Sanitize
       );

       add_settings_section(
           'setting_section_id', // ID
           'Aweber API Settings', // Title
           array( $this, 'print_section_info' ), // Callback
           'buddypress-to-aweber' // Page
       );

       add_settings_field(
           'consumerkey', // ID
           'Consumer Key:', // Title
           array( $this, 'consumerkey_callback' ), // Callback
           'buddypress-to-aweber', // Page
           'setting_section_id' // Section
       );
       add_settings_field(
           'consumersecret', // ID
           'Consumer Secret:', // Title
           array( $this, 'consumersecret_callback' ), // Callback
           'buddypress-to-aweber', // Page
           'setting_section_id' // Section
       );



       //These should be generated on the callback from aweber
       add_settings_section(
           'access_tokens_section', // ID
           'Aweber Tokens:', // Title
           array( $this, 'access_token_info' ), // Callback
           'access_tokens' // Page
       );

       add_settings_field(
           'accessToken', // ID
           'Access Token:', // Title
           array( $this, 'accessToken_callback' ), // Callback
           'access_tokens', // Page
           'access_tokens_section' // Section
       );
       add_settings_field(
           'accessTokenSecret', // ID
           'Access Token Secret:', // Title
           array( $this, 'accessTokenSecret_callback' ), // Callback
           'access_tokens', // Page
           'access_tokens_section' // Section
       );

       //Clear setting
       add_settings_section(
           'setting_clear_section', // ID
           '', // Title
           array( $this, 'print_clearall_warn' ), // Callback
           'setting_clear' // Page
       );

       add_settings_field(
           'clearall', // ID
           '', // Title
           array( $this, 'clearall_callback' ), // Callback
           'setting_clear', // Page
           'setting_clear_section' // Section
       );
   }

   /**
    * Sanitize each setting field as needed
    *
    * @param array $input Contains all settings fields as array keys
    */
   public function sanitize( $input ){
       $new_input = array();

       if($input['clearall'] === 'true'){
         if( isset( $input['consumerkey'] ) )
             $new_input['consumerkey'] =  NULL;
         if( isset( $input['consumersecret'] ) )
             $new_input['consumersecret'] = NULL;

             unset($_COOKIE['mycookiename']);
             setcookie('requestTokenSecret', "", time()-3600);
             setcookie('callbackUrl', "", time()-3600);
             setcookie('accessToken', "", time()-3600);
             setcookie('accessTokenSecret', "", time()-3600);
             update_option('accessToken', NULL);
						 update_option('accessTokenSecret', NULL);
             update_option('mappingfieldarray', false);
						 update_option('aweber_list_options', false );

         return $new_input;

       }else{
         if( isset( $input['consumerkey'] ) )
             $new_input['consumerkey'] =  $input['consumerkey'];
         if( isset( $input['consumersecret'] ) )
             $new_input['consumersecret'] = $input['consumersecret'];
        if( isset( $input['consumersecret'] ) )
                 $new_input['consumersecret'] = $input['consumersecret'];
         return $new_input;

       }
   }
   /**
    * callback fundtion
    */
   public function print_section_info(){
       print 'Aweber API setting below :';
   }
    public function print_clearall_warn(){
      print '';
    }
    public function access_token_info(){
      print 'These should be generated automatically after you Authenticate aweber.';
    }
   /**
    *
    */
   public function clearall_callback(){
     printf( '<input type="hidden" id="clearall" name="aweber_buddypress_options[clearall]" value="%s" />', 'true');
   }
   /**
    * Get the settings option array and print one of its values
    */
   public function consumerkey_callback(){

       printf(
           '<input type="text" id="consumerkey" name="aweber_buddypress_options[consumerkey]" value="%s" />',
           isset( $this->options['consumerkey'] ) ? esc_attr( $this->options['consumerkey']) : ''
       );
   }
   public function consumersecret_callback(){
       printf(
           '<input type="text" id="consumersecret" name="aweber_buddypress_options[consumersecret]" value="%s" />',
           isset( $this->options['consumersecret'] ) ? esc_attr( $this->options['consumersecret']) : ''
       );
   }
   public function accessTokenSecret_callback(){

     printf(
         '<input type="text" disabled id="accessTokenSecret" name="aweber_buddypress_options[accessTokenSecret]" value="%s" />',
         isset( $this->accessTokenSecret ) ? esc_attr( $this->accessTokenSecret) : ''
     );
   }
   public function accessToken_callback(){
     printf(
         '<input type="text" disabled id="accessToken" name="aweber_buddypress_options[accessToken]" value="%s" />',
         isset( $this->accessToken ) ? esc_attr( $this->accessToken) : ''
     );
   }


}
