<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShopController extends AbstractController
{
    #[Route('/shop', name: 'app_shop')]
    public function index(ProductRepository $repository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 6;
        $cartLen = count($request->getSession()->get('cart', []));
        $products = $repository->findBy([], null, $limit, ($page - 1) * $limit);
        $totalProducts = $repository->count([]);
        $totalPages = ceil($totalProducts / $limit);

        return $this->render('shop/index.html.twig', [
            'username' => $this->getUser()->getUsername(),
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'cart' => $cartLen
        ]);

    }
}
