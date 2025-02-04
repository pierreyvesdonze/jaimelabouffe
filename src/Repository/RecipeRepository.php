<?php

namespace App\Repository;

use App\Entity\Recipe;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findByCategory(?string $category = null): array
    {
        $qb = $this->createQueryBuilder('r');

        if ($category) {
            $qb->andWhere('r.category = :category')
                ->setParameter('category', $category);
        }

        $recipes = $qb->getQuery()->getResult();

        // Mélanger aléatoirement les résultats en PHP
        shuffle($recipes);

        return $recipes;
    }

    public function filterRecipesByTags(array $tags): array
    {
        $qb = $this->createQueryBuilder('r')
            ->join('r.tags', 't'); // On joint la table des tags

        if (!empty($tags)) {
            $qb->andWhere('t.id IN (:tags)')
                ->setParameter('tags', $tags);
        }

        return $qb->getQuery()->getResult();
    }

    // Récupérer tous les tags
    public function findAllTags(): array
    {
        return $this->getEntityManager()
            ->getRepository(Tag::class)
            ->findAll();
    }
}
