<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()

    { 
        $this->faker = Factory::create('fr_FR');
    }

      public function load(ObjectManager $manager) : void
    {
        for ($i = 0; $i < 10; $i++) {

            $user = new User();
            $user->setFullName($this->faker->name())
                ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setPassword('password');

              $manager->persist($user);

        }

        $manager->flush();
      }
  }
  
