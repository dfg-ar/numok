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
            $this->logEvent('checkout_completed_invalid_tracking', $session);
            return;
        }

        // Calculate commission status
        $status = $partnerProgram['reward_days'] > 0 ? 'pending' : 'payable';

        // Store conversion
        try {
            // Check session mode - for subscription mode, use subscription ID instead of payment_intent
            $paymentId = $session->payment_intent;
            if ($session->mode === 'subscription' && !empty($session->subscription)) {
                $paymentId = $session->subscription;
            }
            
            if (!$paymentId) {
                $this->logEvent('checkout_completed_missing_payment_id', [
                    'session_id' => $session->id,
                    'mode' => $session->mode
                ]);
                $paymentId = $session->id; // Fallback to session ID if nothing else is available
            }

            Database::insert('conversions', [
                'partner_program_id' => $partnerProgram['id'],
                'stripe_payment_id' => $paymentId,
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
                'payment_id' => $paymentId,
                'tracking_code' => $trackingCode,
                'amount' => $session->amount_total / 100
            ]);
        } catch (\Exception $e) {
            $this->logEvent('conversion_creation_failed', [
                'error' => $e->getMessage(),
                'payment_id' => $paymentId ?? null,
                'session_id' => $session->id
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
            'amount' => $invoice->amount_paid,
            'subscription_metadata' => $invoice->subscription_details->metadata ?? null
        ]);
    
        // Get tracking code from subscription metadata
        $metadata = $invoice->subscription_details->metadata ?? new \stdClass();
        $trackingCode = $metadata->numok_tracking_code ?? null;
    
        // If no tracking code in subscription metadata, try line items
        if (!$trackingCode && !empty($invoice->lines->data)) {
            $lineMetadata = $invoice->lines->data[0]->metadata ?? new \stdClass();
            $trackingCode = $lineMetadata->numok_tracking_code ?? null;
        }
    
        if (!$trackingCode) {
            $this->logEvent('invoice_paid_no_tracking', $invoice);
            return;
        }
    
        // Get partner program
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
            $this->logEvent('invoice_paid_invalid_tracking', [
                'invoice_id' => $invoice->id,
                'tracking_code' => $trackingCode
            ]);
            return;
        }
    
        if (!$partnerProgram['is_recurring']) {
            $this->logEvent('invoice_paid_no_recurring', [
                'invoice_id' => $invoice->id,
                'partner_program' => $partnerProgram
            ]);
            return;
        }
    
        // Calculate amount in dollars
        $amount = $invoice->amount_paid / 100;
    
        // Calculate commission
        $commission = $this->calculateCommission($amount, $partnerProgram);
    
        // Determine status
        $status = $partnerProgram['reward_days'] > 0 ? 'pending' : 'payable';
    
        try {
            // Create conversion for the subscription payment
            Database::insert('conversions', [
                'partner_program_id' => $partnerProgram['id'],
                'stripe_payment_id' => $invoice->payment_intent,
                'amount' => $amount,
                'commission_amount' => $commission,
                'status' => $status,
                'customer_email' => $invoice->customer_email ?? null,
                'metadata' => json_encode([
                    'sid' => $metadata->numok_sid ?? null,
                    'sid2' => $metadata->numok_sid2 ?? null,
                    'sid3' => $metadata->numok_sid3 ?? null,
                    'subscription_id' => $invoice->subscription
                ])
            ]);
    
            $this->logEvent('subscription_conversion_created', [
                'invoice_id' => $invoice->id,
                'tracking_code' => $trackingCode,
                'amount' => $amount,
                'commission' => $commission,
                'subscription_id' => $invoice->subscription
            ]);
        } catch (\Exception $e) {
            $this->logEvent('subscription_conversion_failed', [
                'error' => $e->getMessage(),
                'invoice_id' => $invoice->id,
                'stack_trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function calculateCommission(float $amount, array $partnerProgram): float {
        if (!isset($partnerProgram['commission_type']) || !isset($partnerProgram['commission_value'])) {
            // Log the missing data for debugging
            $this->logEvent('commission_calculation_error', [
                'error' => 'Missing commission data in partner program',
                'partner_program' => $partnerProgram
            ]);
            return 0.0; // Default to zero commission if data is missing
        }
        
        if ($partnerProgram['commission_type'] === 'percentage') {
            return round($amount * ($partnerProgram['commission_value'] / 100), 2);
        }
        return (float)$partnerProgram['commission_value'];
    }

    private function logEvent(string $type, $data): void {
        Database::insert('logs', [
            'type' => $type,
            'message' => is_string($data) ? $data : json_encode($data),
            'context' => is_string($data) ? null : json_encode($data)
        ]);
    }
}