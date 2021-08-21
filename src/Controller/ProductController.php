<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use function PHPUnit\Framework\throwException;

/**
 * @IsGranted("ROLE_USER")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("product",name="product_index")
     */
    public function indexAction()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render("product/index.html.twig", ["products" => $products]);
    }
    /**
     * @Route("product/details/{id}", name="product_detail")
     */
    public function detailAction($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        return $this->render("product/detail.html.twig", ["product" => $product]);
    }
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("product/create",name="product_create")
     */
    public function createAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //get image from uploaded
            $image = $product->getImage();

            //create image a unique name
            $fileName = md5(uniqid());
            //get image extends
            $fileExtension = $image->guessExtension();
            //merge image name and image extension
            $imageName = $fileName . '.' . $fileExtension;

            //move upload file to predefined location
            try {
                $image->move(
                    $this->getParameter('product_image'),
                    $imageName
                );
            } catch (FileException $e) {
                throwException($e);
            }

            //set image name to database
            $product->setImage($imageName);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($product);
            $manager->flush();
            $this->addFlash("Info", "Add successfully");
            return $this->redirectToRoute("product_index");
        }
        return $this->render("product/create.html.twig", ["form" => $form->createView()]);
    }
    /**
     * @Route("product/update/{id}", name="product_update")
     */
    public function updateAction(Request $request, $id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($product);
            $manager->flush();

            $this->addFlash("Info", "Product successfully updated");
            return $this->redirectToRoute("product_index");
        }
        return $this->render("Product/update.html.twig", ["form" => $form->createView()]);
    }
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("product/delete/{id}", name="product_delete")
     */
    public function deleteAction($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if ($product == null) {
            $this->addFlash("Error", "Product not found");
            return $this->redirectToRoute("product_index");
        }
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($product);
        $manager->flush();
        $this->addFlash("Info", "Delete successfully");
        return $this->redirectToRoute("product_index");
    }
    
}
