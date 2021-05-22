<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        for ($p = 0; $p < 6; $p++) {
            $post = new Post;

            $post
                ->setTitle("Site n°$p")
                ->setDescription("Description n°$p")
                ->setLink("#")
                ->setPhoto("https://picsum.photos/id/23$p/400/300");

            $manager->persist($post);
        }

        $user = new User;

        $hash = $this->encoder->encodePassword($user, "DefaceD88FF%");

        $user
            ->setEmail("fournier.florent.88@gmail.com")
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        $manager->flush();
    }
}