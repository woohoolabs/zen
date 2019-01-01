<?php
include_once $this->rootDirectory . '/Fixture/DependencyGraph/Constructor/ConstructorD.php';
include_once $this->rootDirectory . '/Fixture/DependencyGraph/Constructor/ConstructorE.php';

return $this->singletonEntries['WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorE'] = $this->singletonEntries['WoohooLabs\Zen\Tests\Fixture\DependencyGraph\Constructor\ConstructorD'] ?? $this->WoohooLabs__Zen__Tests__Fixture__DependencyGraph__Constructor__ConstructorD();
