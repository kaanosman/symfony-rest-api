<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * Class OrderController
 * @package App\Controller
 * @Route("/api", name="order_api")
 */
class OrderController extends AbstractController
{
    /**
     * @var OrderRepository
     */
    public $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return JsonResponse
     * @Route("/orders", name="list_orders", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $orders = $this->orderRepository->findAllByUser($user);

        return new JsonResponse($orders);
    }

    /**
     * @Route("/orders/{id}", name="show_order", methods={"GET"})
     */
    public function show($id): JsonResponse
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $order = $this->orderRepository->find($id);

        if (empty($order) || $order->getUser()->getId() != $user->getId()) {
            return new JsonResponse('order not found', Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($order);
    }

    /**
     * Create order
     * @Route("/orders", name="create_order", methods={"POST"})
     *
     * @OA\Post(
     *  path="/api/orders",
     *  @Security(name="Bearer"),
     *  @OA\RequestBody(@OA\MediaType(mediaType="application/json", @OA\Schema(
     *      @OA\Property(property="order_code", type="string", example="", description="order_code"),
     *      @OA\Property(property="product_id", type="integer", example="", description="product_id"),
     *      @OA\Property(property="quantity", type="float", example="", description="quantity"),
     *      @OA\Property(property="address", type="string", example="", description="address"),
     *    	@OA\Property(property="shipping_date",type="integer", example="", description="shipping_date"),),),),
     *  summary="Create an order",
     *  @OA\Response(response="201", description="Create an order")
     * )
     */
    public function create(Request $request): JsonResponse
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $order = $this->orderRepository->create($request, $user);

        return new JsonResponse($order, Response::HTTP_CREATED);
    }

    /**
     * Update order
     * @Route("/orders/{id}", name="update_order", methods={"POST"})
     */
    public function update($id, Request $request): JsonResponse
    {
        $order = $this->orderRepository->find($id);

        if ($order->getShippingDate()->format("Y-m-d") <= date("Y-m-d"))
        {
            return new JsonResponse('order can not change', Response::HTTP_NOT_FOUND);
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (empty($order) || $order->getUser()->getId() != $user->getId()) {
            return new JsonResponse('order not found', Response::HTTP_NOT_FOUND);
        }

        $order = $this->orderRepository->update($order, $request);

        return new JsonResponse($order);
    }

    /**
     * @Route("/orders/{id}", name="delete_order", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $order = $this->orderRepository->find($id);

        if (empty($order) || $order->getUser()->getId() != $user->getId()) {
            return new JsonResponse('order not found', Response::HTTP_NOT_FOUND);
        }

        $this->orderRepository->delete($order);

        return new JsonResponse('order deleted');
    }
}
