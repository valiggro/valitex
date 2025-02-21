<?php

namespace App\Command;

use App\Service\AnafOAuth2;
use App\Service\EinvoiceImport;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:cron')]
class CronCommand extends Command
{
    public function __construct(
        private AnafOAuth2 $anafOAuth2,
        private EinvoiceImport $einvoiceImport,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->anafOAuth2->refreshToken();
        $this->einvoiceImport->import();

        return Command::SUCCESS;
    }
}
