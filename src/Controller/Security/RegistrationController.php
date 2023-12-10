<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Controller\Front\RouteCollection as RouteCollectionFront;

class RegistrationController extends AbstractController
{
   public function __construct(
       private readonly EmailVerifier $emailVerifier,
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
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'translator' => $this->translator,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            //TODO: Remove this line for emailing verifier
            $user->setIsVerified(true);
            //TODO: Remove this line for emailing verifier

            $entityManager->persist($user);
            $entityManager->flush();

            /*$this->emailVerifier->sendEmailConfirmation(RouteCollection::EMAIL_REGISTER->prefixed(), $user,
                (new TemplatedEmail())
                    ->from(new Address('noreply@popotion.fr', 'NoReply Popotion'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('security/confirmation_email.html.twig')
            );*/

            return $this->redirectToRoute(RouteCollectionFront::HOMEPAGE->prefixed());
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route(
        path: '/verify/email',
        name: RouteCollection::EMAIL_REGISTER->value,
    )]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute(RouteCollection::REGISTER->prefixed());
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute(RouteCollection::REGISTER->prefixed());
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute(RouteCollection::REGISTER->prefixed());
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute(RouteCollection::REGISTER->prefixed());
    }
}
