<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    #[Route('/{id}/email', name: 'email_send')]
    public function sendEmail(MailerInterface $mailer, Request $request, Ad $ad, AdRepository $adRepository, LoggerInterface $logger): Response
    {

            $user = $ad->getUser();

            if (!$user) {
                throw $this->createNotFoundException('User not found');
            }

            $userEmail = $user->getEmail();

        $email =
        
        (new TemplatedEmail())
            ->from('mel.jnvr@gmail.com')
            ->to($userEmail)
            ->subject('Your add')
            ->htmlTemplate('mail/template.html.twig')
            ->context([
                'ad' => $ad,
                'userMail' => $this->getUser()->getEmail()
            ]);

        $mailer->send($email);
        $this->addFlash('notice', 'Mail sent !');
        

        $logger->info('Mail sent', ['email' => $userEmail]);

        return $this->redirectToRoute('app_ad');
        // ...
    }
}
