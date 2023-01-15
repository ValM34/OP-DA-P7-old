<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Entity\Product;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
  // GET ALL PRODUCTS
  #[Route('/products', name: 'app_products', methods: ['GET'])]
  public function getAll(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
  {
    $productList = $productRepository->findAll();
    $jsonProductList = $serializer->serialize($productList, 'json');

    return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
  }

  // GET ONE PRODUCT
  #[Route('/products/{id}', name: 'app_product', methods: ['GET'])]
  public function getOne(Product $product, SerializerInterface $serializer): JsonResponse
  {
    $jsonProduct = $serializer->serialize($product, 'json');

    return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
  }
}
