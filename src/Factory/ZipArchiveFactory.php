<?php

namespace App\Factory;

class ZipArchiveFactory
{
    public function __invoke(): \ZipArchive
    {
        return new \ZipArchive;
    }
}
