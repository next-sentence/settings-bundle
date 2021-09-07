<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Lwc\SettingsBundle\Settings\SettingsInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class SettingsFormMenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private FactoryInterface $factory;

    /**
     * @var RepositoryInterface
     */
    private RepositoryInterface $localeRepository;

    /**
     * SettingsFormMenuBuilder constructor.
     *
     * @param FactoryInterface $factory
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        FactoryInterface $factory,
        RepositoryInterface $localeRepository
    ) {
        $this->factory = $factory;
        $this->localeRepository = $localeRepository;
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     */
    public function createMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        if (!\array_key_exists('settings', $options) || !$options['settings'] instanceof SettingsInterface) {
            return $menu;
        }

        $menu
            ->addChild('default')
            ->setAttribute('template', '@LwcSettings/Crud/Edit/Tab/_default.html.twig')
            ->setLabel('lwc.settings.ui.by_default')
            ->setCurrent(true)
            ->setExtra('settings', $options['settings'])
            ->setExtra('locales', $this->localeRepository->findAll())
        ;

        return $menu;
    }
}
