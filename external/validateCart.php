<?php
function simular_venta($id, $precio, $sku) {
	$user = $GLOBALS['hakan']['api-user'];
	$pass = $GLOBALS['hakan']['api-user-pass'];
	$header_auth = 'Authorization: Basic '.base64_encode("$user:$pass");
	$curl = curl_init();
	
	curl_setopt_array($curl, array(
	  CURLOPT_URL => $GLOBALS['hakan']['api'].'/woocommerce/simulate-sale/'.$id.'?price='.$precio."&sku=".$sku,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	  CURLOPT_HTTPHEADER => array(
		$header_auth
	  )));
	
	$response = curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
	if(!(in_array((int)$status,array(200, 201, 202, 203, 204)) && $response)){
		return null;
	}
	$result = json_decode($response);
    curl_close($curl);
    if(json_last_error() == JSON_ERROR_NONE) {
		return $result;
	}
	return null;
}

function validar_adicion_carrito($passed, $product_id, $quantity) {
	$items = WC()->cart->get_cart();
	if(sizeof($items) == 0) {
		$passed = true;
	} else {
		$passed = false;
		wc_add_notice( __( 'No es posible agregar mas de una publicación al Carrito', 'textdomain' ), 'error' );
		return $passed;
	}
	
	$product = wc_get_product( $product_id );
	$id = $product->get_id();
	$precio = $product->get_price() ;
	$sku = $product->get_sku();
	$api = simular_venta($id, $precio, $sku);
	//print_r($api);
	
	if(!is_null($api)) {
		if(!$api->ok) {
			$passed = false;
			wc_add_notice( __('Publicación no disponible para su venta, intente nuevamente ó comuniquese con el administrador!', 'textdomain'), 'error' );
		} else {
			$passed = true;
		}
	} else {
		$passed = false;
		wc_add_notice( __('Servicio de verificación no disponible, intente nuevamente mas tarde por favor!', 'textdomain'), 'error' );
	}
	return $passed;
}

add_filter( 'woocommerce_add_to_cart_validation', 'validar_adicion_carrito', 10, 5 );
