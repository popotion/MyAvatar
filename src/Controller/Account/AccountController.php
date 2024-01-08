<?php

namespace App\Controller\Account;

use App\Entity\User;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
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
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(
        Request $request,
        SluggerInterface $slugger,
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class, $user, [
            'translator' => $this->translator,
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $profilePictureFile */
            $profilePictureFile = $form->get('pictureProfile')->getData();

            /** @var User $userData */
            $userData = $form->getData();

            if ($profilePictureFile) {
                $newFilename = md5($userData->getEmail()).'.'.$profilePictureFile->guessExtension();

                try {
                    $profilePictureFile->move(
                        $this->getParameter('profilePictureDirectory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $userData->setPictureProfileName($newFilename);
            }

            $this->entityManager->persist($userData);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('account.flash.updated', [], 'app'));
            return $this->redirectToRoute(RouteCollection::ACCOUNT->prefixed());
        }


        $link = $this->generateUrl(
            'app_avatar', [
                'id'=>md5($user->getEmail())
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return $this->render('account/account.html.twig', [
            'form' => $form->createView(),
            'link' => $link,
        ]);
    }
}