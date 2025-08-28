<?php

namespace App\Tests\Service;

use App\Entity\Einvoice;
use App\Model\Einvoice\EinvoiceModel;
use App\Service\EinvoiceService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EinvoiceServiceTest extends KernelTestCase
{
    private function _einvoiceModel(): EinvoiceModel
    {
        $message = (object) [
            'id' => random_int(1, 9999),
            'solicitationId' => random_int(1, 9999),
        ];
        $einvoice = (new Einvoice)
            ->setMessage($message);
        return new EinvoiceModel(
            einvoice: $einvoice,
            varDir: '/tmp',
        );
    }

    public function test_getModel(): void
    {
        $this->markTestIncomplete();
    }

    public function test_getXmlModel(): void
    {
        $this->markTestIncomplete();
    }

    public function test_getPdfUrl(): void
    {
        $this->markTestIncomplete();
    }
}
