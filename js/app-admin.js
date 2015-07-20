'use strict';
/**
 * This is the main javascript file for the Buddypress to Aweber plugin's main administration view.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end administrator.
 *
 * @package   buddypress-to-aweber
 * @author    vimes1984 <churchill.c.j@gmail.com>
 * @license   GPL-2.0+
 * @link      http://buildawebdoctor.com
 * @copyright 5-15-2015 BAWD
 */

(function ($) {
	$(function () {
		// Place your administration-specific JavaScript here
	});
}(jQuery));

var aweberapp = angular.module('aweberapp', []);
aweberapp.controller('mainapp', function () {});
aweberapp.controller('listsapp', function ($scope, lists, $sce) {

		$scope.error 						= '';
		NProgress.configure({parent: '#listwrap'});
		NProgress.start();

		$scope.toTrusted = function(htmlCode) {
			return $sce.trustAsHtml(htmlCode);
		};
		//Get custom fields from aweber
		lists.getList($scope)
			.success(function(data, status, headers, config) {

				if(data.hasOwnProperty("error") ){
					$scope.error = data.error;
					console.log(data);
					NProgress.done();

					return;
				}
				//return data
				$scope.customfieldlist 	= data;
				NProgress.done();

				console.log(data);

			}).error(function(data, status, headers, config) {});
		//get buddypress profile fields
		lists.getProfileBuddy($scope)
			.success(function(data, status, headers, config) {

				$scope.getProfileBuddy = data;

				console.log(data);

			}).error(function(data, status, headers, config) {});

			//Get Wordpress options
			lists.getProfileOptions($scope)
				.success(function(data, status, headers, config) {

					$scope.getProfileOptions = data;

					console.log(data);

				}).error(function(data, status, headers, config) {});

				//Get profile meta
				lists.getProfileMeta($scope)
					.success(function(data, status, headers, config) {

						$scope.getProfileMeta = data;

						console.log(data);

					}).error(function(data, status, headers, config) {});


			$scope.setfields = function(){
				console.log(this);
				$scope.fielddata = this.customfieldlist
					NProgress.start();

				lists.setFieldMapping($scope)
					.success(function(data, status, headers, config) {

						NProgress.done();

						$scope.successinfo  = '<div id="setting-error-tgmpa" class="updated settings-error notice is-dismissible"><h2>Saved mapping</h2></div>';

					}).error(function(data, status, headers, config) {});
			};
});
//List factory
aweberapp.factory('lists', function($http) {
    return {
			getList: function($scope){
            return  $http({
                            method: 'POST',
                            url: '/wp-admin/admin-ajax.php',
                            data: $scope.accntobject,
                            params: { 'action': 'get_list'}
                    });
        },
				getProfileBuddy: function($scope){
					return  $http({
						method: 'POST',
						url: '/wp-admin/admin-ajax.php',
						data: $scope.accntobject,
						params: { 'action': 'get_bpress_profile'}
						});
				},
				getProfileOptions: function($scope){
					return  $http({
						method: 'POST',
						url: '/wp-admin/admin-ajax.php',
						data: $scope.accntobject,
						params: { 'action': 'get_profile_options'}
						});
				},
				getProfileMeta: function($scope){
					return  $http({
						method: 'POST',
						url: '/wp-admin/admin-ajax.php',
						data: $scope.accntobject,
						params: { 'action': 'get_profile_meta'}
						});
				},
				setFieldMapping: function($scope){
					return  $http({
						method: 'POST',
						url: '/wp-admin/admin-ajax.php',
						data: $scope.fielddata,
						params: { 'action': 'set_field_mapping'}
						});
				},

    };
});
