<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    private CategorieRepository $categorieRepository;
    public function __construct(CategorieRepository $categorieRepository){
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/categories', name: 'app_categories')]
    public function getCategories(): Response
    {

        $categories = $this->categorieRepository->findBy([]);


        return $this->render('categories/index.html.twig', [
            "categories" => $categories
        ]);
    }
}
