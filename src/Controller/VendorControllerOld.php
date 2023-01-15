<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Vendor;

class VendorController extends AbstractController
{
  // GET USERS BY VENDOR
  #[Route('/vendor/{id}', name: 'app_vendor', methods: ['GET'])]
  public function getCustomers(Vendor $vendor, SerializerInterface $serializer): JsonResponse
  {
    $jsonCustomerList = $serializer->serialize($vendor, 'json', ['groups' => 'getUsersByVendor']);

    return new JsonResponse($jsonCustomerList, Response::HTTP_OK, [], true);
  }
}
