<?php

namespace App\Controller\Account;

use App\Controller\Front\RouteCollection as FrontRouteCollection;
use App\Entity\User;
use App\Form\UserFormType;
use App\Messenger\User\UpdateUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(
    path: '/account',
    name: RouteCollection::ACCOUNT->value,
)]
#[IsGranted('IS_AUTHENTICATED')]
class AccountController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(
        Request $request,
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $currentEmail = $user->getEmail();

        $form = $this->createForm(UserFormType::class, $user, [
            'translator' => $this->translator,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $profilePictureFile */
            $profilePictureFile = $form->get('pictureProfile')->getData();

            /** @var User $userData */
            $userData = $form->getData();

            $this->messageBus->dispatch(
                new UpdateUser(
                    $currentEmail,
                    $userData,
                    $profilePictureFile,
                    $this->getParameter('profilePictureDirectory'),
                )
            );

            $this->addFlash('success', $this->translator->trans('account.flash.updated', [], 'app'));
            return $this->redirectToRoute(RouteCollection::ACCOUNT->prefixed());
        }


        $link = $this->generateUrl(
            FrontRouteCollection::AVATAR->prefixed(),
            [
                'id' => md5($user->getEmail())
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return $this->render('account/account.html.twig', [
            'form' => $form->createView(),
            'link' => $link,
        ]);
    }
}