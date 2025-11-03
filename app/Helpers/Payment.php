<?php

namespace App\Helpers;

use Stripe\Stripe;
use Stripe\Charge;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payment as PayPalPayment;
use PayPal\Api\PaymentExecution;

class Payment
{
    private static $config;

    /**
     * Initialize payment system
     */
    public static function init()
    {
        self::$config = require ROOT_PATH . '/config/config.php';
    }

    /**
     * Create Stripe charge
     */
    public static function createStripeCharge($amount, $currency, $token, $description)
    {
        if (!isset(self::$config['payments']['stripe']['enabled']) || 
            !self::$config['payments']['stripe']['enabled']) {
            throw new \Exception('Stripe is not enabled');
        }

        Stripe::setApiKey(self::$config['payments']['stripe']['secret_key']);

        try {
            $charge = Charge::create([
                'amount' => $amount * 100, // Convert to cents
                'currency' => strtolower($currency),
                'source' => $token,
                'description' => $description,
            ]);

            return [
                'success' => true,
                'transaction_id' => $charge->id,
                'status' => $charge->status,
                'data' => $charge
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create Stripe payment intent
     */
    public static function createStripePaymentIntent($amount, $currency, $metadata = [])
    {
        if (!isset(self::$config['payments']['stripe']['enabled']) || 
            !self::$config['payments']['stripe']['enabled']) {
            throw new \Exception('Stripe is not enabled');
        }

        Stripe::setApiKey(self::$config['payments']['stripe']['secret_key']);

        try {
            $intent = \Stripe\PaymentIntent::create([
                'amount' => $amount * 100,
                'currency' => strtolower($currency),
                'metadata' => $metadata,
            ]);

            return [
                'success' => true,
                'client_secret' => $intent->client_secret,
                'intent_id' => $intent->id
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get PayPal API Context
     */
    private static function getPayPalContext()
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                self::$config['payments']['paypal']['client_id'],
                self::$config['payments']['paypal']['secret']
            )
        );

        $apiContext->setConfig([
            'mode' => self::$config['payments']['paypal']['mode'],
        ]);

        return $apiContext;
    }

    /**
     * Create PayPal payment
     */
    public static function createPayPalPayment($amount, $currency, $returnUrl, $cancelUrl, $description)
    {
        if (!isset(self::$config['payments']['paypal']['enabled']) || 
            !self::$config['payments']['paypal']['enabled']) {
            throw new \Exception('PayPal is not enabled');
        }

        try {
            $apiContext = self::getPayPalContext();

            $payer = new \PayPal\Api\Payer();
            $payer->setPaymentMethod('paypal');

            $amountObj = new \PayPal\Api\Amount();
            $amountObj->setTotal($amount);
            $amountObj->setCurrency($currency);

            $transaction = new \PayPal\Api\Transaction();
            $transaction->setAmount($amountObj);
            $transaction->setDescription($description);

            $redirectUrls = new \PayPal\Api\RedirectUrls();
            $redirectUrls->setReturnUrl($returnUrl);
            $redirectUrls->setCancelUrl($cancelUrl);

            $payment = new PayPalPayment();
            $payment->setIntent('sale');
            $payment->setPayer($payer);
            $payment->setTransactions([$transaction]);
            $payment->setRedirectUrls($redirectUrls);

            $payment->create($apiContext);

            return [
                'success' => true,
                'payment_id' => $payment->getId(),
                'approval_url' => $payment->getApprovalLink()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Execute PayPal payment
     */
    public static function executePayPalPayment($paymentId, $payerId)
    {
        try {
            $apiContext = self::getPayPalContext();

            $payment = PayPalPayment::get($paymentId, $apiContext);
            
            $execution = new PaymentExecution();
            $execution->setPayerId($payerId);

            $result = $payment->execute($execution, $apiContext);

            return [
                'success' => true,
                'transaction_id' => $result->getId(),
                'status' => $result->getState(),
                'data' => $result
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Record payment in database
     */
    public static function recordPayment($userId, $listingId, $amount, $currency, $gateway, $transactionId, $status, $metadata = [])
    {
        $db = \App\Core\Database::getInstance();

        return $db->insert('payments', [
            'user_id' => $userId,
            'listing_id' => $listingId,
            'amount' => $amount,
            'currency' => $currency,
            'gateway' => $gateway,
            'transaction_id' => $transactionId,
            'status' => $status,
            'metadata' => json_encode($metadata),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Update payment status
     */
    public static function updatePaymentStatus($paymentId, $status)
    {
        $db = \App\Core\Database::getInstance();

        return $db->update('payments', [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $paymentId]);
    }

    /**
     * Get payment by ID
     */
    public static function getPayment($paymentId)
    {
        $db = \App\Core\Database::getInstance();
        return $db->fetch('SELECT * FROM payments WHERE id = :id', ['id' => $paymentId]);
    }

    /**
     * Get user payments
     */
    public static function getUserPayments($userId)
    {
        $db = \App\Core\Database::getInstance();
        return $db->fetchAll(
            'SELECT * FROM payments WHERE user_id = :user_id ORDER BY created_at DESC',
            ['user_id' => $userId]
        );
    }

    /**
     * Refund payment (Stripe)
     */
    public static function refundStripePayment($chargeId, $amount = null)
    {
        Stripe::setApiKey(self::$config['payments']['stripe']['secret_key']);

        try {
            $refundData = ['charge' => $chargeId];
            
            if ($amount !== null) {
                $refundData['amount'] = $amount * 100;
            }

            $refund = \Stripe\Refund::create($refundData);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'status' => $refund->status
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

// Initialize on include
Payment::init();

