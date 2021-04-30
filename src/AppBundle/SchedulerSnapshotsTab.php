<?php

namespace AppBundle;

use eZ\Publish\API\Repository\Values\Content\Content;
use EzSystems\EzPlatformAdminUi\Tab\AbstractEventDispatchingTab;
use EzSystems\EzPlatformAdminUi\Tab\ConditionalTabInterface;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use EzSystems\EzPlatformAdminUi\Util\ContentTypeUtil;
use EzSystems\EzPlatformPageFieldType\ScheduleBlock\Scheduler;
use EzSystems\EzPlatformPageFieldType\ScheduleBlock\ScheduleService;
use EzSystems\EzPlatformPageFieldType\ScheduleBlock\ScheduleSnapshotService;
use EzSystems\EzPlatformPageFieldType\ScheduleBlock\Timeline\Event\ItemAddedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class SchedulerSnapshotsTab extends AbstractEventDispatchingTab implements OrderedTabInterface, ConditionalTabInterface
{
    const URI_FRAGMENT = 'ez-tab-location-view-versions';

    /** @var \EzSystems\EzPlatformAdminUi\Util\ContentTypeUtil */
    protected $contentTypeUtil;

    /** @var \EzSystems\EzPlatformPageFieldType\ScheduleBlock\ScheduleSnapshotService */
    protected $scheduleSnapshotService;

    /** @var \EzSystems\EzPlatformPageFieldType\ScheduleBlock\Scheduler */
    protected $scheduler;

    /** @var \EzSystems\EzPlatformPageFieldType\ScheduleBlock\ScheduleService */
    protected $scheduleService;

    /** @var FormFactory */
    protected $formFactory;

    /** @var RequestStack */
    protected $requestStack;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        ContentTypeUtil $contentTypeUtil,
        ScheduleSnapshotService $scheduleSnapshotService,
        Scheduler $scheduler,
        ScheduleService $scheduleService,
        FormFactory $formFactory,
        RequestStack $requestStack
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->contentTypeUtil = $contentTypeUtil;
        $this->scheduleSnapshotService = $scheduleSnapshotService;
        $this->scheduler = $scheduler;
        $this->scheduleService = $scheduleService;
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
    }

    public function getIdentifier(): string
    {
        return 'scheduler';
    }

    public function getName(): string
    {
        /** @Desc("Scheduler") */
        return $this->translator->trans('tab.name.scheduler', [], 'locationview');
    }

    public function getOrder(): int
    {
        return 10000;
    }

    public function evaluate(array $parameters): bool
    {
        /** @var Content $content */
        $content = $parameters['content'];

        $contentType = $content->getContentType();
        $isLandingPage = $this->contentTypeUtil->hasFieldType($contentType, 'ezlandingpage');

        if (!$isLandingPage) {
            return false;
        }

        /** @var \EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Model\Page $page */
        $page = $content->getField('page')->value->getPage();

        foreach ($page->getZones() as $zone) {
            foreach ($zone->getBlocks() as $blockValue) {
                if ($blockValue->getType() === 'schedule') {
                    return true;
                }
            }
        }

        return false;
    }

    public function getTemplate(): string
    {
        return 'scheduler_tab.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
        $content = $contextParameters['content'];
        /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
        $location = $contextParameters['location'];

        $versionInfo = $content->getVersionInfo();
        $contentInfo = $versionInfo->getContentInfo();

        /** @var \EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Model\Page $page */
        $page = $content->getField('page')->value->getPage();

        $scheduleData = ['date' => new \DateTime()];
        $form = $this->getScheduleForm($scheduleData);
        $form->handleRequest($this->requestStack->getMasterRequest());

        $scheduleBlocks = [];
        $events = [];
        $items = [];
        foreach ($page->getZones() as $zone) {
            foreach ($zone->getBlocks() as $blockValue) {
                if ($blockValue->getType() !== 'schedule') {
                    continue;
                }

                $scheduleBlocks[] = $blockValue;

                $events = $blockValue->getAttribute('events')->getValue();

                $events[$blockValue->getId()] = $events;
                $items[$blockValue->getId()] = $this->getItems($events);

                if ($form->isSubmitted() && $form->isValid()) {
                    $date = $form->getData()['date'];

                    $this->scheduleService->initializeScheduleData($blockValue);
                    $this->scheduleSnapshotService->restoreFromSnapshot($blockValue, $date);
                    $this->scheduler->scheduleToDate($blockValue, $date);
                    dump($blockValue);
                }
            }
        }

        $parameters = [
            'schedule_blocks' => $scheduleBlocks,
            'items' => $items,
            'schedule_form' => $form->createView(),
        ];

        return array_replace($contextParameters, $parameters);
    }

    private function getItems($events): array
    {
        $items = [];

        foreach ($events as $event) {
            if (!$event instanceof ItemAddedEvent) {
                continue;
            }

            $items[$event->getItem()->getId()] = $event->getItem();
        }

        return $items;
    }

    private function getScheduleForm(array $data)
    {
        $builder = $this->formFactory->createBuilder(FormType::class, $data)
            ->add('date', DateTimeType::class)
            ->add('schedule', SubmitType::class)
        ;

        return $builder->getForm();
    }
}
