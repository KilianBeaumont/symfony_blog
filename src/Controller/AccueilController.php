<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{

    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository){
        $this->articleRepository = $articleRepository;
    }
    #[Route('/', name: 'app_accueil')]
    public function getArticles(): Response
    {

        $articles = $this->articleRepository->findBy([],['createdat'=>'DESC'],limit: 10);


        return $this->render('accueil/index.html.twig',[
            "articles" => $articles
        ]);

    }
}
