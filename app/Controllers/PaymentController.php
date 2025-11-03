<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Payment;
use App\Helpers\Security;

class PaymentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAuth();
    }

    /**
     * Feature listing payment
     */
    public function featureListing($listingId)
    {
        $listingModel = $this->model('Listing');
        $listing = $listingModel->find($listingId);
        
        if (!$listing || $listing['user_id'] != $_SESSION['user_id']) {
            $this->json(['error' => 'Unauthorized'], 403);
        }
        
        $data = [
            'title' => 'Feature Your Listing',
            'listing' => $listing,
            'amount' => 9.99, // Featured listing price
            'stripe_key' => $_ENV['STRIPE_PUBLIC_KEY'] ?? ''
        ];
        
        $this->view('payment/feature-listing', $data);
    }

    /**
     * Process Stripe payment
     */
    public function processStripe()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('');
        }
        
        $token = $_POST['stripeToken'] ?? '';
        $listingId = intval($_POST['listing_id'] ?? 0);
        $amount = floatval($_POST['amount'] ?? 0);
        
        if (empty($token) || $listingId <= 0 || $amount <= 0) {
            $this->json(['error' => 'Invalid payment data'], 400);
        }
        
        // Process payment
        $result = Payment::createStripeCharge(
            $amount,
            'USD',
            $token,
            "Feature listing #$listingId"
        );
        
        if ($result['success']) {
            // Record payment
            $paymentId = Payment::recordPayment(
                $_SESSION['user_id'],
                $listingId,
                $amount,
                'USD',
                'stripe',
                $result['transaction_id'],
                'completed'
            );
            
            // Mark listing as featured
            $listingModel = $this->model('Listing');
            $listingModel->markFeatured($listingId, true);
            
            // Set expiry date (30 days)
            $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
            $listingModel->update($listingId, ['expires_at' => $expiry]);
            
            $this->json([
                'success' => true,
                'message' => 'Payment successful! Your listing is now featured.',
                'payment_id' => $paymentId
            ]);
        } else {
            $this->json([
                'error' => $result['error'] ?? 'Payment failed'
            ], 400);
        }
    }

    /**
     * Create PayPal payment
     */
    public function createPayPalPayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $listingId = intval($_POST['listing_id'] ?? 0);
        $amount = floatval($_POST['amount'] ?? 0);
        
        if ($listingId <= 0 || $amount <= 0) {
            $this->json(['error' => 'Invalid payment data'], 400);
        }
        
        $returnUrl = BASE_URL . '/payment/paypal-success';
        $cancelUrl = BASE_URL . '/payment/paypal-cancel';
        
        $result = Payment::createPayPalPayment(
            $amount,
            'USD',
            $returnUrl,
            $cancelUrl,
            "Feature listing #$listingId"
        );
        
        if ($result['success']) {
            // Store payment info in session
            $_SESSION['pending_payment'] = [
                'listing_id' => $listingId,
                'amount' => $amount,
                'payment_id' => $result['payment_id']
            ];
            
            $this->json([
                'success' => true,
                'approval_url' => $result['approval_url']
            ]);
        } else {
            $this->json([
                'error' => $result['error'] ?? 'Payment creation failed'
            ], 400);
        }
    }

    /**
     * PayPal success callback
     */
    public function paypalSuccess()
    {
        $paymentId = $_GET['paymentId'] ?? '';
        $payerId = $_GET['PayerID'] ?? '';
        
        if (empty($paymentId) || empty($payerId)) {
            $this->redirect('dashboard?error=payment_failed');
        }
        
        // Execute payment
        $result = Payment::executePayPalPayment($paymentId, $payerId);
        
        if ($result['success']) {
            $pendingPayment = $_SESSION['pending_payment'] ?? null;
            
            if ($pendingPayment) {
                // Record payment
                Payment::recordPayment(
                    $_SESSION['user_id'],
                    $pendingPayment['listing_id'],
                    $pendingPayment['amount'],
                    'USD',
                    'paypal',
                    $result['transaction_id'],
                    'completed'
                );
                
                // Mark listing as featured
                $listingModel = $this->model('Listing');
                $listingModel->markFeatured($pendingPayment['listing_id'], true);
                
                // Set expiry date
                $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
                $listingModel->update($pendingPayment['listing_id'], ['expires_at' => $expiry]);
                
                unset($_SESSION['pending_payment']);
            }
            
            $this->redirect('dashboard?success=payment_completed');
        } else {
            $this->redirect('dashboard?error=payment_execution_failed');
        }
    }

    /**
     * PayPal cancel callback
     */
    public function paypalCancel()
    {
        unset($_SESSION['pending_payment']);
        $this->redirect('dashboard?info=payment_cancelled');
    }

    /**
     * Payment history
     */
    public function history()
    {
        $payments = Payment::getUserPayments($_SESSION['user_id']);
        
        $data = [
            'title' => 'Payment History',
            'payments' => $payments
        ];
        
        $this->view('payment/history', $data);
    }

    /**
     * Webhook handler for Stripe
     */
    public function stripeWebhook()
    {
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        
        // Verify webhook signature
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $_ENV['STRIPE_WEBHOOK_SECRET'] ?? ''
            );
            
            // Handle different event types
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    // Update payment status
                    break;
                    
                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    // Handle failed payment
                    break;
                    
                case 'charge.refunded':
                    $charge = $event->data->object;
                    // Handle refund
                    break;
            }
            
            http_response_code(200);
        } catch (\Exception $e) {
            http_response_code(400);
            exit;
        }
    }
}

