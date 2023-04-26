<?php
function accion_mostrar_pagina_new_token( $actions, $order ) {
   if ( $order->has_status( wc_get_is_paid_statuses() ) ) {
      $actions['mostrar_new_token'] = 'Generar Token Descarga';
   }
   return $actions;
}

add_filter( 'woocommerce_order_actions', 'accion_mostrar_pagina_new_token', 9999, 2 );

function redirigir_accion_mostrar_new_token( $order ) {
   $url_generate_token = get_permalink( get_page_by_path( 'generar-token-descarga' ) ).'/?orderId='.$order->get_id().'&saleId='.$order->order_key;
   add_filter( 'redirect_post_location', function() use ( $url_generate_token ) {
      return $url_generate_token;
   });
}

add_action( 'woocommerce_order_action_mostrar_new_token', 'redirigir_accion_mostrar_new_token' );
