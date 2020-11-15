<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\ProfileType;
use App\Form\RegistrationType;
use App\Constant\MessageConstant;
use App\Controller\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($em);
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
            $file = $form->get('image')->getData();
            $this->uploadFile($file, $user);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
            
            if ($this->save($user)) {
                $this->addFlash(
                    MessageConstant::SUCCESS_TYPE,
                    "Votre compte a bien été créé ! Vous pouvez maintenant vous connecter !"
                );
            }
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

    /**
     * Permet de modifier le profil.
     * 
     * @Route("/account/profile", name="account_profile", methods={"POST","GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function profile(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            $this->uploadFile($file, $user);

            if ($this->save($user)) {
                $this->addFlash(
                    'success',
                    "Les données du profil ont été enregistrée avec succès !"
                );
            }
        }
        return $this->render('account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecté.
     *
     * @Route("/account", name="user_show")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function myAccount(): Response
    {
        return $this->render('account/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
