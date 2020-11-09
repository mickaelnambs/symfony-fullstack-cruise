<?php

namespace App\Controller\Front;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Constant\MessageConstant;
use App\Controller\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AdController.
 * 
 * @Route("/ads")
 * 
 */
class AdController extends BaseController
{
    private AdRepository $adRepository;

    /**
     * AdController constructeur.
     *
     * @param AdRepository $adRepository
     */
    public function __construct(EntityManagerInterface $em, AdRepository $adRepository)
    {
        parent::__construct($em);
        $this->adRepository = $adRepository;
    }

    /**
     * Permet de recuperer toutes les annonces.
     * 
     * @Route("/", name="ad_index", methods={"POST","GET"})
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('ad/index.html.twig', [
            'ads' => $this->adRepository->findAll()
        ]);
    }

    /**
     * Permet de creer une annonce.
     * 
     * @Route("/new", name="ad_new", methods={"POST","GET"})
     *
     * @param Request $request
     * 
     * @return Response
     */
    public function new(Request $request): Response
    {
        $ad = new Ad();
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            $this->uploadFiles($images, $ad);
            $ad->setUser($this->getUser());

            if ($this->save($ad)) {
                $this->addFlash(
                    MessageConstant::SUCCESS_TYPE,
                    "L'annonce <strong>{$ad->getName()}</strong> a bien été sauvegardée !"
                );
            }
            return $this->redirectToRoute('ad_index');
        }
        return $this->render('ad/new.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier une annonce.
     * 
     * @Route("/{id}/edit", name="ad_edit", methods={"POST","GET"})
     * @Security("is_granted('ROLE_USER') and user === ad.getUser()", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier")
     *
     * @param Request $request
     * 
     * @return Response
     */
    public function edit(Request $request, Ad $ad): Response
    {
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            $this->uploadFiles($images, $ad);
            $ad->setUser($this->getUser());

            if ($this->save($ad)) {
                $this->addFlash(
                    MessageConstant::SUCCESS_TYPE,
                    "L'annonce <strong>{$ad->getName()}</strong> a bien été modifiée !"
                );
            }
            return $this->redirectToRoute('ad_index');
        }
        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher une seule annonce.
     *
     * @Route("/{slug}", name="ad_show")
     * 
     * @return Response
     */
    public function show(Ad $ad): Response
    {
        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }
    

    /**
     * Permet de supprimer une annonce.
     * 
     * @Route("/{id}/delete", name="ad_delete")
     * @Security("is_granted('ROLE_USER') and user === ad.getUser()", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la supprimer")
     *
     * @param Ad $ad
     * 
     * @return Response
     */
    public function delete(Ad $ad): Response
    {
        if ($this->remove($ad)) {
            $this->addFlash(
                MessageConstant::SUCCESS_TYPE,
                "L'annonce <strong>{$ad->getName()}</strong> a bien été supprimée !"
            );
        }
        return $this->redirectToRoute('ad_index');
    }
}
