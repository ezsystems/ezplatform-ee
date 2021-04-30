<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateContentCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('ezplatform:create-content')
             ->setDescription('Create lots of folders in one go.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getContainer()->get('ezpublish.api.repository');

        $contentService = $repository->getContentService();
        $contentTypeService = $repository->getContentTypeService();
        $locationService = $repository->getLocationService();

        $count = 60;

        try {
            $repository->setCurrentUser($repository->getUserService()->loadUser(14)); // Administrator User

            $contentType = $contentTypeService->loadContentTypeByIdentifier('article');
            $locationCreateStruct = $locationService->newLocationCreateStruct(56);

            for ($i = 0; $i < $count; ++$i) {
                $contentCreateStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');
                $contentCreateStruct->setField('title', 'Content ' . $i);

                $draft = $contentService->createContent($contentCreateStruct, [$locationCreateStruct]);
                $content = $contentService->publishVersion($draft->versionInfo);
                $output->writeln(sprintf('<info>Iteration %d: article created (content ID: %d)</info>', $i, $content->contentInfo->id));
            }
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}
