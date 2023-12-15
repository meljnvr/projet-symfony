<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class AdController extends AbstractController
{
    
    #[Route('/ad/new', name: 'app_ad_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em, LoggerInterface $logger): Response
    {
        $ad = new Ad();
        
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            $ad = $form->getData();
            $ad->setUser($this->getUser());
            $em->persist($ad);
            $em->flush();
            $this->addFlash('success','Annonce bien ajoutée');
            $logger->info('Annonce bien ajoutée', ['id'=> $ad->getId(),'title'=> $ad->getTitle()]);
        }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'AdController',
        ]);
    }

    #[Route('/ad', name: 'app_ad')]
    public function index(AdRepository $adRepository): Response
    {
        return $this->render('ad/index.html.twig', [
            'ads' => $adRepository->findAll(),
        ]);
    }

    #[Route('ad/{id}', name: 'ad_show', methods: ['GET'])]
    public function show(Ad $ad): Response
    {
        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }

    #[Route('/{id}/edit', name: 'ad_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('AD_EDIT', ad)")]
    public function edit(Request $request, Ad $ad, AdRepository $adRepository): Response
    {
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adRepository->save($ad, true);

            return $this->redirectToRoute('app_ad', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'ad_delete', methods: ['POST'])]
    #[Security("is_granted('AD_DELETE', ad)")]
    public function delete(Request $request, Ad $ad, AdRepository $adRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ad->getId(), $request->request->get('_token'))) {
            $adRepository->remove($ad, true);
        }

        return $this->redirectToRoute('app_ad', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/ad/{id}.{extension}', name: 'ad_json', requirements: [
        'extension' => 'json|yaml',
    ],)]
    public function toJson(Ad $ad, SerializerInterface $serializer, string $extension): Response
    {
        // a régler, groupes
        return new Response($serializer->serialize($ad, $extension));

        
    }
}
