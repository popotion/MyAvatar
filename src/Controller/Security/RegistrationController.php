<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Messenger\User\CreateUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route(
        path: '/register',
        name: RouteCollection::REGISTER->value,
        requirements: [
            '_locale' => 'en|fr',
        ],
    )]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'translator' => $this->translator,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $plainPassword = $form->get('plainPassword')->getData();
            $repeatPlainPassword = $form->get('repeatPlainPassword')->getData();

            if ($repeatPlainPassword != $plainPassword) {
                $this->addFlash('danger', $this->translator->trans('security.register.error.passwordNotCorrectRepeat', [], 'app'));
                return $this->redirectToRoute(RouteCollection::REGISTER->prefixed());
            }

            $this->messageBus->dispatch(
                new CreateUser(
                    $user,
                    $plainPassword,
                )
            );

            $this->addFlash('success', $this->translator->trans('security.register.flash.created', [], 'app'));
            return $this->redirectToRoute(RouteCollection::LOGIN->prefixed());
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
