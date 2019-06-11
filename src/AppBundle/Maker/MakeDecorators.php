<?php

namespace AppBundle\Maker;

use eZ\Publish\Core\Event\AfterEvent;
use eZ\Publish\Core\Event\BeforeEvent;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Constant;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Property;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class MakeDecorators extends AbstractMaker
{
    /**
     * Return the command name for your maker (e.g. make:report).
     *
     * @return string
     */
    public static function getCommandName(): string
    {
        return 'make:decorators';
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $rootDir = __DIR__ . '/../../../';
        $kernelDir = $rootDir . 'vendor/ezsystems/ezpublish-kernel/';
        $eventDir = $kernelDir . 'eZ/Publish/Core/Event/';

        $eventNamespaceRoot = '\eZ\Publish\Core\Event\\';

        $services = (new Finder())
            ->in($eventDir)
            ->name('/.*Service\.php/')
            ->files()
            ->getIterator();

        foreach ($services as $service) {
            $serviceName = preg_replace('/Service$/', '', pathinfo($service->getPathname())['filename']);
            $serviceEventNamespace = $eventNamespaceRoot . $serviceName;

            dump($serviceName);

            $events = (new Finder())
                ->in($eventDir . '/' . $serviceName)
                ->name('/^Before.*Event\.php$/')
                ->files()
                ->getIterator();

            foreach ($events as $event) {
                $eventName = preg_replace('/Before(.*)Event$/', '\1', pathinfo($event->getPathname())['filename']);
                $actionName = lcfirst($eventName);
                $eventClassName = $serviceEventNamespace . '\\' . $eventName . 'Event';
                $beforeEventClassName = $serviceEventNamespace . '\Before' . $eventName . 'Event';

                $beforeEventReflectionClass = new ReflectionClass($beforeEventClassName);
                $eventReflectionClass = new ReflectionClass($eventClassName);

                $beforeEventConstructor = $beforeEventReflectionClass->getMethod('__construct');
                $eventConstructor = $eventReflectionClass->getMethod('__construct');

                $isActionVoid = count($beforeEventConstructor->getParameters()) === count($eventConstructor->getParameters());

                $parameterName = $isActionVoid ? null : $eventConstructor->getParameters()[0]->getName();
                $parameterType = $isActionVoid ? null : $eventConstructor->getParameters()[0]->getType()->getName();
            }

            die();
        }
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig) { }
    public function configureDependencies(DependencyBuilder $dependencies) { }
}