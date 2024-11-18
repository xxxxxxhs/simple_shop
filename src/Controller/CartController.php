<?php

namespace App\Controller;


use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/cart/add', name: 'app_cart', methods: ['POST'])]
    public function add(Request $request): Response
    {
        $session = $request->getSession();
        $productId = $request->get('id');
        $cart = $session->get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'productId' => $productId,
                'quantity' => 1,
            ];
        }
        $session->set('cart', $cart);
        return $this->json(['message' => 'added to cart']);
    }

    #[Route('/cart/', name: 'show_cart', methods: ['GET'])]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart', []);

        $productIds = array_filter(array_keys($cart), fn($id) => !empty($id) && is_numeric($id));
        $products = !empty($productIds) ? $productRepository->findBy(['id' => $productIds]) : [];

        $finalCart = [];
        foreach ($products as $product) {
            $productId = $product->getId();

            if (isset($cart[$productId])) {
                $finalCart[] = [
                    'product' => $product,
                    'quantity' => $cart[$productId]['quantity'],
                ];
            }
        }
        $cartLen = count($request->getSession()->get('cart', []));
        return $this->render('cart/index.html.twig', [
            'username' => $this->getUser()->getUsername(),
            'cartList' => $finalCart,
            'cart' => $cartLen
        ]);
    }
    #[Route('/cart/clear', name: 'clear_cart', methods: ['POST'])]
    public function clear(Request $request, ProductRepository $productRepository): Response {
        $session = $request->getSession();
        $session->set('cart', []);
        return $this->json(['message' => 'cart cleared']);
    }


}
