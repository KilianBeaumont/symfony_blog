<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    private ArticleRepository $articleRepository;

    // Demander à Symfony d'infecter une instance de ArticleRepository
    // à la création du contrôleur
    public function __construct(ArticleRepository $articleRepository){
        $this->articleRepository = $articleRepository;
    }

    #[Route('/articles', name: 'app_articles')]

    // A l'appel de la méthode, SYMFONY va
    // créer un objet de la classe repository et le passer
    // en paramètre de la méthode
    // Mécanisme : l'injection de dépendances

    public function getArticles(): Response
    {
        // Récupérer les informations dans la bdd
        // Le controller fait appel au modèle (une classe du modèle)
        // afin de récupérer la liste des articles
        // $repository = new ArticleRepository();
        $articles = $this->articleRepository->findBy([],['createdat'=>'DESC']);


        return $this->render('article/index.html.twig',[
            "articles" => $articles
        ]);

    }

    #[Route('/articles/{slug}', name: 'app_article_slug')]

    public function getArticle($slug): Response
    {

        $articles = $this->articleRepository->findOneBy(["slug"=>$slug]);

        return $this->render('article/article.html.twig',[
            "articles" => $articles
        ]);

    }
}
