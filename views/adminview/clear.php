<?php
 $options = get_option( 'aweber_buddypress_options' );
?>
<h1>Clear all api settings keys and cookies</h1>
<h2>Settings displayed below for debuging.</h2>
<p>Callback Url:          <?php echo $_COOKIE['callbackUrl'];        ?> </p>
<form method="post" action="options.php">

     <?php
     // This prints out all hidden setting fields
      settings_fields( 'buddypress_to_aweber_group' );
      do_settings_sections( 'buddypress-to-aweber' );
      do_settings_sections( 'access_tokens' );

      do_settings_sections( 'setting_clear' );

      submit_button('CLEAR ALL OPTIONS');

     ?>
</form>
