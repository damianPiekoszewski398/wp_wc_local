<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Przelewy24Mailer
 */
class Przelewy24Mailer {

    /**
     * Send amil.
     *
     * @param WC_Order|null $order Order that need an mail.
     */
    public function trigger( $order ) {

        if ( !$order )
            return;

        if($order->is_paid() || $order->get_payment_method() !== 'przelewy24')
            return;

        $subject = __('Dziękujemy za złożenie zamówienia', 'przelewy24');

        $this->send_email( $order, $subject, '/emails/notification_email.php' );

        $note = __('Wiadomość o płatnosci P24 wysłana.');
        $order->add_order_note($note);
    }

    /**
     * Send order summary email.
     *
     * @param WC_Order|null $order Order that need an mail.
     */
    public function send_order_summary_mail( $order )
    {
        if (
            ! $order ||
            $order->get_payment_method() !== WC_Gateway_Przelewy24::PAYMENT_METHOD ||
            ! get_przelewy24_plugin_instance()->should_activate_order_created_notification()
        ) {
            return;
        }

        $subject = __('Dziękujemy za złożenie zamówienia', 'przelewy24');

        $this->send_email( $order, $subject, '/emails/order_summary.php' );
    }

    /**
     * Get email content
     *
     * @param string    $template
     * @param WC_Order  $order
     * @param string    $heading
     * @param WC_Emails $mailer
     *
     * @return string
     */
    private function get_content( $template, $order, $heading, $mailer ) {
        return wc_get_template_html( $template, array(
            'order'         => $order,
            'email_heading' => $heading,
            'sent_to_admin' => false,
            'plain_text'    => false,
            'email'         => $mailer,
        ), '', PRZELEWY24_PATH );
    }

    /**
     * Perform sending email
     *
     * @param WC_Order $order
     * @param string   $subject
     * @param string   $template
     */
    private function send_email( $order, $subject, $template )
    {
        $recipient = $order->get_billing_email();

        $mailer = WC()->mailer();

        $content = $this->get_content( $template, $order, $subject, $mailer );

        $headers = 'Content-Type: text/html';

        $mailer->send( $recipient, $subject, $content, $headers );
    }

}
