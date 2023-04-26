<?php
function registrar_venta($productId, $username, $orderId, $sku, $price) {
	$user = $GLOBALS['hakan']['api-user'];
    $pass = $GLOBALS['hakan']['api-user-pass'];
	$header_auth = 'Authorization: Basic '.base64_encode("$user:$pass");
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $GLOBALS['hakan']['api'].'/woocommerce/sale/'.$productId,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS =>'{
	  "productId": '.$productId.',
	  "username": "'.$username.'",
	  "orderId": "'.$orderId.'",
	  "sku": "'.$sku.'",
	  "price":'.$price.'
	  }',
	  CURLOPT_HTTPHEADER => array(
		  $header_auth,
		  'Content-Type: application/json'
	  ),
	));

	$response = curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
	curl_close($curl);
	if(in_array((int)$status,array(200, 201, 202, 203, 204)) && $response){
		return $response;
	}
	return null;
}

function verificar_registrar_venta( $order_id ) {
    $order = wc_get_order( $order_id );
	$user = $order->get_user();
	//print_r($user);
	$username = $user->user_login;
	$order_key = $order->order_key ;
	if(!$username) {
		$username = 'lectura';
	}

	//echo "Orden: " . $order_id . "<br />";
	//echo "User Name: " . $username . "<br />";
	//echo "Order Key: " . $order_key . "<br />";
	//echo "Status: " . $order->get_status() . "<br />";
	//echo "Product ID: " . array_values($order->get_items())[0]->get_product_id() . "<br />";

	switch($order->get_status()){
		case 'processing':
		case 'on-hold':
			if($order->get_total() == 0) {
				$order->update_status( 'wc-completed' );
			}
			echo <<<HTML
			<style>.contador{
			font-size: 50%;
			padding: 5px 10px;
			color: red;
			border :1px solid red;
			border-radius : 10px;
			}</style>
			<script>
			var titulo_antiguo = document.querySelector(".entry-title").innerText;
			var contador = 10;
			var intervalo = setInterval(function(){
				document.querySelector(".entry-title").innerHTML = titulo_antiguo + ' <span class="contador"> Se verificará su pago en ' + (contador--) + '</span>';
				if(contador == 0) {
					console.log("actualizamos la pagina para ver si se verifico el pago");
					var currentPage = new URL(window.location.href);
					currentPage.searchParams.set('r', (+ new Date * Math.random()).toString(36).substring(0, 5));
					window.location.href = currentPage.href;
				}
			}, 1000);
			</script>
			HTML;

			echo "<script>console.log('Su pago se esta confirmando')</script>";
			break;
		case 'completed':
			$json = array("ids"=>array(), "tokens"=>array(), "orderId"=>$order_key, "uname"=>$username);
			$hay_error = false;
			foreach($order->get_items() as $item) {
				$product = $item->get_product();
				$token = registrar_venta($item->get_product_id(), $username, $order_key, $product->get_sku(), $product->get_price());
				if($token) {
					$json["ids"][] = $item->get_product_id();
					$json["tokens"][] = $token;
					$json["nombres"][] = $item->get_name();
				} else {
					$hay_error = true;
					echo "<script>alert('Nose puede registrar la venta correctamente, por favor contactese con el administrador!')</script>";
				}
			}

			if(!$hay_error) {
				$text = json_encode($json);
				$url = get_site_url().'/pagina-de-descarga/?data='.base64_encode($text);
				wp_safe_redirect( $url );
       			exit;
			}
			break;
	}
}

add_action( 'woocommerce_thankyou', 'verificar_registrar_venta' );
