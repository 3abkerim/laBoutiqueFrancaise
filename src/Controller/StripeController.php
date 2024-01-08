<?php

namespace App\Controller;

use App\Class\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\RedirectResponse;


class StripeController extends AbstractController
{
    #[Route('/commande/checkout', name: 'checkout')]
    public function index(Cart $cart)
    {
        $YOUR_DOMAIN = 'http://127.0.0.1:8000/';
        $product_for_stripe = [];

        try {
            foreach ($cart->getFull() as $product) {
                $image = [$YOUR_DOMAIN . "/uploads/" . $product['product']->getIllustration()];

                $product_for_stripe[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $product['product']->getPrice(),
                        'product_data' => [
                            'name' => $product['product']->getName(),
                            'images' => $image,
                        ],
                    ],
                    'quantity' => $product['quantity'],

                ];
            }


            $stripeSecretKey = 'sk_test_51OWDyMI94W7P88LUJO6Jz3jWDsfqnkgacGDep7hjYTfBGxU3vfInnI468pw4EHLOBj9vU832HmNPPXfpptQrx8uT00vHOE9Cxd';
            Stripe::setApiKey($stripeSecretKey);


            $checkout_session = Session::create([
                'line_items' => [
                    $product_for_stripe
                ],
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/success.html',
                'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
            ]);

            // header("HTTP/1.1 303 See Other");
            // header("Location: " . $checkout_session->url);
            return new RedirectResponse($checkout_session->url, 303);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle Stripe API errors
            // Log or display an error message
            return new RedirectResponse('/error.html', 500);
        } catch (\Exception $e) {
            // Handle other exceptions
            // Log or display an error message
            return new RedirectResponse('/error.html', 500);
        }
    }
}
