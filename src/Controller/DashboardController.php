<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @Route("/admin", name="dashboard")
     */
    public function index()
    {
        return $this->render('admin/dashboard/inicio.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
