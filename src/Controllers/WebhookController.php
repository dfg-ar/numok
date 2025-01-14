<?php

namespace Numok\Controllers;

use Numok\Database\Database;

class WebhookController extends Controller {
    public function stripeWebhook(): void {
        // Get webhook secret from settings
        $webhookSecret = Database::query(
            "SELECT value FROM settings WHERE name = 'stripe_webhook_secret' LIMIT 1"
        )->fetch()['value'];

        // Get payload and signature
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

        try {
            // Verify signature
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $webhookSecret
            );

            // Process different event types
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutCompleted($event->data->object);
                    break;
                    
                case 'payment_intent.succeeded':
                    $this->handlePaymentSucceeded($event->data->object);
                    break;

                case 'invoice.paid':
                    $this->handleInvoicePaid($event->data->object);
                    break;
            }

            http_response_code(200);
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            error_log('Stripe Webhook Error: ' . $e->getMessage());
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            error_log('Stripe Signature Error: ' . $e->getMessage());
            http_response_code(400);
            exit();
        } catch(\Exception $e) {
            // Other errors
            error_log('Stripe Webhook Processing Error: ' . $e->getMessage());
            http_response_code(500);
            exit();
        }
    }

    private function handleCheckoutCompleted($session): void {
        $metadata = $session->metadata ?? new \stdClass();
        $trackingCode = $metadata->numok_tracking_code ?? null;

        if (!$trackingCode) {
            $this->logEvent('checkout_completed_no_tracking', $session);
            return;
        }

        // Get partner program
        $partnerProgram = Database::query(
            "SELECT pp.*, p.reward_days, p.is_recurring 
             FROM partner_programs pp
             JOIN programs p ON pp.program_id = p.id
             WHERE pp.tracking_code = ? 
             AND pp.status = 'active'
             AND p.status = 'active'
             LIMIT 1",
            [$trackingCode]
        )->fetch();

        if (!$partnerProgram) {
            $this->logEvent('checkout_completed_invalid_tracking', $session);
            return;
        }

        // Calculate commission status
        $status = $partnerProgram['reward_days'] > 0 ? 'pending' : 'payable';

        // Store conversion
        try {
            Database::insert('conversions', [
                'partner_program_id' => $partnerProgram['id'],
                'stripe_payment_id' => $session->payment_intent,
                'amount' => $session->amount_total / 100, // Convert from cents
                'commission_amount' => $this->calculateCommission($session->amount_total / 100, $partnerProgram),
                'status' => $status,
                'customer_email' => $session->customer_details->email ?? null,
                'metadata' => json_encode([
                    'sid' => $metadata->numok_sid ?? null,
                    'sid2' => $metadata->numok_sid2 ?? null,
                    'sid3' => $metadata->numok_sid3 ?? null
                ])
            ]);

            $this->logEvent('conversion_created', [
                'payment_id' => $session->payment_intent,
                'tracking_code' => $trackingCode,
                'amount' => $session->amount_total / 100
            ]);
        } catch (\Exception $e) {
            $this->logEvent('conversion_creation_failed', [
                'error' => $e->getMessage(),
                'payment_id' => $session->payment_intent
            ]);
            throw $e;
        }
    }

    private function handlePaymentSucceeded($paymentIntent): void {
        // Similar to handleCheckoutCompleted but for direct PaymentIntents
        $metadata = $paymentIntent->metadata ?? new \stdClass();
        $trackingCode = $metadata->numok_tracking_code ?? null;

        if (!$trackingCode) {
            $this->logEvent('payment_succeeded_no_tracking', $paymentIntent);
            return;
        }

        // Rest of the logic similar to handleCheckoutCompleted...
    }

    private function handleInvoicePaid($invoice): void {
        // Handle recurring payments
        // Need to look up the original payment to get tracking info
        $originalPaymentId = $invoice->subscription;
        
        if (!$originalPaymentId) {
            $this->logEvent('invoice_paid_no_subscription', $invoice);
            return;
        }

        // Get the original conversion to find partner program
        $originalConversion = Database::query(
            "SELECT c.*, pp.tracking_code, p.is_recurring 
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             JOIN programs p ON pp.program_id = p.id
             WHERE c.stripe_payment_id = ?
             LIMIT 1",
            [$originalPaymentId]
        )->fetch();

        if (!$originalConversion || !$originalConversion['is_recurring']) {
            $this->logEvent('invoice_paid_no_recurring', $invoice);
            return;
        }

        // Create new conversion for the recurring payment...
    }

    private function calculateCommission(float $amount, array $partnerProgram): float {
        if ($partnerProgram['commission_type'] === 'percentage') {
            return round($amount * ($partnerProgram['commission_value'] / 100), 2);
        }
        return $partnerProgram['commission_value'];
    }

    private function logEvent(string $type, $data): void {
        Database::insert('logs', [
            'type' => $type,
            'message' => is_string($data) ? $data : json_encode($data),
            'context' => is_string($data) ? null : json_encode($data)
        ]);
    }
}