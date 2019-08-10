<?php

include_once $this->rootDirectory . '/src/Container/Definition/DefinitionInterface.php';
include_once $this->rootDirectory . '/src/Container/Definition/AbstractDefinition.php';
include_once $this->rootDirectory . '/tests/Double/TestDefinition.php';
include_once $this->rootDirectory . '/tests/Double/StubPrototypeDefinition.php';

self::$entryPoints['WoohooLabs\Zen\Tests\Double\StubPrototypeDefinition'] = 'WoohooLabs__Zen__Tests__Double__StubPrototypeDefinition';

return require __DIR__ . '/WoohooLabs__Zen__Tests__Double__StubPrototypeDefinition.php';
