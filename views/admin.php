<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   buddypress-to-aweber
 * @author    vimes1984 <churchill.c.j@gmail.com>
 * @license   GPL-2.0+
 * @link      http://buildawebdoctor.com
 * @copyright 5-15-2015 BAWD
 */

	global $wpdb,$woocommerce, $pagenow;
	$getadminfunction = new AdminFunctions();
?>
<div class="wrap" ng-app="aweberapp" ng-controller="mainapp">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<!-- TODO: Provide markup for your options page here. -->
	<div class="wrap">

						<?php


							if ( isset ( $_GET['tab'] ) ) $getadminfunction->admin_tabs($_GET['tab']); else $getadminfunction->admin_tabs('settings');
						?>
		<div id="poststuff">
			<?php

				wp_nonce_field( "buddypress-to-aweber" );

				if ( $pagenow == 'admin.php' && $_GET['page'] == 'buddypress-to-aweber' ){

					if ( isset ( $_GET['tab'] ) ) {

						$tab = $_GET['tab'];

					}else{

						$tab = 'settings';

					}

					switch ( $tab ){
						case 'fields' :
							include_once(dirname(__FILE__). '/adminview/fields.php');
						break;
						case 'settings' :
							include_once(dirname(__FILE__). '/adminview/options.php');
						break;
						case 'clear' :
							include_once(dirname(__FILE__). '/adminview/clear.php');
						break;
					}
				}


			?>

			<p>Plugin built by  <a href="http://accruemarketing.com/">Accrue</a> | programed by <a href="http://twitter.com/vimes1984">BAWD</a></p>
		</div>
	</div>
</div>
