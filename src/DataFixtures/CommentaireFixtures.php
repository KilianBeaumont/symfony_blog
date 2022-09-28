<?php

namespace App\DataFixtures;

use App\Entity\Commentaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentaireFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");

        for ($i=0; $i<=499; $i++){
            $commentaire = new Commentaire();
            $commentaire->setContenu($faker->paragraph);
            $commentaire->setCreatedAt($faker->dateTimeBetween('-6 month','now'));

            $nbUtilisateur = $faker->numberBetween(1,50);
            $nbCommentaire = $faker->numberBetween(1,99);

            $commentaire->setArticle($this->getReference("article".$nbCommentaire));
            $commentaire->setUtilisateur($this->getReference("utilisateur".$nbUtilisateur));
            $manager->persist($commentaire);

        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ArticleFixtures::class,
            UtilisateurFixtures::class
        ];
        // TODO: Implement getDependencies() method.
    }
}
