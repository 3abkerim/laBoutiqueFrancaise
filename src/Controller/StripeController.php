<?php

namespace App\Controller;

use App\Class\Cart;
use App\Entity\Order;
use App\Entity\Products;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\RedirectResponse;


class StripeController extends AbstractController
{
    #[Route('/commande/checkout/{reference}', name: 'checkout')]
    public function index(EntityManagerInterface $entityManager, Cart $cart, $reference)
    {
        $YOUR_DOMAIN = 'http://127.0.0.1:8000/';
        $product_for_stripe = [];

        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);

        // dd($order->getOrderDetails()->getValues());

        if (!$order) {
            return new RedirectResponse('/error.html', 500);
        }

        try {
            foreach ($order->getOrderDetails()->getValues() as $product) {
                $product_object = $entityManager->getRepository(Products::class)->findOneByName($product->getProduct());
                $image = [$YOUR_DOMAIN . "/uploads/" . $product_object->getIllustration()];

                $product_for_stripe[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $product->getPrice(),
                        'product_data' => [
                            'name' => $product->getProduct(),
                            'images' => $image,
                        ],
                    ],
                    'quantity' => $product->getQuantity(),

                ];
            }


            $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $order->getCarrierPrice() * 100,
                    'product_data' => [
                        'name' => $order->getCarrierName(),
                        'images' => [$YOUR_DOMAIN],
                    ],
                ],
                'quantity' => 1,

            ];



            $stripeSecretKey = 'sk_test_51OWDyMI94W7P88LUJO6Jz3jWDsfqnkgacGDep7hjYTfBGxU3vfInnI468pw4EHLOBj9vU832HmNPPXfpptQrx8uT00vHOE9Cxd';
            Stripe::setApiKey($stripeSecretKey);


            $checkout_session = Session::create([
                'customer_email' => $order->getUser()->getEmail(),
                'line_items' => [
                    $product_for_stripe
                ],
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . 'commande/merci/{CHECKOUT_SESSION_ID}',
                'cancel_url' => $YOUR_DOMAIN . 'commande/erreur/{CHECKOUT_SESSION_ID}',
            ]);

            $order->setStripeSessionId($checkout_session->id);
            $entityManager->flush();

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
