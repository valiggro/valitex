<?php

namespace App\Command;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsCommand(name: 'app:user:create')]
class UserCreateCommand extends Command
{
    public function __construct(
        private UserService $userService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        $user = (new User())->setEmail($email);

        try {
            $this->userService->create($user);
        } catch (ValidationFailedException $e) {
            foreach ($e->getViolations() as $violation) {
                $io->error($violation->getMessage());
            }
            return Command::FAILURE;
        }
        $io->success("User '{$email}' created.");
        return Command::SUCCESS;
    }
}
