<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/',
    name: RouteCollection::HOMEPAGE->value,
    requirements: [
        '_locale' => 'en|fr',
    ],
    methods: ['GET'],
)]
class HomepageController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('front/homepage.html.twig');
    }
}