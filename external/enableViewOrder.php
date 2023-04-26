<?php
add_filter( 'woocommerce_order_actions', 'adicionar_accion_mostrar_pagina_pedido', 9999, 2 );

function adicionar_accion_mostrar_pagina_pedido( $actions, $order ) {
   if ( $order->has_status( wc_get_is_paid_statuses() ) ) {
      $actions['mostrar_pedido'] = 'Mostrar pagina de Pedido';
   }
   return $actions;
}

add_action( 'woocommerce_order_action_mostrar_pedido', 'redirigir_accion_mostrar_pagina_pedido' );

function redirigir_accion_mostrar_pagina_pedido( $order ) {
   $url_pedido = $order->get_checkout_order_received_url();
   add_filter( 'redirect_post_location', function() use ( $url_pedido ) {
      return $url_pedido;
   });
}