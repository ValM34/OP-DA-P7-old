<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CustomerRepository;
use App\Repository\VendorRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Customer;
use App\Entity\Vendor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CustomerController extends AbstractController
{
  // GET USERS BY VENDOR
  #[Route('api/vendor/{id}', name: 'app_vendor', methods: ['GET'])]
  public function getCustomers(Vendor $vendor, SerializerInterface $serializer, Request $request, CustomerRepository $customerRepository, TagAwareCacheInterface $cachePool): JsonResponse
  {
    $page = $request->get('page', 1);
    $limit = $request->get('limit', 3);

    $idCache = "getAllProducts-" . $page . "-" . $limit;

    $jsonCustomerList = $cachePool->get($idCache, function (ItemInterface $item) use ($customerRepository, $page, $limit, $serializer) {
      echo('test : cache non existant [CustomerController] !');
      $item->tag("customersCache");
      $context = SerializationContext::create()->setGroups(['getUsersByVendor']);
      $customerList = $customerRepository->findAllWithPagination($page, $limit);
      return $serializer->serialize($customerList, 'json', $context);
    });

    return new JsonResponse($jsonCustomerList, Response::HTTP_OK, [], true);
  }
  
  // GET ONE
  #[Route('/api/customers/{id}', name: 'customers_get_one', methods: ['GET'])]
  public function getOne(Customer $customer, SerializerInterface $serializer): JsonResponse
  {
    $context = SerializationContext::create()->setGroups(['getUser']);
    $jsonCustomer = $serializer->serialize($customer, 'json', $context);

    return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
  }

  // CREATE
  #[Route('/api/customers/create', name: 'customers_create', methods: ['POST'])]
  #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour créer un customer')]
  public function create(Request $request, VendorRepository $vendorRepository, UrlGeneratorInterface $urlGenerator, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator, TagAwareCacheInterface $cachePool)
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

    $cachePool->invalidateTags(['customersCache']);
    $em->persist($customer);
    $em->flush();

    $context = SerializationContext::create()->setGroups(['getUsersByVendor']);
    $jsonCustomer = $serializer->serialize($customer, 'json', $context);

    $location = $urlGenerator->generate('customers_get_one', ['id' => $customer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

    return new JsonResponse($jsonCustomer, Response::HTTP_CREATED, ["Location" => $location], true);
  }

  // DELETE
  #[Route('/api/customers/delete/{id}', name: 'customers_delete', methods: ['DELETE'])]
  #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour supprimer un customer')]
  public function delete(Customer $customer, EntityManagerInterface $em, TagAwareCacheInterface $cachePool)
  {
    $cachePool->invalidateTags(['customersCache']);
    $em->remove($customer);
    $em->flush();

    return new JsonResponse(null, Response::HTTP_NO_CONTENT);
  }
}
