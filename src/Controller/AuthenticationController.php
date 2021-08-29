<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use OpenApi\Annotations as OA;

class AuthenticationController extends AbstractController
{
    /**
     * @var UserRepository
     */
    public $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    public $encoder;

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
    {
        $this->userRepository = $userRepository;
        $this->encoder = $encoder;
    }

    /**
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     *  @OA\RequestBody(@OA\MediaType(mediaType="application/json", @OA\Schema(
     *      @OA\Property(property="username", type="string", example="", description="username"),
     *    	@OA\Property(property="password",type="string", example="", description="password"),),),),
     *  summary="Login",
     *  @OA\Response(response="200", description="Login")
     */
    public function login(JWTTokenManagerInterface $JWTManager, Request $request): JsonResponse
    {
        $request = json_decode($request->getContent(),true);
        $username = $request['username'];
        $password = $request['password'];

        $user = $this->userRepository->findOneBy([
            'userName' => $username
        ]);

        dd($user);

        if (!$user) {
            return new JsonResponse('user not found', Response::HTTP_NOT_FOUND);
        }

        $isPasswordValid = $this->encoder->isPasswordValid($user, $password);

        if (!$isPasswordValid) {
            return new JsonResponse('user not found', Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }

    /**
     * @return JsonResponse
     *  @OA\RequestBody(@OA\MediaType(mediaType="application/json", @OA\Schema(
     *      @OA\Property(property="username", type="string", example="", description="username"),
     *    	@OA\Property(property="password",type="string", example="", description="password"),),),),
     *  summary="Register",
     *  @OA\Response(response="200", description="Register")
     */
    public function register(Request $request)
    {
        $request = json_decode($request->getContent(),true);
        $username = $request['username'];
        $password = $request['password'];

        $user = $this->userRepository->create($username, $password);

        return new JsonResponse(sprintf('User %s successfully created', $user->getUsername()));
    }
}
