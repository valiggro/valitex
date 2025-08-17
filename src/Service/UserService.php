<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
    ) {}

    /**
     * @throws ValidationFailedException
     */
    public function create(User $user): void
    {
        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            throw new ValidationFailedException($user, $errors);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
