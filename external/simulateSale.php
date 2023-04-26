<?php
function paso_1_simular_venta($id, $precio, $sku) {
	$user = 'admin';
	$pass = 'adminpassword';
	$header_auth = 'Authorization: Basic '.base64_encode("$user:$pass");
	//var_dump('https://servicios.scratch-it.tech/lectura/api/woocommerce/simulate-sale/'.$id.'?price='.$precio."&sku=".$sku);
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://servicios.scratch-it.tech/lectura/api/woocommerce/simulate-sale/'.$id.'?price='.$precio."&sku=".$sku,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'GET',
	  CURLOPT_HTTPHEADER => array(
		$header_auth
	  ),
	));

	$response = curl_exec($curl);
	//var_dump($response);
	curl_close($curl);
	$respones = json_decode($response);
	if(json_last_error()==JSON_ERROR_NONE) {
		return $response;
	}
	return null;
}

add_filter( 'wc_add_to_cart_message_html', 'paso_1' );
function paso_1($message = null, $products = null) {
	$msg = '/!\\EL precio del libro fue actualizo';
	//var_dump($values['data']->sku);

	$salida = '';
	$items = WC()->cart->get_cart();
    foreach($items as $item => $values) {
        $id = $values['data']->get_id();
        $precio = $values['data']->get_price() ;
        $api = paso_1_simular_venta("$id",$precio,$values['data']->sku);
        if(!is_null($api)) {
            if(!$api->ok) {
                $salida .= "<b>{$api->message}</b>";
            } else if($api->price!=$precio) {
                $salida .= $msg  . " ";
            } else {
                continue;
            }
        } else {
            $salida .= '<br/><b style="color:red">Ocurrio un error al verificar el precio del libro, favor verificar su Carrito de compra</b>';
        }
	  $salida .= "<br/>";
    }

    if($salida=='') {
        return null;
    }

    return $message.$salida;
}