<?php

namespace App\Controller;

use App\Class\Cart;
use App\Class\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/commande/merci/{stripeSessionId}', name: 'app_order_validate')]
    public function index($stripeSessionId, Cart $cart): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($order->getState() == 0) {
            // Vider la sessio cart
            $cart->remove();
            // Modifier le statut isPaid de notre commande en mettant 1
            $order->setState(1);
            $this->entityManager->flush();
            // Envoyer un mail à notre client pour lui confirmer sa commande
            $mail = new Mail();
            $content = 'Bonjour ' . $order->getUser()->getFirstName() . ', Merci pour votre commande';
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), 'Votre commande sur La Boutique Française est bien validé', $content);
        }



        // Afficher les quelques infos de la commande de l'utilisateur


        // dd($order);
        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
