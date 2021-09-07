<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Form;

use Lwc\SettingsBundle\Settings\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractSettingsType extends AbstractType implements SettingsTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            'settings',
        ]);

        $resolver->setDefaults([
            'show_default_checkboxes' => true,
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param $child
     * @param string|null $type
     * @param array $options
     *
     * @return $this
     */
    public function addWithDefaultCheckbox(FormBuilderInterface $builder, string $child, string $type = null, array $options = []): self
    {
        $data = $builder->getData();
        $builder->add($child, $type, $options);
        if (!$this->isDefaultForm($builder)) {
            $builder->add($child . '___' . Settings::DEFAULT_KEY, DefaultCheckboxType::class, [
                'label' => 'lwc.settings.ui.use_default_value',
                'related_form_child' => $builder->get($child),
                'data' => !\array_key_exists($child, $data),
                'required' => true,
            ]);
        }

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return bool
     */
    protected function isDefaultForm(FormBuilderInterface $builder): bool
    {
        return !$builder->getOption('show_default_checkboxes', true);
    }
}
