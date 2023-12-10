<?php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/security/logout',
    name: RouteCollection::LOGOUT->value,
    methods: ['GET'],
)]
class LogoutController extends AbstractController
{
    public function __invoke(): never
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}