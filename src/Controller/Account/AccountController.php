<?php

namespace App\Controller\Account;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(
    path: '/account',
    name: RouteCollection::ACCOUNT->value,
)]
#[IsGranted('IS_AUTHENTICATED')]
class AccountController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('account/account.html.twig');
    }
}