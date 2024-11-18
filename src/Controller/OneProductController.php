<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OneProductController extends AbstractController
{
    #[Route('/shop/product/{id<\d+>}', name: 'app_one_product', methods: ['GET'])]
    public function index(Request $request, ProductRepository $productRepository, int $id): Response
    {
        $product = $productRepository->find($id);
        $cartLen = count($request->getSession()->get('cart', []));
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }
        return $this->render('one_product/index.html.twig', [
            'controller_name' => 'OneProductController',
            'product' => $product,
            'username' => $this->getUser()->getUsername(),
            'cart' => $cartLen
        ]);
    }
}
