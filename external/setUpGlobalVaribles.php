// To Use:
// $GLOBALS['hakan']['api']
<?php
function hakan_global_vars() {
	global $hakan;
	$hakan = array(
		'api'  => 'https://servicios-test.hakansolutions.com/lectura/api',
		'api-user'  => 'admin',
		'api-user-pass'  => 'adminpassword',
	);
}

add_action( 'parse_query', 'hakan_global_vars' );
