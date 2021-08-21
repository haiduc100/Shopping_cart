<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use function PHPUnit\Framework\throwException;

/**
 * @IsGranted("ROLE_USER")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category_index")
     */
    public function indexCategoryAction()
    {
        $categorys = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render("category/index.html.twig", ["categorys" => $categorys]);
    }
    /**
     * @Route("/category/detail/{id}", name="category_detail")
     */
    public function detailAction($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        return $this->render("category/detail.html.twig", ["category" => $category]);
    }
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/category/create", name="category_create")
     */
    public function createAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            $this->addFlash("Info", "Add successfully!");
            return $this->redirectToRoute("category_index");
        }
        return $this->render("category/create.html.twig", ["form" => $form->createView()]);
    }
    /**
     * @Route("/category/update/{id}", name="category_update")
     */
    public function updateAction(Request $request, $id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            $this->addFlash("Info", "Update successfully");
            $this->redirectToRoute("category_index");
        }
        return $this->render("category/create.html.twig", ["form" => $form->createView()]);
    }
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/category/delete/{id}", name="category_delete")
     */
    public function deleteAction($id)
    {
        try {
            $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

            $chk = $category->getProducts();
            if (count($chk) > 0) {
                $this->addFlash("Error", "Can not delete category has contains");
                return $this->redirectToRoute("category_index");
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($category);
            $manager->flush();
            $this->addFlash("Info", "Delete successfully");
            return $this->redirectToRoute("category_index");
        } catch (\Exception $e) {
            throwException($e->getMessage);
        }
    }
}
