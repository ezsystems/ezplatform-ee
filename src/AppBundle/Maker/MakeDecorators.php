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
        $root = __DIR__ . '/../../../';
        $kernel = $root . 'vendor/ezsystems/ezpublish-kernel/';

        $finder = new Finder();

        $services = $finder
            ->in($kernel . 'eZ/Publish/API/Repository')
            ->name('/.*Service\.php/')
            ->files()
            ->getIterator();

        $filesystem = new Filesystem();

        foreach ($services as $service) {
            $filename = pathinfo($service->getPathname())['filename'];
            $serviceClass = 'eZ\Publish\API\Repository\\' . $filename;
            $serviceName = str_replace('Service', '', $filename);
            $filesystem->mkdir($kernel . 'eZ/Publish/Core/Event/' . $serviceName);

//            $file = $this->getDecoratorFile(new ReflectionClass($serviceClass));
//            $test = $this->getDecoratorFileTest(new ReflectionClass($serviceClass));
            $eventService = $this->getEventService(new ReflectionClass($serviceClass), $serviceName);
            $events = $this->getEvents(new ReflectionClass($serviceClass), $serviceName);
            $eventsFile = $this->getEventsFile($serviceName, $events);
//
//            file_put_contents($kernel . 'eZ/Publish/SPI/Repository/Decorator/' . $filename . 'Decorator.php', (string)$file);
//            file_put_contents($kernel . 'eZ/Publish/SPI/Repository/Tests/Decorator/' . $filename . 'DecoratorTest.php', (string)$test);
            file_put_contents($kernel . 'eZ/Publish/Core/Event/' . $serviceName . '/'  . $serviceName . 'Events.php', (string)$eventsFile);
            file_put_contents($kernel . 'eZ/Publish/Core/Event/' . $filename . '.php', (string)$eventService);
            foreach ($events as $eventName => $event) {
                file_put_contents($kernel . 'eZ/Publish/Core/Event/' . $serviceName . '/' . $eventName . 'Event.php', (string)$event);
            }
        }
    }

    public function getSetterAndGetter(ReflectionParameter $parameter)
    {
        $name = $parameter->getName();
        $method = ucfirst($name);

        $getter = new Method('get' . $method);
        $getter->setVisibility('public');
        $getter->setBody('return $this->' . $name . ';');

        $argument = new Parameter($name);

        if ($parameter->hasType()) {
            $getter->setReturnType($parameter->getType()->getName());
            $argument->setTypeHint($parameter->getType()->getName());
        }

        $setter = new Method('set' . $method);
        $setter->setVisibility('public');
        $setter->setReturnType('void');
        $setter->setParameters([$argument]);
        $setter->setBody('$this->' . $name . ' = $' . $name . ';');

        return [
            $getter,
            // $setter,
        ];
    }

    public function getEvents(ReflectionClass $reflectionClass, $serviceName)
    {
        $events = [];

        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            $imports = [];

            $eventName = ucfirst($reflectionMethod->getName());

            if ($reflectionMethod->hasReturnType()) {
                $type = $reflectionMethod->getReturnType();
                if ($type && !$type->isBuiltin()) {
                    $imports[] = $type->getName();
                }
            }

            $parameters = [];
            $properties = [];
            $methods = [];

            foreach ($reflectionMethod->getParameters() as $parameter) {
                $property = new Property($parameter->getName());
                $property->setVisibility('private');

                array_push($methods, ...$this->getSetterAndGetter($parameter));
                $param = new Parameter($parameter->getName());

                if ($parameter->hasType()) {
                    $type = $parameter->getType();
                    $property->setComment('@var ' . $type);
                    $param->setTypeHint($type->getName());

                    if ($type && !$type->isBuiltin()) {
                        $imports[] = $type->getName();
                    }
                }

                $properties[] = $property;
                $parameters[] = $param;
            }

            $returnValueParameter = new Parameter('returnValue');
            $returnValueProperty = new Property('returnValue');

            $isVoid = $reflectionMethod->hasReturnType() && $reflectionMethod->getReturnType()->getName() === 'void';

            $afterParameters = $isVoid
                ? $parameters
                : array_merge($parameters, [$returnValueParameter]);

            $afterProperties = $isVoid
                ? $properties
                : array_merge($properties, [$returnValueProperty]);

            $constructor = new Method('__construct');
            $constructor->setVisibility('public');
            $constructor->setParameters($afterParameters);
            $constructor->setBody(implode("\r", array_map(function($item) { return '$this->' . $item->getName() . ' = $' . $item->getName() . ';'; }, $afterParameters)));

            $parts = array_filter(preg_split('/(?=[A-Z])/', $eventName), function ($item) { return !empty($item); });

            $const = new Constant('NAME');
            $const->setVisibility('public');
            $const->setValue(implode('.', array_merge(['ezplatform', 'event'], array_map('strtolower', array_reverse($parts)))));

            $file = new PhpFile();
            $file->setStrictTypes(true);
            $returnValueGetter = new Method('getReturnValue');
            $returnValueGetter->setVisibility('public');
            $returnValueGetter->setBody('return $this->returnValue;');

            $class = new ClassType(Str::getShortClassName($eventName . 'Event'));
            $class->addExtend(AfterEvent::class);
            $class->setFinal(true);
            $class->setProperties($afterProperties);
            $class->setMethods(array_merge([$constructor], $methods, [$returnValueGetter]));
            $class->setConstants([$const]);

            $namespace = $file->addNamespace('eZ\Publish\Core\Event\\' . $serviceName);
            $namespace->add($class);

            $namespace->addUse(AfterEvent::class);
            foreach ($imports as $import) {
                $namespace->addUse($import);
            }

            $events[$eventName] = $file;

            $eventName = 'Before' . $eventName;

            $constructor = new Method('__construct');
            $constructor->setVisibility('public');
            $constructor->setParameters($parameters);
            $constructor->setBody(implode("\r", array_map(function($item) { return '$this->' . $item->getName() . ' = $' . $item->getName() . ';'; }, $parameters)));

            $file = new PhpFile();
            $file->setStrictTypes(true);

            $class = new ClassType(Str::getShortClassName($eventName . 'Event'));
            $class->addExtend(BeforeEvent::class);
            $class->setFinal(true);
            $class->setProperties($properties);
            $class->setMethods(array_merge([$constructor], $methods));

            $beforeConst = new Constant('NAME');
            $beforeConst->setVisibility('public');
            $beforeConst->setValue($const->getValue() . '.before');

            $class->setConstants([$beforeConst]);

            $namespace = $file->addNamespace('eZ\Publish\Core\Event\\' . $serviceName);
            $namespace->add($class);

            $namespace->addUse(BeforeEvent::class);
            foreach ($imports as $import) {
                $namespace->addUse($import);
            }

            $events[$eventName] = $file;
        }

        return $events;
    }

    public function getEventsFile($name, $events)
    {
        $file = new PhpFile();
        $file->setStrictTypes(true);
        $namespace = $file->addNamespace('eZ\Publish\Core\Event\\' . $name);

        $class = new ClassType(Str::getShortClassName($name . 'Events'));
        $class->setFinal(true);

        $consts = [];

        foreach ($events as $eventName => $event) {
            $parts = array_filter(preg_split('/(?=[A-Z])/', $eventName), function ($item) { return !empty($item); });
            $const = new Constant(implode('_', array_map('strtoupper', $parts)));
            $const->setVisibility('public');
            $const->setValue($eventName . 'Event::NAME');
            $consts[] = $const;
        }

        $class->setConstants($consts);

        $namespace->add($class);

        return $file;
    }

    public function getEventService(ReflectionClass $reflectionClass, $serviceName)
    {
        $file = new PhpFile();
        $file->setStrictTypes(true);
        $namespace = $file->addNamespace('eZ\Publish\Core\Event');

        $imports = [
            'eZ\Publish\Core\Event\\' . $serviceName . '\\' . $serviceName . 'Events',
            EventDispatcherInterface::class,
        ];

        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->hasReturnType()) {
                $type = $method->getReturnType();
                if ($type && !$type->isBuiltin()) {
                    $imports[] = $type->getName();
                }
            }

            foreach ($method->getParameters() as $parameter) {
                if ($parameter->hasType()) {
                    $type = $parameter->getType();
                    if ($type && !$type->isBuiltin()) {
                        $imports[] = $type->getName();
                    }
                }
            }
        }

        array_unique($imports);

        $namespace->addUse($reflectionClass->getName(),Str::getShortClassName($reflectionClass->getName() . 'Interface'));

        $class = new ClassType(Str::getShortClassName($reflectionClass->getName()));
        $class->isFinal();
        $class->addImplement($reflectionClass->getName());

        $eventDispatcherProperty = new Property('eventDispatcher');
        $eventDispatcherProperty->setVisibility('protected');
        $eventDispatcherProperty->setComment('@var ' . EventDispatcherInterface::class);

        $class->setProperties([$eventDispatcherProperty]);

        $constructor = new Method('__construct');
        $constructor->setVisibility('public');
        $constructor->setBody(implode("\r", [
            'parent::__construct($innerRepository);',
            '',
            '$this->eventDispatcher = $eventDispatcher;',
        ]));
        $innerServiceParameter = new Parameter('innerService');
        $innerServiceParameter->setTypeHint($reflectionClass->getName());
        $eventDispatcherParameter = new Parameter('eventDispatcher');
        $eventDispatcherParameter->setTypeHint(EventDispatcherInterface::class);
        $constructor->setParameters([
            $innerServiceParameter,
            $eventDispatcherParameter,
        ]);

        $methods = [$constructor];
        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            if ($reflectionMethod->isPublic()) {
                $method = new Method($reflectionMethod->getName());
                $method->setVisibility('public');

                if ($reflectionMethod->hasReturnType()) {
                    $method->setReturnType($reflectionMethod->getReturnType());
                }

                $parameters = [];

                foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
                    $parameter = new Parameter($reflectionParameter->getName());

                    if ($reflectionParameter->hasType()) {
                        $parameter->setTypeHint($reflectionParameter->getType());
                    }

                    if ($reflectionParameter->isDefaultValueAvailable()) {
                        $parameter->setDefaultValue($reflectionParameter->getDefaultValue());
                    }

                    $parameters[] = $parameter;
                }
                $method->setParameters($parameters);

                $isVoid = $reflectionMethod->hasReturnType() && $reflectionMethod->getReturnType()->getName() === 'void';
                $eventData = array_map(function($item) { return '$' . $item->getName(); }, $reflectionMethod->getParameters());

                $eventDataString = count($eventData) > 1
                    ? "\r" . implode("\r", array_map(function($item) { return "\t$item,"; }, $eventData)) . "\r"
                    : array_pop($eventData);

                $eventName = ucfirst($reflectionMethod->getName());
                $parts = array_filter(preg_split('/(?=[A-Z])/', $eventName), function ($item) { return !empty($item); });
                $eventConst = implode('_', array_map('strtoupper', $parts));

                $imports[] = 'eZ\Publish\Core\Event\\' . $serviceName . '\\' . $eventName . 'Event';
                $imports[] = 'eZ\Publish\Core\Event\\' . $serviceName . '\Before' . $eventName . 'Event';

                $bodyLines = [
                    '$eventData = [' . $eventDataString . '];',
                    '',
                    '$beforeEvent = new Before' . $eventName . 'Event(...$eventData);',
                    'if ($this->eventDispatcher->dispatch(' . $serviceName . 'Events::BEFORE_' . $eventConst . ', $beforeEvent)->isPropagationStopped()) {',
                    "\t" . 'return' . ($isVoid ? ';' : ' $beforeEvent->getReturnValue();'),
                    '} else {',
                    "\t" . ($isVoid ? '' : '$result = ') . 'parent::' . $reflectionMethod->getName() . '(' . implode(', ', array_map(function($item) { return '$' . $item->getName(); }, $parameters)) . ');',
                    '}',
                    '',
                    '$this->eventDispatcher->dispatch(',
                    "\t" . $serviceName . 'Events::' . $eventConst . ',',
                    "\t" . 'new ' . $eventName . 'Event(...$eventData' . ($isVoid ? '' : ', $result') . ')',
                    ');',
                ];

                if (!$isVoid) {
                    $bodyLines[] = '';
                    $bodyLines[] = 'return $result;';
                }

                $body = implode("\r", $bodyLines);

                $method->setBody($body);

                $methods[] = $method;
            }
        }

        $class->setMethods($methods);
        $namespace->add($class);

        foreach ($imports as $import) {
            $namespace->addUse($import);
        }

        return $file;
    }

//    public function getDecoratorFile(ReflectionClass $reflectionClass): PhpFile
//    {
//        $file = new PhpFile();
//        $file->setStrictTypes(true);
//        $namespace = $file->addNamespace('eZ\Publish\SPI\Repository\Decorator');
//
//        $imports = [
//            $reflectionClass->getName(),
//        ];
//
//        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
//            if ($method->hasReturnType()) {
//                $type = $method->getReturnType();
//                if ($type && !$type->isBuiltin()) {
//                    $imports[] = $type->getName();
//                }
//            }
//
//            foreach ($method->getParameters() as $parameter) {
//                if ($parameter->hasType()) {
//                    $type = $parameter->getType();
//                    if ($type && !$type->isBuiltin()) {
//                        $imports[] = $type->getName();
//                    }
//                }
//            }
//        }
//
//        array_unique($imports);
//
//        foreach ($imports as $import) {
//            $namespace->addUse($import);
//        }
//
//        $class = new ClassType(Str::getShortClassName($reflectionClass->getName().'Decorator'));
//        $class->setAbstract();
//        $class->addImplement($reflectionClass->getName());
//
//        $innerServiceProperty = new Property('innerService');
//        $innerServiceProperty->setVisibility('protected');
//        $innerServiceProperty->setComment('@var ' . $reflectionClass->getName());
//
//        $class->setProperties([$innerServiceProperty]);
//
//        $constructor = new Method('__construct');
//        $constructor->setVisibility('public');
//        $constructor->setComment('@param ' . $reflectionClass->getName());
//        $constructor->setBody('$this->innerService = $innerService;');
//        $innerServiceParameter = new Parameter('innerService');
//        $innerServiceParameter->setTypeHint($reflectionClass->getName());
//        $constructor->setParameters([$innerServiceParameter]);
//
//        $methods = [$constructor];
//        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
//            if ($reflectionMethod->isPublic()) {
//                $method = new Method($reflectionMethod->getName());
//                $method->setVisibility('public');
//
//                if ($reflectionMethod->hasReturnType()) {
//                    $method->setReturnType($reflectionMethod->getReturnType());
//                }
//
//                $parameters = [];
//
//                foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
//                    $parameter = new Parameter($reflectionParameter->getName());
//
//                    if ($reflectionParameter->hasType()) {
//                        $parameter->setTypeHint($reflectionParameter->getType());
//                    }
//
//                    if ($reflectionParameter->isDefaultValueAvailable()) {
//                        $parameter->setDefaultValue($reflectionParameter->getDefaultValue());
//                    }
//
//                    $parameters[] = $parameter;
//                }
//                $method->setParameters($parameters);
//
//                $body =
//                    '$this->innerService->'
//                    . $reflectionMethod->getName()
//                    . '('
//                    . implode(', ', array_map(function($item) { return '$' . $item->getName(); }, $parameters))
//                    . ');';
//
//                if ($reflectionMethod->hasReturnType() && $reflectionMethod->getReturnType()->getName() !== 'void') {
//                    $body = 'return ' . $body;
//                }
//
//                $method->setBody($body);
//
//                $methods[] = $method;
//            }
//        }
//
//        $class->setMethods($methods);
//        $namespace->add($class);
//
//        return $file;
//    }
//
//    public function getDecoratorFileTest(ReflectionClass $reflectionClass): PhpFile
//    {
//        $file = new PhpFile();
//        $file->setStrictTypes(true);
//        $namespace = $file->addNamespace('eZ\Publish\SPI\Repository\Tests\Decorator');
//
//        $imports = [
//            $reflectionClass->getName(),
//            'eZ\Publish\SPI\Repository\Decorator\\' . Str::getShortClassName($reflectionClass->getName()) . 'Decorator',
//            TestCase::class,
//            MockObject::class,
//        ];
//
//        array_unique($imports);
//
//        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
//            if ($method->hasReturnType()) {
//                $type = $method->getReturnType();
//                if ($type && !$type->isBuiltin()) {
//                    $imports[] = $type->getName();
//                }
//            }
//
//            foreach ($method->getParameters() as $parameter) {
//                if ($parameter->hasType()) {
//                    $type = $parameter->getType();
//                    if ($type && !$type->isBuiltin()) {
//                        $imports[] = $type->getName();
//                    }
//                }
//            }
//        }
//
//        foreach ($imports as $import) {
//            $namespace->addUse($import);
//        }
//
//        $class = new ClassType(Str::getShortClassName($reflectionClass->getName().'DecoratorTest'));
//        $class->addExtend(TestCase::class);
//
//        $serviceParameter = new Parameter('service');
//        $serviceParameter->setTypeHint($reflectionClass->getName());
//
//        $createDecoratorMethod = new Method('createDecorator');
//        $createDecoratorMethod->setVisibility('protected');
//        $createDecoratorMethod->setReturnType($reflectionClass->getName());
//        $createDecoratorMethod->setParameters([$serviceParameter]);
//        $createDecoratorMethod->setBody('return new class($service) extends ' . Str::getShortClassName($reflectionClass->getName()) . 'Decorator {};');
//
//        $createServiceMockMethod = new Method('createServiceMock');
//        $createServiceMockMethod->setVisibility('protected');
//        $createServiceMockMethod->setReturnType(MockObject::class);
//        $createServiceMockMethod->setBody('return $this->createMock(' . Str::getShortClassName($reflectionClass->getName()) . '::class);');
//
//        $methods = [
//            $createDecoratorMethod,
//            $createServiceMockMethod
//        ];
//
//        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
//            if ($reflectionMethod->isPublic()) {
//                $method = new Method('test' . ucfirst($reflectionMethod->getName()) . 'Decorator');
//                $method->setVisibility('public');
//
//                $parameters = [];
//
//                foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
//                    $parameters[] = $this->getParameterValue($reflectionParameter->getType());
//                }
//
//                if (count($parameters) > 1) {
//                    $parameters = array_map(function ($item) { return "\t" . $item; }, $parameters);
//                }
//
//                $parametersString = implode(",\r", $parameters);
//
//                if (count($parameters) > 1) {
//                    $parametersString = "\r" . $parametersString . ",\r";
//                }
//
//                $method->setBody(<<<EOL
//\$serviceMock = \$this->createServiceMock();
//\$decoratedService = \$this->createDecorator(\$serviceMock);
//
//\$parameters = [$parametersString];
//
//\$serviceMock->expects(\$this->exactly(1))->method('{$reflectionMethod->getName()}')->with(...\$parameters);
//
//\$decoratedService->{$reflectionMethod->getName()}(...\$parameters);
//EOL
//);
//
//                $methods[] = $method;
//            }
//        }
//
//        $class->setMethods($methods);
//        $namespace->add($class);
//
//        return $file;
//    }
//
//    public function getParameterValue(?ReflectionNamedType $type)
//    {
//        if ($type === null) {
//            return $this->getRandomValue();
//        } else if ($type->getName() === 'array') {
//            return '[' . $this->getRandomValue() . ']';
//        } else if ($type->getName() === 'int') {
//            return random_int(100, 999);
//        } else if ($type->getName() === 'bool') {
//            return 'true';
//        } else if ($type->getName() === 'string') {
//            return $this->getRandomValue();
//        } else if (class_exists($type->getName())) {
//            return '$this->createMock(' . Str::getShortClassName($type->getName()) . '::class)';
//        }
//
//        dump($type);
//    }
//
//    public function getRandomValue()
//    {
//        return "'" . uniqid('random_value_', true) . "'";
//    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig) { }
    public function configureDependencies(DependencyBuilder $dependencies) { }
}