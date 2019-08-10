<?php
        include_once $this->rootDirectory . '/MixedD.php';
        include_once $this->rootDirectory . '/MixedE.php';

        self::$entryPoints['WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Mixed\MixedE'] = 'WoohooLabs__Zen__Tests__Fixture__DependencyGraph__Mixed__MixedE';

        return $this->WoohooLabs__Zen__Tests__Fixture__DependencyGraph__Mixed__MixedE();
