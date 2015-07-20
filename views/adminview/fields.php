<?php
$accessToken        = get_option('accessToken');
$accessTokenSecret  = get_option('accessTokenSecret');
//First check to make sure we have a access token
if(isset($_COOKIE['accessToken']) && isset($_COOKIE['accessTokenSecret'])){

 update_option('accessToken', $_COOKIE['accessToken']);
 update_option('accessTokenSecret', $_COOKIE['accessTokenSecret']);
 $accessToken        = get_option('accessToken');
 $accessTokenSecret  = get_option('accessTokenSecret');

}

if( isset($accessToken)  && isset($accessTokenSecret) && $accessToken != NULL && $accessTokenSecret != NULL ){ ?>
  <div ng-controller="listsapp">
    <h2>Buddypress fields</h2>
    <form method="post" action="options.php">
         <?php
         // This prints out all hidden setting fields
          settings_fields( 'aweber_list_options_group' );
          do_settings_sections( 'list_options_page' );

          submit_button('Save');


         ?>
       </form>
       <hr>

    <div id="listwrap">
          <h3>List fields</h3>
          <div class="" ng-bind-html="toTrusted(successinfo)"></div>
          <div  ng-bind-html="toTrusted(error)"></div>

          <form  ng-submit="setfields()" ng-cloak>
              <section ng-repeat="customfield in customfieldlist">
                <h2> Aweber: {{customfield.name}}</h2>
                <p>please pick ONE of the following three</p>
                <label for="getProfileBuddy">
                  Buddypress profile fields:

                  <select name="getProfileBuddy" id="getProfileBuddy" ng-options=" fields.name for fields in getProfileBuddy track by fields.id" ng-model="customfield.selected"></select>
                  &nbsp;
                </label>
                <label for="getProfileOptions">
                  Wordpress profile main profile fields:
                  <select name="getProfileOptions" id="getProfileOptions" ng-options="fields.name for fields in getProfileOptions track by fields.id" ng-model="customfield.selected"></select>
                  &nbsp;
                </label>
                <label for="getProfileMeta">
                  Wordpress profile meta fields:
                  <select name="getProfileMeta" id="getProfileMeta" ng-options="fields.name for fields in getProfileMeta track by fields.id" ng-model="customfield.selected"></select>
                  &nbsp;
                </label>

                <hr />
              </section>
              <input type="submit" name="submit" value="Save mapping " class="button button-primary">

          </form>
    <div class="" ng-bind-html="toTrusted(successinfo)"></div>
    </div>



  </div>
    <?php
}else{
    ?>
  <h2> Set your Consumer Key and your Consumer Secret before picking a list</h2>
<?php } ?>
