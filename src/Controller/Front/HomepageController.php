<?php

namespace App\Controller\Front;

use App\Form\SeeMyAvatarHomepageFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/',
    name: RouteCollection::HOMEPAGE->value,
    requirements: [
        '_locale' => 'en|fr',
    ],
    methods: ['GET', 'POST'],
)]
class HomepageController extends AbstractController
{
    public function __invoke(
        Request $request,
    ): Response {
        $form = $this->createForm(SeeMyAvatarHomepageFormType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emailUser = md5($form->get('email')->getData());

            return $this->redirectToRoute(
                'app_avatar',
                [
                    'id' => $emailUser,
                ]
            );
        }

        return $this->render('front/homepage.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}