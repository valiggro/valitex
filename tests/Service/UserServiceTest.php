<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class UserServiceTest extends KernelTestCase
{
    public function test_create_ValidationFailedException(): void
    {
        $user = new User();
        $user->setEmail(uniqid() . '@example.com');

        $violationList = $this->createMock(ConstraintViolationListInterface::class);
        $violationList
            ->expects($this->once())
            ->method('count')
            ->willReturn(1);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($user)
            ->willReturn($violationList);
        static::getContainer()->set(ValidatorInterface::class, $validator);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->never())
            ->method('persist');
        $entityManager
            ->expects($this->never())
            ->method('flush');
        static::getContainer()->set(EntityManagerInterface::class, $entityManager);

        $this->expectException(ValidationFailedException::class);

        static::getContainer()->get(UserService::class)->create($user);
    }

    public function test_create(): void
    {
        $user = new User();
        $user->setEmail(uniqid() . '@example.com');

        $violationList = $this->createMock(ConstraintViolationListInterface::class);
        $violationList
            ->expects($this->once())
            ->method('count')
            ->willReturn(0);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($user)
            ->willReturn($violationList);
        static::getContainer()->set(ValidatorInterface::class, $validator);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($user);
        $entityManager
            ->expects($this->once())
            ->method('flush');
        static::getContainer()->set(EntityManagerInterface::class, $entityManager);

        static::getContainer()->get(UserService::class)->create($user);
    }
}
