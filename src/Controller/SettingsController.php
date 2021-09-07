<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Controller;

use Lwc\SettingsBundle\Factory\Form\MainSettingsFormTypeFactoryInterface;
use Lwc\SettingsBundle\Form\MainSettingsType;
use Lwc\SettingsBundle\Processor\SettingsProcessorInterface;
use Lwc\SettingsBundle\Settings\RegistryInterface;
use Lwc\SettingsBundle\Settings\SettingsInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SettingsController extends AbstractController
{
    /**
     * @var SettingsProcessorInterface
     */
    private SettingsProcessorInterface $settingsProcessor;

    /**
     * @var MainSettingsFormTypeFactoryInterface
     */
    private MainSettingsFormTypeFactoryInterface $formFactory;

    /**
     * SettingsController constructor.
     *
     * @param SettingsProcessorInterface $settingsProcessor
     * @param MainSettingsFormTypeFactoryInterface $formFactory
     */
    public function __construct(
        SettingsProcessorInterface $settingsProcessor,
        MainSettingsFormTypeFactoryInterface $formFactory
    ) {
        $this->settingsProcessor = $settingsProcessor;
        $this->formFactory = $formFactory;
    }

    /**
     * @param RegistryInterface $registry
     *
     * @return Response
     */
    public function indexAction(RegistryInterface $registry)
    {
        return $this->render('@LwcSettings/Crud/index.html.twig', [
            'settings' => $registry->getAllSettings(),
        ]);
    }

    /**
     * @param Request $request
     * @param RegistryInterface $registry
     * @param $alias
     *
     * @return Response
     */
    public function formAction(Request $request, RegistryInterface $registry, $alias)
    {
        if (null === ($settings = $registry->getByAlias($alias))) {
            throw $this->createNotFoundException();
        }

        $form = $this->getForm($settings);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->settingsProcessor->processData($settings, $data);
            $this->addFlash('success', 'lwc.settings.settings_successfully_saved');

            return $this->redirectToRoute('lwc_sylius_settings_admin_edit', [
                'alias' => $settings->getAlias(),
            ]);
        }

        return $this->render(
            '@LwcSettings/Crud/edit.html.twig',
            [
                'settings' => $settings,
                'form_event' => 'lwc.settings.form',
                'form_event_dedicated' => sprintf('lwc.settings.form.%s', $settings->getAlias()),
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param SettingsInterface $settings
     *
     * @return FormInterface
     */
    private function getForm(SettingsInterface $settings): FormInterface
    {
        return $this->formFactory->createNew(
            $settings,
            MainSettingsType::class,
            [
                'action' => $this->generateUrl('lwc_sylius_settings_admin_edit_post', ['alias' => $settings->getAlias()]),
                'method' => 'POST',
                'settings' => $settings,
            ],
        );
    }
}
