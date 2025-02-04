<?php

// src/Controller/TestEmailController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class TestEmailController extends AbstractController
{
    #[Route('/test-email', name: 'test_email')]
    public function sendTestEmail(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('contact@jaimelabouffe.pydonze.fr')
            ->to('pyd3.14@gmail.com')
            ->subject('Test Email')
            ->text('Ceci est un email de test envoyé depuis Symfony.');

        $mailer->send($email);

        return $this->json(['status' => 'Email envoyé']);
    }
}
