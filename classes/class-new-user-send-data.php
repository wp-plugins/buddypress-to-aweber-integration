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
class NewUserSendData{

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

    add_action('init', array($this, 'check_options'), 10 );

    $options = get_option('aweber_buddypress_options');

    $consumerKey    = $options['consumerkey'];
    $consumerSecret = $options['consumersecret'];
    add_filter( 'bp_registration_needs_activation', '__return_false' );

    $this->aweber = new AWeberAPI($consumerKey, $consumerSecret);
    //Debugging
    //add_action( 'wp_head', array($this, 'bp_aweber_mapping_new_user' ) );
      add_action('wp_login',array($this, 'your_last_login' ) );

  }
  /**
   * Log login times
   */
  public function your_last_login($login) {
      global $user_ID;
      $user = get_userdatabylogin($login);
      update_usermeta($user->ID, 'last_login', current_time('mysql'));
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
   *
   */
   public function check_options(){
     $getexisting = get_option('mappingfieldarray');
       add_action( 'bp_core_activated_user', array($this, 'bp_aweber_mapping_new_user' ) );
       add_action( 'xprofile_updated_profile', array($this, 'update_profile_send') );

     if((get_option('accessToken') != false || get_option('accessToken') != NULL) && (get_option('accessTokenSecret')!= false || get_option('accessToken') != NULL) && $getexisting != false ){



     }
   }
   /**
    * Profile updated
    */
    public function update_profile_send(){
      global $bp;


      $user_id  =  get_current_user_id();

      $listid   = $this->options;

    	//$user_id = 160;
      //Variables
      $listid 				= $this->options;
      $user_meta      = get_user_meta($user_ID);
      //$user_options   =
      $getexisting    = get_option('mappingfieldarray');
      $custom_fields    = array();

      try {
          $account = $this->aweber->getAccount(get_option('accessToken'), get_option('accessTokenSecret'));
          $lists = $account->lists->find(array('name' => $listid['listid']));
          if(count($lists)) {

          $list = $lists[0];
          $listURL = $list->url;
          $list = $account->loadFromUrl($listURL);

          foreach($getexisting as $key => $value){

            switch ($value->selected->from) {
              case 'meta':
                # code...


                    if(!isset($value->custom_field)){

                      $custom_fields[$value->name] =  get_user_meta($user_id, $value->selected->id, true);

                    }elseif($value->id === 'name'){

                      $name   =  get_user_meta($user_id, $value->selected->id, true);

                    }elseif($value->id === 'email'){

                      $email   =  get_user_meta($user_id, $value->selected->id, true);

                    }
                break;
              case 'options':
                  # code...
                  if(!isset($value->custom_field)){

                    $userdata = get_userdata($user_id);
                    $custom_fields[$value->name] =  $userdata->{$value->selected->id};

                  }elseif($value->id === 'name'){

                    $userdata = get_userdata($user_id);
                    $name    =  $userdata->{$value->selected->id};


                  }elseif($value->id === 'email'){

                    $userdata = get_userdata($user_id);
                    $email    =  $userdata->{$value->selected->id};

                  }
                break;
              case 'buddypress':
                    # code...
                    if(!isset($value->custom_field)){

                      $custom_fields[$value->name] = bp_get_profile_field_data('field=' . $value->selected->name . '&user_id='.$user_id);

                    }elseif($value->id === 'name'){

                      $name = bp_get_profile_field_data('field=' . $value->selected->name . '&user_id='.$user_id);

                    }elseif($value->id === 'email'){

                      $email = bp_get_profile_field_data('field=' . $value->selected->name . '&user_id='.$user_id);

                    }
                break;

              default:
                # code...
                break;
            }



          }
          $params       = array('email' => $email);
          $subscribers  = $account->findSubscribers($params);
          if(count($subscribers)) {
					//Update user

            $subscriber                 = $subscribers[0];
            $subscriber->name           = $name;
            $subscriber->custom_fields  = $custom_fields;
            $subscriber->save();

          }else{
						//New user
						$params = array(
								'email' => $email,
								'name' => $name,
								'ip_address' => '127.0.0.1',
								'misc_notes' => 'Signed up from buddypress',
								'custom_fields' => $custom_fields,
						);
						$subscribers = $list->subscribers;
						$new_subscriber = $subscribers->create($params);

					}
          # success!

        }

      } catch(AWeberAPIException $exc) {
        /*
        print "<h3>AWeberAPIException:</h3>";
        print " <li> Type: $exc->type              <br>";
        print " <li> Msg : $exc->message           <br>";
        print " <li> Docs: $exc->documentation_url <br>";
        print "<hr>";
        */

      }
    }
   /**
    *
    */
    public function bp_aweber_mapping_new_user($user_id){
      global $bp;
      $listid = $this->options;
      //Variables
      $listid = $this->options;
      $user_meta      = get_user_meta($user_ID);
      //$user_options   =
      $getexisting    = get_option('mappingfieldarray');
      $custom_fields    = array();

      try {
          $account = $this->aweber->getAccount(get_option('accessToken'), get_option('accessTokenSecret'));
          $lists = $account->lists->find(array('name' => $listid['listid']));
          if(count($lists)) {

          $list = $lists[0];
          $listURL = $list->url;
          $list = $account->loadFromUrl($listURL);

          foreach($getexisting as $key => $value){

            switch ($value->selected->from) {
              case 'meta':
                # code...
                  if(!isset($value->custom_field)){

                    $custom_fields[$value->name] =  get_user_meta($user_id, $value->selected->id, true);

                  }elseif($value->id === 'name'){

                    $name   =  get_user_meta($user_id, $value->selected->id, true);

                  }elseif($value->id === 'email'){

                    $email   =  get_user_meta($user_id, $value->selected->id, true);

                  }
                break;
              case 'options':
                  # code...
                  if(!isset($value->custom_field)){

                    $userdata = get_userdata($user_id);
                    $custom_fields[$value->name] =  $userdata->{$value->selected->id};

                  }elseif($value->id === 'name'){

                    $userdata = get_userdata($user_id);
                    $name    =  $userdata->{$value->selected->id};


                  }elseif($value->id === 'email'){

                    $userdata = get_userdata($user_id);
                    $email    =  $userdata->{$value->selected->id};

                  }
                break;
              case 'buddypress':
                    # code...
                    if(!isset($value->custom_field)){

                      $custom_fields[$value->name] = bp_get_profile_field_data('field=' . $value->selected->name . '&user_id='.$user_id);

                    }elseif($value->id === 'name'){

                      $name = bp_get_profile_field_data('field=' . $value->selected->name . '&user_id='.$user_id);

                    }elseif($value->id === 'email'){

                      $email = bp_get_profile_field_data('field=' . $value->selected->name . '&user_id='.$user_id);

                    }
                break;

              default:
                # code...
                break;
            }



          }
          # create a subscriber
          $params = array(
              'email' => $email,
              'name' => $name,
              'ip_address' => '127.0.0.1',
              'misc_notes' => 'Signed up from buddypress',
              'custom_fields' => $custom_fields,
          );
          $subscribers = $list->subscribers;
          $new_subscriber = $subscribers->create($params);
          # success!

        }

      } catch(AWeberAPIException $exc) {

      }



    }
}
