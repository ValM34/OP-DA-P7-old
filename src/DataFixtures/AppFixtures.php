<?php

namespace App\DataFixtures;

use App\Entity\Vendor;
use App\Entity\Customer;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use \DateTimeImmutable;

class AppFixtures extends Fixture
{
  public function load(ObjectManager $manager): void
  {
    $date = new DateTimeImmutable();
    $listVendor = [];
    for ($i = 0; $i < 10; $i++) {
      $vendor = new Vendor();
      $vendor
        ->setName('Name'.$i)
        ->setEmail('e@mail'.$i.'.fr')
        ->setUpdatedAt($date)
        ->setCreatedAt($date)
      ;
      $manager->persist($vendor);
      $listVendor[] = $vendor;
    }

    for ($i = 0; $i < 20; $i++) {
      $customer = new Customer();
      $customer
        ->setFirstName('firstName'.$i)
        ->setLastName('lastName'.$i)
        ->setEmail('e@mail'.$i.'.fr')
        ->setUpdatedAt($date)
        ->setCreatedAt($date)
        ->setVendor($listVendor[array_rand($listVendor)])
      ;
      $manager->persist($customer);
    }

    for ($i = 0; $i < 50; $i++) {
      $product = new Product();
      $product
        ->setName('Product name '.$i)
        ->setDescription('Description '.$i)
        ->setPrice(2000)
        ->setUpdatedAt($date)
        ->setCreatedAt($date)
      ;
      $manager->persist($product);
    }

    $manager->flush();
  }
}
