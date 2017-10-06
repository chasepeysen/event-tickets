<?php
/**
 * PayPal Tickets Success content
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/tickets/tpp-success.php
 *
 * @package TribeEventsCalendar
 * @version TBD
 *
 * @var bool    $order_is_valid Whether the current order is a valid one or not.
 * @var string  $purchaser_name
 * @var string  $purchaser_email
 * @var bool    $is_event Whether the post the tickets are associated with is an event or not.
 * @var array   $tickets {
 *      @type string $name     The ticket name
 *      @type int    $price    The ticket unit price
 *      @type int    $quantity The number of tickets of this type purchased by the user
 *      @type int    $subtotal The  ticket subtotal
 *      }
 * @var array   $order {
 *      @type int $quantity The total number or purchased tickets
 *      @type int $total    The  order subtotal
 *      }
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$view      = Tribe__Tickets__Tickets_View::instance();
$event     = get_post( $ticket_post_id );
$post_type = get_post_type_object( $event->post_type );

$is_event_page = class_exists( 'Tribe__Events__Main' ) && Tribe__Events__Main::POSTTYPE === $event->post_type ? true : false;
?>

<div class="tribe-events-single tpp-success">
	<?php if ( ! $order_is_valid ) : ?>
		<div class="order-recap invalid">
			<p>
				<?php esc_html__( 'Whoops! It looks like there was a problem with your order. Please contact the site owner for assistance.', 'event-tickets' ); ?>
			</p>
		</div>
	<?php else : ?>
		<div class="order-recap valid">
			<p>
				<?php esc_html_e( 'Thank you for your purchase! You will receive your receipt and tickets via email.', 'event-tickets' ); ?>
			</p>
			<p>
				<strong><?php esc_html_e( 'Purchaser Name', 'event-tickets' ) ?>:</strong> <?php echo esc_html( $purchaser_name ) ?>
			</p>
			<p>
				<strong><?php esc_html_e( 'Purchaser Email', 'event-tickets' ) ?>:</strong> <?php echo esc_html( antispambot( $purchaser_email ) ) ?>
			</p>
		</div>
		<table class="tickets">
			<thead>
				<tr>
					<th><?php echo esc_html_x( 'Ticket', 'Success page tickets table header', 'event-tickets' ) ?></th>
					<th><?php echo  esc_html_x( 'Price', 'Success page tickets table header', 'event-tickets' ) ?></th>
					<th><?php echo esc_html_x( 'Quantity', 'Success page tickets table header', 'event-tickets' ) ?></th>
					<th><?php echo esc_html_x( 'Subtotal', 'Success page tickets table header', 'event-tickets' ) ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $tickets as $ticket ) : ?>
				<?php $ticket_post_id = $ticket['post_id'] ?>
				<tr class="ticket">
					<td class="post-details">
						<?php if ( ! empty( $ticket['header_image_id'] ) ) : ?>
							<div class="thumbnail">
								<?php echo get_image_tag(
									$ticket['header_image_id'],
									__( 'Ticket header image', 'event-tickets' ),
									get_the_title( $ticket_post_id ),
									'none',
									'thumbnail'
								); ?>
							</div>
						<?php endif; ?>
						<div class="ticket-name">
							<?php echo esc_html( $ticket['name'] ) ?>
						</div>
						<div class="post-permalink">
							<a href="<?php the_permalink( $ticket_post_id ) ?>">
								<?php echo esc_html( get_the_title($ticket_post_id) ) ?>
							</a>
						</div>
						<?php if ( $is_event ) : ?>
							<span class="post-date"> - <?php echo esc_html( tribe_get_start_date( $ticket_post_id, false ) ) ?></span>
						<?php endif; ?>
					</td>
					<td class="ticket-price">
						<div>
							<?php echo esc_html( tribe_format_currency( $ticket['price'], $ticket_post_id ) ) ?>
						</div>
					</td>
					<td class="ticket-quantity">
						<div>
							<?php echo esc_html( $ticket['quantity'] ) ?>
						</div>
					</td>
					<td class="ticket-subtotal">
						<div>
							<?php echo esc_html( tribe_format_currency( $ticket['subtotal'], $ticket_post_id ) ) ?>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
			<tr class="order">
				<td class="empty"></td>
				<td class="title">
					<strong><?php esc_html_e( 'Order Total', 'event-tickets' ) ?></strong>
				</td>
				<td class="quantity">
					<div><?php echo esc_html( $order['quantity'] ) ?></div>
				</td>
				<td class="total">
					<div><?php echo esc_html( tribe_format_currency( $order['total'], $ticket_post_id ) ) ?></div>
				</td>
			</tr>
			</tbody>
		</table>
	<?php endif; ?>
</div><!-- #tribe-events-content -->
