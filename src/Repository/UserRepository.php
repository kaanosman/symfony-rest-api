<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    public $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    public $encoder;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct($registry, User::class);
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    public function create($username, $password): User
    {
        $user = new User();

        $user
            ->setUserName($username)
            ->setPassword($this->encoder->encodePassword($user, $password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

}
