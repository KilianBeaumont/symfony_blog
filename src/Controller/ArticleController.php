<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\ArticleType;
use App\Form\CommentaireType;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    private ArticleRepository $articleRepository;
    private CommentaireRepository $commentaireRepository;

    // Demander à Symfony d'infecter une instance de ArticleRepository
    // à la création du contrôleur
    public function __construct(ArticleRepository $articleRepository, CommentaireRepository $commentaireRepository){
        $this->articleRepository = $articleRepository;
        $this->commentaireRepository = $commentaireRepository;
    }

    #[Route('/articles', name: 'app_articles')]
    // A l'appel de la méthode, SYMFONY va
    // créer un objet de la classe repository et le passer
    // en paramètre de la méthode
    // Mécanisme : l'injection de dépendances
    public function getArticles(PaginatorInterface $paginator, Request $request): Response
    {
        // Récupérer les informations dans la bdd
        // Le controller fait appel au modèle (une classe du modèle)
        // afin de récupérer la liste des articles
        // $repository = new ArticleRepository();

        // Mise en place de la pagination
        $articles = $paginator->paginate(
            $this->articleRepository->findBy(['publie'=>'true'],['createdat'=>'DESC']), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('article/index.html.twig',[
            "articles" => $articles
        ]);
    }

    #[Route('/articles/{slug}', name: 'app_article_slug')]
    public function getArticle($slug): Response
    {
        $articles = $this->articleRepository->findOneBy(["slug"=>$slug]);

        return $this->render('article/article.html.twig',[
            "article" => $articles
        ]);
    }

    #[Route('/articles/nouveau', name: 'app_articles_nouveau',methods: ['GET','POST'], priority: 1)]
    public function insert(SluggerInterface $slugger, Request $request) : Response {
        $article = new Article();
        // Création du formulaire
        $formArticle = $this->createForm(ArticleType::class, $article);

        // Reconnaitre si le formulaire a été soumis ou non
        $formArticle->handleRequest($request);
        // Est-ce que le formulaire a été soumis
        if ($formArticle->isSubmitted() && $formArticle->isValid()) {
            $article->setSlug($slugger->slug($article->getTitre())->lower())
                ->setCreatedat(new \DateTime());
            // Insérer l'article dans la bdd
            $this->articleRepository->add($article, true);
            return $this->redirectToRoute("app_articles");
        }

        // Appel de la vue twig permettant d'afficher le formulaire
        return $this->renderForm('articles/nouveau.html.twig', [
            "formArticle"=>$formArticle
        ]);

        /*$article->setTitre('Nouvel article 2')
            ->setContenu("Contenu du nouvel article 2")
            ->setSlug($slugger->slug($article->getTitre())->lower())
            ->setCreatedat(new \DateTime());

        // Only avec Symfony 6 !
        $this->articleRepository->add($article, true);

        return $this->redirectToRoute("app_articles");*/
    }

    #[Route('/articles/commentaire/{slug}', name: 'app_articles_commentaire',methods: ['GET','POST'], priority: 2)]
    public function addCommentaire($slug, Request $request) : Response {
        $commentaire = new Commentaire();
       $article = $this->articleRepository->findOneBy(["slug"=>$slug]);
       $formCommentaire = $this->createForm(CommentaireType::class,$commentaire);

       $formCommentaire->handleRequest($request);

        if ($formCommentaire->isSubmitted() && $formCommentaire->isValid()) {
            $commentaire->setCreatedat(new \DateTime())
                        ->setArticle($article);
            $this->commentaireRepository->add($commentaire, true);
            return $this->redirectToRoute("app_articles");
        }

        // Appel de la vue twig permettant d'afficher le formulaire
        return $this->renderForm('article/commentaire.html.twig', [
            "formCommentaire"=>$formCommentaire
        ]);

}}
