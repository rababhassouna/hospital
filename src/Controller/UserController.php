<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UserController extends AbstractFOSRestController
{
    /**
     * @Route("/user", methods={"POST"})
     * @ParamConverter("user", converter="fos_rest.request_body")
     */

    public function registerUser(User $user, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, JWTTokenManagerInterface $JWTManager, ConstraintViolationListInterface $validationErrors): Response
    {
        if (count($validationErrors) > 0) {
            return $this->handleView(View::create($validationErrors, Response::HTTP_BAD_REQUEST));
        }
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );
        $user->setPassword($hashedPassword);
        $em->persist($user);
        $em->flush();

        $user->getUserIdentifier();

        return View::create(['token' => $JWTManager->create($user)], Response::HTTP_CREATED);
    }
}
