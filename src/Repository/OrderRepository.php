<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    public $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Order::class);
        $this->entityManager = $entityManager;
    }

    public function create($request, $user): Order
    {
        $request = json_decode($request->getContent(),true);

        $orderCode = $request['order_code'];
        $productId = $request['product_id'];
        $quantity = $request['quantity'];
        $address = $request['address'];
        $shippingDate = \DateTime::createFromFormat('Y-m-d', $request['shipping_date']);

        $order = new Order();

        $order
            ->setOrderCode($orderCode)
            ->setProductId($productId)
            ->setQuantity($quantity)
            ->setAddress($address)
            ->setShippingDate($shippingDate)
            ->setUser($user);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    public function update(Order $order, $request): Order
    {
        $request = json_decode($request->getContent(),true);
        $orderCode = $request['order_code'];
        $productId = $request['product_id'];
        $quantity = $request['quantity'];
        $address = $request['address'];
        $shippingDate = \DateTime::createFromFormat('Y-m-d', $request['shipping_date']);

        $order
            ->setOrderCode($orderCode)
            ->setProductId($productId)
            ->setQuantity($quantity)
            ->setAddress($address)
            ->setShippingDate($shippingDate);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    public function delete(Order $order)
    {
        $this->entityManager->remove($order);
        $this->entityManager->flush();
    }

    public function findAllByUser($user)
    {
        return $this->findBy(['user' => $user]);
    }
}
