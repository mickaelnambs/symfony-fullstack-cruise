<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Constant\MessageConstant;
use App\Controller\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class AccountController.
 */
class AccountController extends BaseController
{
    /** @var UserPasswordEncoderInterface */
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * AccountController constructeur.
     *
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($entityManager);
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Permet de creer un compte utilisateur.
     * 
     * @Route("/register", name="account_register", methods={"POST","GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function registration(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $this->save($user);
            $this->addFlash(
                MessageConstant::SUCCESS_TYPE,
                "Votre compte a bien été créé ! Vous pouvez maintenant vous connecter !"
            );
            return $this->redirectToRoute('account_login');
        }
        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Login.
     * 
     * @Route("/login", name="account_login", methods={"POST","GET"})
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('account/login.html.twig', [
            'username' => $authenticationUtils->getLastUsername(),
            'hasError' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * Logout.
     * 
     * @Route("/logout", name="account_logout", methods={"POST","GET"})
     *
     * @return void
     */
    public function logout()
    {
        // Vide ..
    }
}
