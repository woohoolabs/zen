<?php
include_once $this->rootDirectory . 'MixedD.php';
include_once $this->rootDirectory . 'MixedE.php';

self::$entryPoints[\WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Mixed\MixedE::class] = 'WoohooLabs__Zen__Tests__Fixture__DependencyGraph__Mixed__MixedE';

return require __DIR__ . '/WoohooLabs__Zen__Tests__Fixture__DependencyGraph__Mixed__MixedE.php';
