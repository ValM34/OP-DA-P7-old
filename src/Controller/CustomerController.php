<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\VendorRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Customer;
use App\Entity\Vendor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController extends AbstractController
{
  // GET ONE
  #[Route('/customers/{id}', name: 'customers_get_one', methods: ['GET'])]
  public function getOne(Customer $customer, SerializerInterface $serializer): JsonResponse
  {
    $jsonCustomer = $serializer->serialize($customer, 'json', ['groups' => 'getUser']);

    return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
  }

  // CREATE
  #[Route('/customers/create', name: 'customers_create', methods: ['POST'])]
  public function create(Request $request, VendorRepository $vendorRepository, UrlGeneratorInterface $urlGenerator, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator)
  {
    $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');

    // On vérifie les erreurs
    $errors = $validator->validate($customer);

    if ($errors->count() > 0) {
        return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
    }

    $date = new \DateTimeImmutable();

    $customer
      ->setUpdatedAt($date)
      ->setCreatedAt($date)
    ;

    // Récupération de l'ensemble des données envoyées sous forme de tableau
    $content = $request->toArray();

    // Récupération du vendor_id. S'il n'est pas défini, alors on met -1 par défaut.
    $vendorId = $content['vendor_id'] ?? -1;

    // On cherche le vendeur qui correspond et on l'assigne à l'utilisateur.
    // Si "find" ne trouve pas le vendeur, alors null sera retourné.
    $customer->setVendor($vendorRepository->find($vendorId));

    $em->persist($customer);
    $em->flush();

    $jsonCustomer = $serializer->serialize($customer, 'json', ['groups' => 'getUser']);

    $location = $urlGenerator->generate('app_get_user', ['id' => $customer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

    return new JsonResponse($jsonCustomer, Response::HTTP_CREATED, ["Location" => $location], true);
  }

  // DELETE
  #[Route('/customers/delete/{id}', name: 'customers_delete', methods: ['DELETE'])]
  public function delete(Customer $customer, EntityManagerInterface $em)
  {
    $em->remove($customer);
    $em->flush();

    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }
}
