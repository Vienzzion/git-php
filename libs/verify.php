<?php
class Verify {

	public static function system($config) {
		if(!extension_loaded('gd') || !function_exists('gd_info')) {
			printf('You are required to have GD installed. Please contact your administrator!');
			exit;
		}
	}

}