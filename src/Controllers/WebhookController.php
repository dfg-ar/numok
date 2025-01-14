<?php

namespace Numok\Controllers;

use Numok\Database\Database;

class WebhookController extends Controller {
    public function stripeWebhook(): void {

        // Log the raw payload
        $payload = @file_get_contents('php://input');
        $this->logEvent('webhook_received', [
            'payload' => $payload,
            'signature' => $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? null
        ]);
        
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
        } catch (\Exception $e) {
            $this->logEvent('webhook_error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
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
        // Add detailed logging
        $this->logEvent('payment_intent_processing', [
            'payment_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'metadata' => $paymentIntent->metadata
        ]);
    
        // Get tracking code from metadata
        $metadata = $paymentIntent->metadata ?? new \stdClass();
        $trackingCode = $metadata->numok_tracking_code ?? null;
    
        if (!$trackingCode) {
            $this->logEvent('payment_succeeded_no_tracking', $paymentIntent);
            return;
        }
    
        // Get partner program with detailed logging
        $partnerProgram = Database::query(
            "SELECT pp.*, p.reward_days, p.is_recurring, p.commission_type, p.commission_value
             FROM partner_programs pp
             JOIN programs p ON pp.program_id = p.id
             WHERE pp.tracking_code = ? 
             AND pp.status = 'active'
             AND p.status = 'active'
             LIMIT 1",
            [$trackingCode]
        )->fetch();
    
        if (!$partnerProgram) {
            $this->logEvent('payment_succeeded_invalid_tracking', [
                'payment_id' => $paymentIntent->id,
                'tracking_code' => $trackingCode
            ]);
            return;
        }
    
        // Log found partner program
        $this->logEvent('partner_program_found', [
            'payment_id' => $paymentIntent->id,
            'partner_program' => $partnerProgram
        ]);
    
        // Calculate amount in dollars
        $amount = $paymentIntent->amount / 100;
    
        // Calculate commission
        $commission = $this->calculateCommission($amount, $partnerProgram);
    
        // Determine status
        $status = $partnerProgram['reward_days'] > 0 ? 'pending' : 'payable';
    
        // Store conversion
        try {
            Database::insert('conversions', [
                'partner_program_id' => $partnerProgram['id'],
                'stripe_payment_id' => $paymentIntent->id,
                'amount' => $amount,
                'commission_amount' => $commission,
                'status' => $status,
                'customer_email' => $paymentIntent->receipt_email ?? null,
                'metadata' => json_encode([
                    'sid' => $metadata->numok_sid ?? null,
                    'sid2' => $metadata->numok_sid2 ?? null,
                    'sid3' => $metadata->numok_sid3 ?? null
                ])
            ]);
    
            $this->logEvent('conversion_created', [
                'payment_id' => $paymentIntent->id,
                'tracking_code' => $trackingCode,
                'amount' => $amount,
                'commission' => $commission
            ]);
        } catch (\Exception $e) {
            $this->logEvent('conversion_creation_failed', [
                'error' => $e->getMessage(),
                'payment_id' => $paymentIntent->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function handleInvoicePaid($invoice): void {
        // Add initial logging
        $this->logEvent('invoice_paid_processing', [
            'invoice_id' => $invoice->id,
            'subscription_id' => $invoice->subscription,
            'amount' => $invoice->amount_paid
        ]);
    
        // Need to look up the original payment to get tracking info
        $originalPaymentId = $invoice->subscription;
        
        if (!$originalPaymentId) {
            $this->logEvent('invoice_paid_no_subscription', $invoice);
            return;
        }
    
        // Get the original conversion to find partner program
        $originalConversion = Database::query(
            "SELECT c.*, pp.tracking_code, pp.id as partner_program_id, 
                    p.is_recurring, p.commission_type, p.commission_value 
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             JOIN programs p ON pp.program_id = p.id
             WHERE c.stripe_payment_id = ?
             AND pp.status = 'active'
             AND p.status = 'active'
             LIMIT 1",
            [$originalPaymentId]
        )->fetch();
    
        if (!$originalConversion) {
            $this->logEvent('invoice_paid_no_original_conversion', [
                'invoice_id' => $invoice->id,
                'subscription_id' => $originalPaymentId
            ]);
            return;
        }
    
        if (!$originalConversion['is_recurring']) {
            $this->logEvent('invoice_paid_no_recurring', [
                'invoice_id' => $invoice->id,
                'original_conversion_id' => $originalConversion['id']
            ]);
            return;
        }
    
        // Log found original conversion
        $this->logEvent('original_conversion_found', [
            'invoice_id' => $invoice->id,
            'original_conversion' => $originalConversion
        ]);
    
        // Calculate amount in dollars
        $amount = $invoice->amount_paid / 100;
    
        // Calculate commission based on program settings
        $commission = $this->calculateCommission($amount, $originalConversion);
    
        // Determine status (use same logic as original conversion)
        $status = $originalConversion['reward_days'] > 0 ? 'pending' : 'payable';
    
        try {
            // Create new conversion for the recurring payment
            Database::insert('conversions', [
                'partner_program_id' => $originalConversion['partner_program_id'],
                'stripe_payment_id' => $invoice->payment_intent, // Use the new payment intent ID
                'amount' => $amount,
                'commission_amount' => $commission,
                'status' => $status,
                'customer_email' => $invoice->customer_email ?? null,
                'metadata' => $originalConversion['metadata'], // Maintain the original metadata
            ]);
    
            $this->logEvent('recurring_conversion_created', [
                'invoice_id' => $invoice->id,
                'original_conversion_id' => $originalConversion['id'],
                'amount' => $amount,
                'commission' => $commission
            ]);
        } catch (\Exception $e) {
            $this->logEvent('recurring_conversion_creation_failed', [
                'error' => $e->getMessage(),
                'invoice_id' => $invoice->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
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