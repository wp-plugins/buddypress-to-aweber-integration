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
class AjaxFunctions{

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
    //these ajax calls are enqueed in here so as not to conflict if the user hasn't authenticated with aweber yet
    add_action('admin_init', array($this, 'check_options'), 10 );

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

     $accessToken        = get_option('accessToken');
     $accessTokenSecret  = get_option('accessTokenSecret');
       if( isset($accessToken)  && isset($accessTokenSecret) && $accessToken != NULL && $accessTokenSecret != NULL ){
         $options = get_option('aweber_buddypress_options');

         $consumerKey    = $options['consumerkey'];
         $consumerSecret = $options['consumersecret'];

         $this->aweber = new AWeberAPI($consumerKey, $consumerSecret);

         add_action( 'wp_ajax_get_list', array($this,  'get_list') );
         add_action('wp_ajax_nopriv_get_list', array($this, 'get_list') );

         add_action( 'wp_ajax_get_bpress_profile', array($this,  'get_bpress_profile') );
         add_action('wp_ajax_nopriv_get_bpress_profile', array($this, 'get_bpress_profile') );

         add_action( 'wp_ajax_get_profile_options', array($this,  'get_profile_options') );
         add_action('wp_ajax_nopriv_get_profile_options', array($this, 'get_profile_options') );

         add_action( 'wp_ajax_get_profile_meta', array($this,  'get_profile_meta') );
         add_action('wp_ajax_nopriv_get_profile_meta', array($this, 'get_profile_meta') );

         add_action( 'wp_ajax_set_field_mapping', array($this,  'set_field_mapping') );
         add_action('wp_ajax_nopriv_set_field_mapping', array($this, 'set_field_mapping') );

       }

  }
  public function set_field_mapping(){
    $request_body = file_get_contents( 'php://input' );
    $decodeit     = json_decode( $request_body );

    update_option('mappingfieldarray', $decodeit );

    $json        = get_option('mappingfieldarray');

    echo json_encode($json);
    die(); // this is required to return a proper result
  }
  /**
   * get the wordpress meta fields for the user maybe someone wants to map this..
   */
   public function get_profile_meta(){
     //Variables
     $user_ID   =  get_current_user_id();
     $json      = array();
     $i         = 0;
     $user_meta = get_user_meta($user_ID);

      //parse the user meta values
      foreach ($user_meta as $key => $value) {
        # code...

        $json[$i]               =   new stdClass;
        $json[$i]->name         =   $key;
        $json[$i]->id           =   $key;
        $json[$i]->group_id     =   'user_meta';
        $json[$i]->from         =   'meta';
        $json[$i]->type         =   'unknown';
        $json[$i]->buddypress   =   false;

        $i++;
      }
     echo json_encode($json);
     die(); // this is required to return a proper result
   }
  /**
   * set up the wordpress options array
   */
  public function get_profile_options(){
    //Default data
    $currentuserid          =   get_current_user_id();
    $userdata               = get_userdata($currentuserid);

    $i = 0;
    foreach($userdata->data as $key => $value){

      $json[$i]               =   new stdClass;
      $json[$i]->name         =   $key;
      $json[$i]->id           =   $key;
      $json[$i]->group_id     =   'user_options';
      $json[$i]->type         =   'text';
      $json[$i]->from         =   'options';
      $json[$i]->buddypress   =   false;
      $i++;
    }
    echo json_encode($json);
    die(); // this is required to return a proper result

  }
  /**
   * Get buddypress profile fields as a json array.
   */
  public function get_bpress_profile(){
     global $bp;
     //Variables
     $json      = array();
     $i         = 0;
     //Buddypress profile field data
        if ( bp_is_active( 'xprofile' ) ) :
          if ( bp_has_profile( array( 'fetch_field_data' => false ) ) ) :
            while ( bp_profile_groups() ) : bp_the_profile_group();
              while ( bp_profile_fields() ) : bp_the_profile_field();
                    // Get buddypress profile fields
                  $fieldID  = bp_get_the_profile_field_id();
                  $field    = new BP_XProfile_Field( $fieldID );
                  //pass the data through to our return array
                  $json[$i]               =   new stdClass;
                  $json[$i]->name         =   $field->name;
                  $json[$i]->id           =   $field->id;
                  $json[$i]->group_id     =   $field->group_id;
                  $json[$i]->from         =   'buddypress';
                  $json[$i]->type         =   $field->type;
                  $json[$i]->buddypress   =   true;
                  $i++;

                  /*
                      $json[$i]->type_obj     =   $field->type_obj;
                      ALL data causes recursion error
                      $json[$i]->all    =   $field;
                  */

              endwhile;
            endwhile;
          endif;
        endif;

     echo json_encode($json);
     die(); // this is required to return a proper result
  }
  /**
   * Ajax function get list
   */
   public function get_list(){
     $listid = $this->options;
     //Check to make sure we don't have the default value
     if($listid['listid'] != 'default' && $listid['listid'] != false){

       try {
           $account = $this->aweber->getAccount(get_option('accessToken'), get_option('accessTokenSecret'));
           $lists = $account->lists->find(array('name' => $listid['listid']));

           if(count($lists)) {
             $list = $lists[0];
             $custom_fields = $list->custom_fields->data['entries'];

             $custom_fields[] = array(
                    'http_etag'                 => '',
                    'id'                        => 'name',
                    'is_subscriber_updateable'  => true,
                    'name'                      => 'Name',
                    'resource_type_link'        => '',
                    'self_link'                 => '',
                    'custom_field'              => false,

             );
             $custom_fields[] =  array(
                    'http_etag'                 => '',
                    'id'                        => 'email',
                    'is_subscriber_updateable'  => true,
                    'name'                      => 'Email',
                    'resource_type_link'        => '',
                    'self_link'                 => '',
                    'custom_field'              => false,

             );

             $getexisting = get_option('mappingfieldarray');
             $json = $custom_fields;
             //check for existing values
             if($getexisting != false){

                foreach($json as $key => $value){

                  if(isset($getexisting[$key]->selected)){

                    $json[$key]['selected'] = $getexisting[$key]->selected;
                    /*var_dump($getexisting[$key]->selected);
                    var_dump($value);*/

                  }
                }

             }
             $json = $json;

           } else {
               $error =  '<div id="setting-error-tgmpa" class="updated settings-error notice is-dismissible"><h2>Did not find list</h2></div>';
               $json =   array('error' => $error);
           }

       } catch(AWeberAPIException $exc) {

          $error =  "<h3>AWeberAPIException:</h3> <li> Type: $exc->type <br>  <li> Msg : $exc->message<br> <li> Docs: $exc->documentation_url <br> <hr>";
          $json = array('error' => $error);

       }


     }else{
        //Throw error to json if it's default
        $json = array('error' => '<div id="setting-error-tgmpa" class="updated settings-error notice is-dismissible"><h3>Pick a list first</h3></div>');

     }

     echo json_encode($json);
 		die(); // this is required to return a proper result
   }
}
