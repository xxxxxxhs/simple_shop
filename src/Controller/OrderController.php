<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/order/make', name: 'app_order', methods: ['POST'])]
    public function add(Request $request, ProductRepository $productRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $cart = $request->getSession()->get('cart');
        if (empty($cart)) {
            return new JsonResponse(['message' => 'Cart is empty'], Response::HTTP_BAD_REQUEST);
        }

        $order = new Order();
        $order->setCreatedAt(new \DateTime());
        $order->setStatus("accepted");
        $order->setUserId($this->getUser());

        $entityManager->persist($order);

        $totalPrice = 0;

        foreach ($cart as $productId => $item) {
            $product = $productRepository->find($productId);
            if ($product) {
                if ($item['quantity'] > $product->getStock()) {
                    return new JsonResponse([
                        'message' => "Not enough {$product->getName()} in stock, available: {$product->getStock()}"
                    ], 400);
                }

                $totalPrice += $product->getPrice() * $item['quantity'];

                $orderProduct = new OrderProduct();
                $orderProduct->setProduct($product);
                $orderProduct->setOrderId($order);
                $orderProduct->setQuantity($item['quantity']);

                $product->setStock($product->getStock() - $item['quantity']);

                $entityManager->persist($orderProduct);
                $entityManager->persist($product);
            }
        }

        $order->setTotalPrice($totalPrice);

        $entityManager->flush();

        $request->getSession()->set('cart', []);

        return new JsonResponse(['message' => 'Successfully made an order'], 200);
    }

}
