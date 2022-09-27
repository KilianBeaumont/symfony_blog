<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleFixtures extends Fixture
{

    private  SluggerInterface $slugger;

    // Demander à Symfony d'injecter le slugger au niveau du constructeur
    public function __construct(SluggerInterface $slugger){
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0;$i<100;$i++) {

            // Initialiser Faker
            $Faker = Factory::create("fr_FR");

            $article = new Article();
            $article->setTitre($Faker->words($Faker->numberBetween(3,10),true));
            $article->setContenu($Faker->paragraph(3,true));
            $article->setCreatedat($Faker->dateTimeBetween('- 6 months'));
            $article->setSlug($this->slugger->slug($article->getTitre())->lower());

            // Générer l'ordre INSERT
            // INSERT INTO article values ("Titre 1","Contenu de l'article 1")
            $manager->persist($article);
        }

        // Envoyer l'ordre INSERT vers la base
        $manager->flush();
    }
}