<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;

class ProductController extends AbstractController
{
  // GET ALL PRODUCTS
  #[Route('/api/products', name: 'app_products', methods: ['GET'])]
  public function getAll(ProductRepository $productRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cachePool): JsonResponse
  {
    $page = $request->get('page', 1);
    $limit = $request->get('limit', 3);

    $idCache = "getAllProducts-" . $page . "-" . $limit;

    $jsonProductList = $cachePool->get($idCache, function (ItemInterface $item) use ($productRepository, $page, $limit, $serializer) {
      $item->tag("ProductsCache");
      $productList = $productRepository->findAllWithPagination($page, $limit);
      return $serializer->serialize($productList, 'json');
    });

    return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
  }

  // GET ONE PRODUCT
  #[Route('/api/products/{id}', name: 'app_product', methods: ['GET'])]
  public function getOne(Product $product, SerializerInterface $serializer): JsonResponse
  {
    $jsonProduct = $serializer->serialize($product, 'json');

    return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
  }
}
