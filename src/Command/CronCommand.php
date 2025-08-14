<?php

namespace App\Command;

use App\Message\AnafOAuth2\Refresh;
use App\Message\EinvoiceImport\Import;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:cron')]
class CronCommand extends Command
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->messageBus->dispatch(new Refresh());
        $this->messageBus->dispatch(new Import());

        return Command::SUCCESS;
    }
}
