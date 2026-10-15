<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EventRepository $repo): Response
    {
        $category = $request->query->get('categorie');

        return $this->render('home/index.html.twig', [
            'events' => $repo->findUpcomingValidated($category),
            'category' => $category,
        ]);
    }
}