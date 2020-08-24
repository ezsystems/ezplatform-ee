<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace App\EventSubscriber;

use EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Definition\BlockDefinitionEvents;
use EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Definition\Event\BlockAttributeDefinitionEvent;
use EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Definition\Event\BlockDefinitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TranslateBlockNameSubscriber implements EventSubscriberInterface
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlockDefinitionEvents::getBlockDefinitionEventName('example_block') => 'onBlockDefinition',
            BlockDefinitionEvents::getBlockAttributeDefinitionEventName('example_block', 'name') => 'onNameAttributeDefinition'
        ];
    }

    public function onBlockDefinition(BlockDefinitionEvent $event): void
    {
        $event->getDefinition()->setName(
            $this->translator->trans('app.example_block.name', [], 'blocks')
        );
    }

    public function onNameAttributeDefinition(BlockAttributeDefinitionEvent $event): void
    {
        $event->getDefinition()->setName(
            $this->translator->trans('app.example_block.attribute.name.name', [], 'blocks')
        );
    }
}