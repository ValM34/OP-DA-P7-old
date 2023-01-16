<?php

namespace App\DataFixtures;

use App\Entity\Vendor;
use App\Entity\Customer;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use \DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
  private $userPasswordHasher;

  public function __construct(UserPasswordHasherInterface $userPasswordHasher)
  {
    $this->userPasswordHasher = $userPasswordHasher;
  }

  public function load(ObjectManager $manager): void
  {
    $date = new DateTimeImmutable();
    $listVendor = [];
    for ($i = 0; $i < 10; $i++) {
      if($i < 5) {
        $vendor = new Vendor();
        $vendor
          ->setName('Name' . $i)
          ->setEmail('e@mail' . $i . '.fr')
          ->setRoles(["ROLE_USER"])
          ->setPassword($this->userPasswordHasher->hashPassword($vendor, "password"))
          ->setUpdatedAt($date)
          ->setCreatedAt($date);
        $manager->persist($vendor);
        $listVendor[] = $vendor;
      } else {
        $vendor = new Vendor();
        $vendor
          ->setName('Name' . $i)
          ->setEmail('e@mail' . $i . '.fr')
          ->setRoles(["ROLE_ADMIN"])
          ->setPassword($this->userPasswordHasher->hashPassword($vendor, "password"))
          ->setUpdatedAt($date)
          ->setCreatedAt($date);
        $manager->persist($vendor);
        $listVendor[] = $vendor;
      }
      
    }

    for ($i = 0; $i < 20; $i++) {
      $customer = new Customer();
      $customer
        ->setFirstName('firstName' . $i)
        ->setLastName('lastName' . $i)
        ->setEmail('e@mail' . $i . '.fr')
        ->setUpdatedAt($date)
        ->setCreatedAt($date)
        ->setVendor($listVendor[array_rand($listVendor)]);
      $manager->persist($customer);
    }

    for ($i = 0; $i < 50; $i++) {
      $product = new Product();
      $product
        ->setName('Product name ' . $i)
        ->setDescription('Description ' . $i)
        ->setPrice(2000)
        ->setUpdatedAt($date)
        ->setCreatedAt($date);
      $manager->persist($product);
    }

    $manager->flush();
  }
}
