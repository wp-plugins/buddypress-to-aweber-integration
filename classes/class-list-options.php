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
class ListOptions{

  /**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = "1.0.0";


  public $options           = '';

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

    require_once(dirname(__FILE__)  . '/../includes/aweber_api/aweber_api.php');

    $this->options            = get_option('aweber_list_options' );
    $this->accessToken        = get_option('accessToken');
    $this->accessTokenSecret  = get_option('accessTokenSecret');

    //add_action('admin_init', array($this, 'check_options'), 10 );
    add_action( 'admin_init', array($this, 'aweber_list_settings' ), 20 );

    $options = get_option('aweber_buddypress_options');

    $consumerKey    = $options['consumerkey'];
    $consumerSecret = $options['consumersecret'];

    $this->aweber = new AWeberAPI($consumerKey, $consumerSecret);

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
  public function check_options(){

    if(isset($_COOKIE['accessToken']) && isset($_COOKIE['accessTokenSecret'])){

     update_option('accessToken', $_COOKIE['accessToken']);
     update_option('accessTokenSecret', $_COOKIE['accessTokenSecret']);
     $accessToken        = get_option('accessToken');
     $accessTokenSecret  = get_option('accessTokenSecret');

   }else{
     $accessToken        = get_option('accessToken');
     $accessTokenSecret  = get_option('accessTokenSecret');
   }

    if( isset($accessToken)  && isset($accessTokenSecret) && $accessToken != NULL && $accessTokenSecret != NULL ){

      add_action( 'admin_init', array($this, 'aweber_list_settings' ), 20 );



    }

  }
  /**
   * Settings init
   */
   public function aweber_list_settings(){

     //Default Aweber api settings
       register_setting(
           'aweber_list_options_group', // Option group
           'aweber_list_options', // Option name
           array( $this, 'sanitize' ) // Sanitize
       );

       add_settings_section(
           'list_section', // section
           'Lists', // Title
           array( $this, 'print_section_info' ), // Callback
           'list_options_page' // Page
       );

       add_settings_field(
           'listid', // ID
           'Pick a list:', // Title
           array( $this, 'listid_callback' ), // Callback
           'list_options_page', // Page
           'list_section' // Section
       );
   }
   /**
    * Sanitize each setting field as needed
    *
    * @param array $input Contains all settings fields as array keys
    */
   public function sanitize( $input ){
       $new_input = array();
        /*var_dump($input);
        die();*/
         if( isset( $input['listid'] ) )
             $new_input['listid'] =  $input['listid'];
         return $new_input;
   }
   /**
    * callback fundtion
    */
   public function print_section_info(){
       print 'Your list settings:';
   }

   /**
    * Get aweber lists from the api
    */
   public function listid_callback(){


     $account = $this->aweber->getAccount(get_option('accessToken'), get_option('accessTokenSecret'));
     $html = '<select id="listid" name="aweber_list_options[listid]">';
     $html .= '<option value="default">Select a list...</option>';
      foreach($account->lists as $offset => $list) {
         $listuniqueid = $list->data['name'];

         $html .= "<option value='$listuniqueid'" . selected( $this->options['listid'], $list->data['name'], false) . ">$list->name</option>";

       }
     $html .= '</select>';

     echo $html;


   }

}
