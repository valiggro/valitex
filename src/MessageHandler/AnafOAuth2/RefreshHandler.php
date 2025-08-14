<?php

namespace App\MessageHandler\AnafOAuth2;

use App\Message\AnafOAuth2\Refresh;
use App\Service\AnafOAuth2;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RefreshHandler
{
    public function __construct(
        private AnafOAuth2 $anafOAuth2,
    ) {}

    public function __invoke(Refresh $message): void
    {
        $this->anafOAuth2->refreshToken();
    }
}
