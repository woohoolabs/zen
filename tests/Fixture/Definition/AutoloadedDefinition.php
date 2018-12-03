<?php
        include_once $this->rootDirectory . '/var/www/tests/Fixture/DependencyGraph/Mixed/MixedE.php';

        self::$entryPoints[\WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Mixed\MixedE::class] = 'WoohooLabs__Zen__Tests__Fixture__DependencyGraph__Mixed__MixedE';

        return $this->WoohooLabs__Zen__Tests__Fixture__DependencyGraph__Mixed__MixedE();
