<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Processor;

use Lwc\SettingsBundle\Settings\SettingsInterface;

interface SettingsProcessorInterface
{
    public function processData(SettingsInterface $settings, array $data): void;
}
