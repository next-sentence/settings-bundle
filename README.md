
<h1 align="center">Settings for Sylius</h1>

## Installation

> This installation instruction assumes that you're using Symfony Flex.

1. Require the plugin using composer

    ```bash
    composer require next-sentence/settings-bundle
    ```

2. Generate & Run Doctrine migrations

    ```
    ./bin/console doctrine:migration:diff
    ./bin/console doctrine:migration:migrate
    ```

## License

This plugin is completely free and released under the [MIT License](https://github.com/monsieurbiz/SyliusSettingsPlugin/blob/master/LICENSE).
