<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Oversell__Request
 *
 * @since TBD
 */
class Tribe__Tickets__Commerce__PayPal__Oversell__Request {

	/**
	 * @var string
	 */
	public static $oversell_action = 'oversell';

	/**
	 * @var string
	 */
	protected $policy;

	/**
	 * @var string
	 */
	protected $order_id;

	/**
	 * Conditionally handles an oversell request.
	 *
	 * @since TBD
	 */
	public function handle() {
		if ( ! isset( $_GET['tpp_action'], $_GET['tpp_policy'], $_GET['tpp_order_id'], $_GET['tpp_slug'] ) ) {
			return;
		}

		if ( self::$oversell_action !== $_GET['tpp_action'] ) {
			return;
		}

		$this->policy   = $_GET['tpp_policy'];
		$this->order_id = $_GET['tpp_order_id'];

		if ( false === $order = Tribe__Tickets__Commerce__PayPal__Order::from_order_id( $this->order_id ) ) {
			return;
		}

		$cap = get_post_type_object( Tribe__Tickets__Commerce__PayPal__Main::ORDER_OBJECT )->cap->edit_post;
		if ( ! current_user_can( $cap, $order->get_post_id() ) ) {
			return;
		}

		/** @var Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal = tribe( 'tickets.commerce.paypal' );

		add_filter( 'tribe_tickets_commerce_paypal_oversell_policy', array( $this, 'filter_policy' ), 10, 4 );
		add_filter( 'tribe_tickets_commerce_paypal_oversell_generates_notice', '__return_false' );

		/** @var Tribe__Tickets__Commerce__PayPal__Gateway $gateway */
		$gateway = tribe( 'tickets.commerce.paypal.gateway' );

		$data = $order->get_meta( 'transaction_data' );
		$retry_status = $order->get_status();

		// put back the order status to pending
		$order->set_meta( 'payment_status', 'pending' );
		$order->update();

		$gateway->set_raw_transaction_data( $data );
		$gateway->set_transaction_data( $gateway->parse_transaction( $data ) );

		$paypal->generate_tickets( $retry_status, false );

		/** @var Tribe__Tickets__Commerce__PayPal__Notices $notices */
		$notices = tribe('tickets.commerce.paypal.notices');
		$notices->remove_transient_notice( $_GET['tpp_slug'] );

		/** @var Tribe__Tickets__Commerce__PayPal__Orders__Report $orders_report */
		$post_ids = $order->get_related_post_ids();
		$post     = get_post( reset( $post_ids ) );
		wp_safe_redirect( Tribe__Tickets__Commerce__PayPal__Orders__Report::get_tickets_report_link( $post ) );

		tribe_exit();
	}

	public function filter_policy( $policy, $post_id, $ticket_id, $order_id ) {
		return $order_id == $this->order_id ? $this->policy : $policy;
	}
}