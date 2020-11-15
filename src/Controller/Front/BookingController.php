<?php

namespace App\Controller\Front;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use App\Constant\MessageConstant;
use App\Controller\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BookingController.
 */
class BookingController extends BaseController
{   
    /**
     * BookingController constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);
    }

    /**
     * Permet de faire une réservation.
     * 
     * @Route("/ads/{id}/book", name="booking_new", methods={"POST","GET"})
     *
     * @param Ad $ad
     * @param Request $request
     * 
     * @return Response
     */
    public function book(Ad $ad, Request $request): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setAd($ad)
                ->setBooker($this->getUser());

            if ($ad->getAvailablePlaces() > 0) {
                $this->save($booking);
                return $this->redirectToRoute('booking_show', ['id' => $booking->getId(), 'withAlert' => true]);
            } else {
                $this->addFlash(
                    MessageConstant::ERROR_TYPE,
                    "Les places que vous avez choisi ne peuvent être réservées !"
                );
            }
        }
        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher une annonce.
     * 
     * @Route("/{id}/booking", name="booking_show", methods={"POST","GET"}, requirements={"slug": "[a-z0-9\-]*"})
     *
     * @param Booking $booking
     * @param Request $request
     * 
     * @return Response
     */
    public function show(Booking $booking, Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAd($booking->getAd())
                ->setAuthor($this->getUser());

            if ($this->save($comment)) {
                $this->addFlash(
                    MessageConstant::SUCCESS_TYPE,
                    "Votre commentaire a bien été pris en compte !"
                );
            }
        }
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }
}
