<?php

namespace App\Controller\Admin;

use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminDashboardController.
 */
class AdminDashboardController extends AbstractController
{   
    /**
     * Permet d'afficher le tableau de bord.
     * 
     * @Route("/admin", name="admin_dashboard_index", methods={"POST","GET"})
     *
     * @param StatsService $statsService
     * @return Response
     */
    public function index(StatsService $statsService): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $statsService->getStats()
        ]);
    }
}
