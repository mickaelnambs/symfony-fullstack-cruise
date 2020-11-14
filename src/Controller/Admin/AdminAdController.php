<?php

namespace App\Controller\Admin;

use App\Constant\MessageConstant;
use App\Repository\AdRepository;
use App\Controller\BaseController;
use App\Entity\Ad;
use App\Form\AdType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminAdController.
 * 
 * @Route("/admin/ads")
 */
class AdminAdController extends BaseController
{
    private AdRepository $adRepository;

    /**
     * AdminAdController constructor.
     *
     * @param EntityManagerInterface $em
     * @param AdRepository $adRepository
     */
    public function __construct(EntityManagerInterface $em, AdRepository $adRepository)
    {
        parent::__construct($em);
        $this->adRepository = $adRepository;
    }

    /**
     * Permet de lister toutes les annonces.
     * 
     * @Route("/", name="admin_ad_index", methods={"POST","GET"})
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('admin/ad/index.html.twig', [
            'ads' => $this->adRepository->findAll()
        ]);
    }

    /**
     * Permet de creer une nouvelle annonce.
     * 
     * @Route("/new", name="admin_ad_new", methods={"POST","GET"})
     *
     * @param Request $request
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
            return $this->render('admin/ad/new.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    /**
     * Permet de modifier une annonce.
     * 
     * @Route("/{id}/edit", name="admin_ad_edit", methods={"POST","GET"})
     *
     * @param Ad $ad
     * @param Request $request
     * @return Response
     */
    public function edit(Ad $ad, Request $request): Response
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
            return $this->render('admin/ad/edit.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    /**
     * Permet de supprimer une annonce.
     * 
     * @Route("/{id}/delete", name="admin_ad_delete")
     *
     * @param Ad $ad
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
        return $this->redirectToRoute('admin_ad_index');
    }
}
