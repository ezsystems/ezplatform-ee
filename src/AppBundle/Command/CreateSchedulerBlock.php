<?php

namespace AppBundle\Command;

use eZ\Publish\API\Repository\Values\Content\Content;
use EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Model\BlockValue;
use EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Model\Page;
use EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Model\Zone;
use EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Value;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateSchedulerBlock extends ContainerAwareCommand
{
    const MODIFIER = '+2 hours';

    protected function configure()
    {
        $this
            ->setName('ibexa:create-scheduler-block')
            ->addArgument('userId', InputArgument::OPTIONAL, 'Author/User', 14)
            ->addArgument('limit', InputArgument::OPTIONAL, 'Number of children', 300)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getContainer()->get('ezpublish.api.repository');

        $contentService = $repository->getContentService();

        $repository->setCurrentUser($repository->getUserService()->loadUser($input->getArgument('userId'))); // Administrator User

        // create draft
        $content = $this->createLandingPage();
        $newContentItems = $this->createChildren($content, $input->getArgument('limit'));
        $draft = $contentService->createContentDraft($content->contentInfo);

        // get page value
        $updateStruct = $contentService->newContentUpdateStruct();
        /** @var Page $page */
        $page = $draft->getField('page')->value->getPage();

        // add scheduler block
        $zone = $page->getZones()[0];
        $zone->addBlock($this->getSchedulerBlock($newContentItems));

        // save page field
        $updateStruct->setField('page', new Value($page));
        $contentService->updateContent($draft->versionInfo, $updateStruct);

        // finally publish
        $content = $contentService->publishVersion($draft->versionInfo);

        $output->writeln(sprintf('<info>Created landing page \'%s\' (content ID: %d)</info>', (string)$content->getField('name')->value, $content->contentInfo->id));
    }

    private function createLandingPage(): Content
    {
        $repository = $this->getContainer()->get('ezpublish.api.repository');

        $contentService = $repository->getContentService();
        $contentTypeService = $repository->getContentTypeService();
        $locationService = $repository->getLocationService();

        $contentType = $contentTypeService->loadContentTypeByIdentifier('landing_page');
        $locationCreateStruct = $locationService->newLocationCreateStruct(2);

        $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');
        $lpUuid = Uuid::uuid4();

        $contentCreateStruct->setField('name', 'Landing Page ' . substr($lpUuid, 0, 8));
        $contentCreateStruct->setField('description', 'description');

        // empty page
        $value = new Value(
            new Page('default', [new Zone('', 'default', [])])
        );
        $contentCreateStruct->setField('page', $value);

        $draft = $contentService->createContent($contentCreateStruct, [$locationCreateStruct]);
        $content = $contentService->publishVersion($draft->versionInfo);

        return $content;
    }

    /**
     * @param Content[] $contentItems
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function getSchedulerBlock(array $contentItems): BlockValue
    {
        $repository = $this->getContainer()->get('ezpublish.api.repository');

        $locationService = $repository->getLocationService();

        $scheduleBlockGenerator = new ScheduleBlockGenerator(new \DateTime());

        $lastDate = new \DateTime('now');
        // add items
        $addedItemIds = [];
        foreach ($contentItems as $contentItem) {
            $itemId = Uuid::uuid4();
            $location = $locationService->loadLocation($contentItem->contentInfo->mainLocationId);
            $scheduleBlockGenerator->addItem($itemId, $location, clone $lastDate);
            $lastDate->modify(self::MODIFIER);

            $addedItemIds[] = $itemId;
        }

        // remove every 2nd item
        $count = \count($addedItemIds);
        for ($i = 0; $i < $count; ++$i) {
            if ($i % 2 !== 0) {
                continue;
            }

            $scheduleBlockGenerator->removeItem($addedItemIds[$i], clone $lastDate);
            unset($addedItemIds[$i]);

            $lastDate->modify(self::MODIFIER);
        }

        // reverse order leftover items
        $reversedOrder = array_reverse($addedItemIds);
        $scheduleBlockGenerator->reorderItems($reversedOrder, clone $lastDate);

        return $scheduleBlockGenerator->generate();
    }

    private function createChildren(Content $content, int $limit = 300): array
    {
        $repository = $this->getContainer()->get('ezpublish.api.repository');

        $contentService = $repository->getContentService();
        $contentTypeService = $repository->getContentTypeService();
        $locationService = $repository->getLocationService();

        $contentType = $contentTypeService->loadContentTypeByIdentifier('folder');
        $locationCreateStruct = $locationService->newLocationCreateStruct($content->contentInfo->mainLocationId);

        $children = [];
        for ($i = 0; $i < $limit; ++$i) {
            $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');

            $contentCreateStruct->setField('name', 'Folder ' . $i);

            $child = $contentService->createContent($contentCreateStruct, [$locationCreateStruct]);

            $children[] = $contentService->publishVersion($child->versionInfo);
        }

        return $children;
    }
}
