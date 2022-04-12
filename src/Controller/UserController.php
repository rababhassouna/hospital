<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(Request $request,UserPasswordHasherInterface $passwordHasher,EntityManagerInterface $em,JWTTokenManagerInterface $JWTManager): Response
    {
        $email = $request->get('email');
        $password = $request->get('password');
        $user = new User();
        $user->setEmail($email);
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );
        $user->setPassword($hashedPassword);
        $em->persist($user);
        $em->flush();
        return $this->json([
            'token' =>  $JWTManager->create($user)
        ]);
    }
}
