<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Factory\Form;

use Lwc\SettingsBundle\Settings\SettingsInterface;
use Symfony\Component\Form\FormInterface;

interface MainSettingsFormTypeFactoryInterface
{
    public function createNew(SettingsInterface $settings, string $type, array $options = []): FormInterface;
}
