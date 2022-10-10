<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleCrudController extends AbstractCrudController
{

    private SluggerInterface $slugger;

    // injection du slugger au niveau du constructeur
    public function __construct(SluggerInterface $slugger) {
        $this->slugger = $slugger;
    }

    public static function getEntityFqcn(): string
    {
        return Article::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('titre'),
            TextEditorField::new('contenu'),
            DateField::new('createdat')->hideOnForm(),
            TextField::new('slug')->hideOnForm(),
            BooleanField::new('publie')
        ];
    }

    // redéfinir la m"thode persistEntity qui va être appelée lors de la création d'un article en bdd
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // Vérifier que l'objet entityInstance est bien l'instance de l'entité article
        if (!$entityInstance instanceof Article) return;
        $entityInstance->setCreatedat(new \DateTime());
        $entityInstance->setSlug($this->slugger->slug($entityInstance->getTitre())->lower());
        // Appel à la méthode héritée afin de persister l'entité
        parent::persistEntity($entityManager,$entityInstance);
    }

}
