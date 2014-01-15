<?php
/**
 * Jamef tracking code.
 */
class WC_Jamef_Tracking {

	/**
	 * Initialize the tracking metabox.
	 */
	public function __construct() {

		// Add metabox.
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );

		// Save Metabox.
		add_action( 'save_post', array( $this, 'save' ) );

		// Show tracking code in order details.
		add_action( 'woocommerce_view_order', array( $this, 'view_order_tracking_code' ), 1 );
	}

	/**
	 * Register tracking code metabox.
	 *
	 * @return void
	 */
	public function register_metabox() {
		add_meta_box(
			'wc_jamef',
			'Jamef',
			array( $this, 'metabox_content' ),
			'shop_order',
			'side',
			'default'
		);
	}

	/**
	 * Tracking code metabox content.
	 *
	 * @param  object $post order_shop data.
	 *
	 * @return string       Metabox HTML.
	 */
	public function metabox_content( $post ) {
		// Use nonce for verification.
		wp_nonce_field( basename( __FILE__ ), 'wc_jamef_nonce' );

		$html = '<label for="jamef_tracking">' . __( 'Tracking code:', 'wcjamef' ) . '</label><br />';
		$html .= '<input type="text" id="jamef_tracking" name="jamef_tracking" value="' . get_post_meta( $post->ID, 'jamef_tracking', true ) . '" style="width: 100%;" />';

		echo $html;
	}

	/**
	 * Save metabox data.
	 *
	 * @param  int $post_id Current post type ID.
	 *
	 * @return void
	 */
	public function save( $post_id ) {
		// Verify nonce.
		if ( ! isset( $_POST['wc_jamef_nonce'] ) || ! wp_verify_nonce( $_POST['wc_jamef_nonce'], basename( __FILE__ ) ) ) {
			return $post_id;
		}

		// Verify if this is an auto save routine.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Check permissions.
		if ( 'shop_order' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( isset( $_POST['jamef_tracking'] ) ) {
			$old = get_post_meta( $post_id, 'jamef_tracking', true );

			$new = $_POST['jamef_tracking'];

			if ( $new && $new != $old ) {
				update_post_meta( $post_id, 'jamef_tracking', $new );

				// Gets order data.
				$order = new WC_Order( $post_id );

				// Add order note.
				$order->add_order_note( sprintf( __( 'Added a Jamef tracking code: %s', 'wcjamef' ), $new ) );

				// Send email notification.
				$this->email_notification( $order, $new );
			} elseif ( '' == $new && $old ) {
				delete_post_meta( $post_id, 'jamef_tracking', $old );
			}
		}
	}

	/**
	 * Tracking code email notification.
	 *
	 * @param  object $order         Order data.
	 * @param  string $tracking_code The Jamef tracking code.
	 *
	 * @return void
	 */
	protected function email_notification( $order, $tracking_code ) {
		// Backwards compatibility.
		if ( function_exists( 'WC' ) ) {
			$mailer = WC()->mailer();
		} else {
			global $woocommerce;
			$mailer = $woocommerce->mailer();
		}

		$subject = sprintf( __( 'Your the Jamef tracking code of the order #%s', 'wcjamef' ), $order->id );

		// Mail headers.
		$headers = array();
		$headers[] = "Content-Type: text/html\r\n";

		// Body message.
		$url = sprintf( '<a href="http://websro.jamef.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=%1$s" target="_blank">%1$s</a>', $tracking_code );
		$main_message = '<p>' . sprintf( __( 'Track the delivery of your purchase at the Jamef: %s', 'wcjamef' ), $url ) . '</p>';

		// Sets message template.
		$message = $mailer->wrap_message( __( 'Your the Jamef tracking code', 'wcjamef' ), $main_message );

		// Send email.
		$mailer->send( $order->billing_email, $subject, $message, $headers, '' );
	}

	/**
	 * Display the order tracking code in order details.
	 *
	 * @param  int    $order_id Order ID.
	 *
	 * @return string           Tracking code as link.
	 */
	public function view_order_tracking_code( $order_id ) {
		$tracking_code = get_post_meta( $order_id, 'jamef_tracking', true );

		if ( ! empty( $tracking_code ) ) {
			$url = sprintf( '<a href="http://websro.jamef.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=%1$s" target="_blank">%1$s</a>', $tracking_code );
			echo '<p>' . sprintf( __( 'Your the Jamef tracking code: %s.', 'wcjamef' ), $url ) . '</p>';
		}
	}
}
