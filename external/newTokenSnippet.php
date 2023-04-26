<?php
function get_status($order_key) {
	$url_status = '/sale/'.$order_key.'/status';
	$response = invoke_service($url_status);
	$status = json_decode($response);
	if(json_last_error()==JSON_ERROR_NONE) {
		return $status;
	}
	return null;
}

function generate_token($order_key) {
	$url_token = '/sale/'.$order_key.'/regenerate-token';
	// print_r($url_token);
	$response = invoke_service($url_token);
	return $response;
}

function invoke_service($url) {
	$user = $GLOBALS['hakan']['api-user'];
    $pass = $GLOBALS['hakan']['api-user-pass'];
	$header_auth = 'Authorization: Basic '.base64_encode("$user:$pass");
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $GLOBALS['hakan']['api'].'/woocommerce'.$url,
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
	// print_r($GLOBALS['hakan']['api'].'/woocommerce'.$url);

	$response = curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
	curl_close($curl);
	if(in_array((int)$status,array(200, 201, 202, 203, 204)) && $response){
		return $response;
	}
	return null;
}

function display_new_token_form() {
	$html = '<style>
	.form {
		table-layout: fixed;
  		width: 100%;
  		border-collapse: collapse;
  		border: 1px solid;
	}
	.form td {
	  padding: 5px;
	}
	.result-link {
		display: flex;
		width: 100%;
	}
	.copy-link {
		flex-grow: 1px;
		padding: 8px;
		border: 1px solid #cccccc;
	}
</style>
<script>
	let copy = (inputId) => {
	  document.getElementById(inputId).select();
	  document.execCommand("copy");
	};
</script>';

	if(isset($_GET["saleId"])) {
		$html = $html.'<div><table class="form">';
		$order_key = $_GET["saleId"];
		$info_status = get_status($order_key);
		if($info_status) {
			$html = $html.'
			<tr><td width="40%">Identificador de Orden:</td>
				<td width="60%">'.$info_status->orderId.'</td></tr>
			<tr><td>Usuario:</td>
				<td>'.$info_status->customer.'</td></tr>
			<tr><td>Fecha Compra:</td>
				<td>'.$info_status->dateTime.'</td></tr>
			<tr><td>¿Descargado?</td>
				<td><input type="checkbox" name="downloaded" '.($info_status->downloaded ? 'checked' : '').'></td></tr>';

			if(isset($_GET["orderId"])) {
				$order = new WC_Order($_GET["orderId"]);
				$html = $html.'<tr><td>Total:</td>
				<td>'.$order->get_total().'</td></tr>
				<tr><td>Email:</td>
				<td>'.$order->get_billing_email().'</td></tr>';

				foreach ( $order->get_items() as $item_id => $item ) {
					$html = $html.'<tr><td>Producto:</td><td>'.$item->get_product_id().' >> '.$item->get_name().'</td></tr>';
				}
			}
			$html = $html.'</table></div>';

			if($info_status->downloaded == false) {
				$html = $html.'<div><form action="#" method="POST" class="comment-form">
					<div style="font-weight: bold;">
						<h2>Generar nuevo enlace de Descarga</h2>
					</div>
					<table class="form">
						<tr>
							<td width="40%">Ingresa el Identificador de Orden:</td>
							<td width="60%">
								<input id="order-key" type="string" name="order-key" size="30" />
								<input id="regenerate-url" type="submit" name="regenerate-url" class="submit" value="Generar" />
							</td>
						</tr>
					</table>
				</form>
				<div>';

				if (isset($_POST['regenerate-url'])) {
					$order_key_entered = $_POST['order-key'];
					if($order_key_entered == $order_key) {
						$new_token = generate_token( $order_key );
						$new_url = $GLOBALS['hakan']['api'].'/woocommerce/download/'.$new_token;
						wc_print_notice( 'URL generado correctamente!' );
						$html = $html.'<div>URL Resultado:</div>
							<div class="result-link">
								<input style="width:100%; padding:0;" type="text" id="url-generated" value="'.$new_url.'" />
								<button class="copy-link" onclick="copy(\'url-generated\')"><span class="dashicons dashicons-clipboard"></span></button>
							</div>';
					} else {
						wc_print_notice( 'Debe ingresar el Identificador de Orden!', 'error' );
					}
				}
				$html = $html.'</div></div>';
			}

		} else {
			$html = $html.'<div><span>No se pudo recuperar una Venta con el identificador!!</span></div>';
		}
	} else {
		$html = $html.'<div><span>No se encontro el Identificador de Orden</span></div>';
	}

	return $html;
}

add_shortcode('generate_token', 'display_new_token_form');