<?php
/**
 * Plugin Name: Order Reminder For WooCommerce
 * Plugin URI: https://www.wpcocktail.com/image-caption
 * Description: Order Reminder For WooCommerce plugin is designed to enhance customer engagement and feedback. 
 * Version: 1.0.0
 * Author: WPCocktail
 * Author URI: https://www.wpcocktail.com/
 * License:  GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: image-caption-for-wp
 * Domain Path: /languages
 * Tested up to: 6.4.2
 */

// Exit if accessed directly, I would like to protect my packag
if (!defined('ABSPATH')) {
    exit;
}
// Check if WooCommerce is active
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    // Hook when order status changes to completed
    add_action( 'woocommerce_order_status_completed', 'wpc_send_order_reminder_email', 10, 1 );

    function wpc_send_order_reminder_email( $order_id ) {
        // Schedule an action in 30 days
        $thirty_days = 30 * DAY_IN_SECONDS;
        wp_schedule_single_event( time() + $thirty_days, 'send_reminder_email_hook', array( $order_id ) );
    }

    // Hook the scheduled action to a function that sends the email
    add_action( 'send_reminder_email_hook', 'wpc_send_reminder_email' );

    function wpc_send_reminder_email( $order_id ) {
        $order = wc_get_order( $order_id );
        $customer_email = $order->get_billing_email();
        
        // Craft the email content
        $subject = 'How do you like your product?';
        $message = 'Dear customer, it has been 30 days since your order. We would love to hear your thoughts on the product you purchased.';

        // Send the email
        wp_mail( $customer_email, $subject, $message );
    }

}
