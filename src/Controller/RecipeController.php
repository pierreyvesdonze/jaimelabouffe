<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeFilterType;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Service\ImageResizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Filesystem\Filesystem;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/recette')]
final class RecipeController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('s/{category}', name: 'recipe_index', methods: ['GET', 'POST'], defaults: ['category' => null])]
    public function index(
        ?string $category,
        RecipeRepository $recipeRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        // Récupérer toutes les recettes
        $allRecipes = $recipeRepository->findAll();

        // Appliquer un filtre de catégorie si nécessaire
        if ($category) {
            $allRecipes = array_filter($allRecipes, function ($recipe) use ($category) {
                return $recipe->getCategory() === $category;
            });
        }

        // Récupérer le mot-clé de la barre de recherche
        $keyword = $request->query->get('q', '');

        // Appliquer la recherche par nom ou ingrédient
        if ($keyword) {
            $allRecipes = array_filter($allRecipes, function ($recipe) use ($keyword) {
                // Transformer la collection de tags en une chaîne de texte
                $tagsString = implode(', ', $recipe->getTags()->map(fn($tag) => $tag->getName())->toArray());
        
                return stripos($recipe->getName(), $keyword) !== false ||
                    stripos($recipe->getIngredient(), $keyword) !== false ||
                    stripos($tagsString, $keyword) !== false;
            });
        }

        // Création du formulaire de filtrage par tags
        $form = $this->createForm(RecipeFilterType::class);
        $form->handleRequest($request);

        // Par défaut, on prend toutes les recettes (pagination toujours sur `$recipes`)
        $recipes = $allRecipes;

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedTags = $form->get('tags')->getData()->toArray(); // Récupérer les tags sélectionnés

            if (!empty($selectedTags)) {
                // Filtrer uniquement si des tags sont sélectionnés
                $recipes = $recipeRepository->filterRecipesByTags($selectedTags);
            }
        }

        // Pagination (toujours sur `$recipes`)
        $pagination = $paginator->paginate(
            $recipes,
            $request->query->getInt('page', 1), // Page actuelle
            21 // Nombre de recettes par page
        );

        return $this->render('recipe/index.html.twig', [
            'recipes'    => $pagination, // Passe la pagination aux résultats
            'filterForm' => $form->createView(), // Passe le formulaire pour les filtres
            'category'   => $category // Passe la catégorie actuelle (si applicable)
        ]);
    }


    #[Route('/nouvelle', name: 'recipe_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        ImageResizer $imageResizer
    ): Response {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $imagePath = null;

            if ($image) {
                $imagePath = $imageResizer->resize($image, $this->getParameter('images_directory'));
            }

            $recipe->setImage($imagePath);
            $recipe->setUser($this->getUser());

            $this->entityManager->persist($recipe);
            $this->entityManager->flush();

            $this->addFlash('success', 'Recette ajoutée avec succès !');

            return $this->redirectToRoute('recipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recipe/new.html.twig', [
            'recipe' => $recipe,
            'form'   => $form,
        ]);
    }

    #[Route('/{id}', name: 'recipe_show', methods: ['GET'])]
    public function show(Recipe $recipe): Response
    {
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Route('/{id}/modifier', name: 'recipe_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Recipe $recipe,
        ImageResizer $imageResizer
    ): Response {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mainImage = $form->get('image')->getData();
            $oldMainImage = $recipe->getImage();

            if ($mainImage) {
                // Supprimer l'ancienne image principale si elle existe
                if ($oldMainImage) {
                    $oldMainImagePath = $this->getParameter('images_directory') . '/' . $oldMainImage;
                    if (file_exists($oldMainImagePath)) {
                        unlink($oldMainImagePath);
                    }
                }

                // Ajouter la nouvelle image principale
                $mainImagePath = $imageResizer->resize($mainImage, $this->getParameter('images_directory'));
                $recipe->setImage($mainImagePath);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Recette modifiée avec succès !');

            return $this->redirectToRoute('recipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'recipe_delete', methods: ['POST'])]
    public function delete(
        Recipe $recipe,
        FileSystem $filesystem
    ): Response {
        $image = $recipe->getImage();
        $uploadDir = $this->getParameter('images_directory');

        $imagePath = $uploadDir . '/' . $image;
        if ($filesystem->exists($imagePath)) {
            $filesystem->remove($imagePath);
        }

        $this->entityManager->remove($recipe);
        $this->entityManager->flush();

        $this->addFlash('success', 'Recette supprimée avec succès !');

        return $this->redirectToRoute('recipe_index', [], Response::HTTP_SEE_OTHER);
    }
}
