<?php

namespace AppBundle\Command;

use DateTime;
use eZ\Publish\API\Repository\Values\Content\Location;
use EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Model\Attribute;
use EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Model\BlockValue;
use EzSystems\EzPlatformPageFieldType\ScheduleBlock\Item\Item;
use EzSystems\EzPlatformPageFieldType\ScheduleBlock\ScheduleBlock;
use EzSystems\EzPlatformPageFieldType\ScheduleBlock\Timeline\Event\ItemAddedEvent;
use EzSystems\EzPlatformPageFieldType\ScheduleBlock\Timeline\Event\ItemRemovedEvent;
use EzSystems\EzPlatformPageFieldType\ScheduleBlock\Timeline\Event\ItemsReorderedEvent;
use Ramsey\Uuid\Uuid;

class ScheduleBlockGenerator
{
    const INTERVAL = '+2 hours';

    private $date;
    private $name = 'Content Scheduler';
    private $view = 'default';
    private $limit = 10;
    private $events = [];

    public function __construct(
        ?DateTime $date,
        ?string $name = null,
        ?string $view = null,
        ?int $limit = null,
        ?array $events = null
    ) {
        $this->date = $date ?: new DateTime();
        $this->name = $name ?: 'Content Scheduler';
        $this->view = $view ?: 'default';
        $this->limit = $limit ?: 10;
        $this->events = $events ?: [];
    }

    public function addItem(string $itemId, Location $location, ?DateTime $date = null)
    {
        if (null === $date) {
            $this->date->modify(self::INTERVAL);
            $date = clone $this->date;
        }

        $item = new Item(
            $itemId,
            $date,
            $location
        );

        $this->events[] = new ItemAddedEvent(
            Uuid::uuid4(),
            $date,
            $item
        );

        return $this;
    }

    public function removeItem(string $itemId, ?DateTime $date = null)
    {
        if (null === $date) {
            $this->date->modify(self::INTERVAL);
            $date = clone $this->date;
        }

        $this->events[] = new ItemRemovedEvent(
            Uuid::uuid4(),
            $date,
            $itemId
        );

        return $this;
    }

    public function reorderItems(array $map, ?DateTime $date = null)
    {
        if (null === $date) {
            $this->date->modify(self::INTERVAL);
            $date = clone $this->date;
        }

        $this->events[] = new ItemsReorderedEvent(
            Uuid::uuid4(),
            $date,
            $map
        );

        return $this;
    }

    public function generate(): BlockValue
    {
        $blockValue = new BlockValue(
            '',
            'schedule',
            $this->name,
            $this->view,
            null,
            null,
            '',
            null,
            null,
            [
                new Attribute(
                    '',
                    ScheduleBlock::ATTRIBUTE_LIMIT,
                    $this->limit
                ),
                new Attribute(
                    '',
                    ScheduleBlock::ATTRIBUTE_INITIAL_ITEMS,
                    []
                ),
                new Attribute(
                    '',
                    ScheduleBlock::ATTRIBUTE_SLOTS,
                    []
                ),
                new Attribute(
                    '',
                    ScheduleBlock::ATTRIBUTE_EVENTS,
                    $this->events
                ),
                new Attribute(
                    '',
                    ScheduleBlock::ATTRIBUTE_SNAPSHOTS,
                    []
                ),
                new Attribute(
                    '',
                    ScheduleBlock::ATTRIBUTE_LOADED_SNAPSHOT,
                    null
                ),
            ]
        );

        return $blockValue;
    }
}
