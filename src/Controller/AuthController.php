<?php

namespace App\Controller;

use App\Dto\RegisterUserDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class AuthController extends ApiController
{

    #[Route('api/register', methods:'POST', name: 'app_home')]
    public function register(#[MapRequestPayload] RegisterUserDto $registerUserDto, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, JWTTokenManagerInterface $jwtManager, UserRepository $userRepository): JsonResponse
    {
        $email = $registerUserDto->email;
        $password = $registerUserDto->password;
        $name = $registerUserDto->name;

        $user = $userRepository->findOneBy(['email' => $email]);

        if($user) {
            return $this->respondValidationError('User already exists!');
        }

        $user = new User();
        $user->setEmail($email);
        $user->setName($name);

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );

        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        // Generate JWT token
        $token = $jwtManager->create($user);

        return $this->respondCreated(['token' => $token]);
    }
}
