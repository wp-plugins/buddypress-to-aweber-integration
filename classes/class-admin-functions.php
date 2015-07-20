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
class AdminFunctions{
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
  	 * Tabbed Settings Page
  	 */
  	public function admin_tabs( $current = 'Settings' ) {
  	    $tabs = array(
                        'settings'  => 'Settings',
                        'fields'    => 'List Settings',
                        'clear'     => 'Reset Settings'
                      );
  	    $links = array();
  	    echo '<div id="icon-themes" class="icon32"><br></div>';
  	    echo '<h2 class="nav-tab-wrapper">';
  	    foreach( $tabs as $tab => $name ){
  	        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
  	        echo "<a class='nav-tab$class' href='?page=buddypress-to-aweber&tab=$tab'>$name</a>";

  	    }
  	    echo '</h2>';
  	}


}
