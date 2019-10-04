<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples\Infrastructure;

use WoohooLabs\Zen\Examples\Domain\AnimalRepositoryInterface;
use WoohooLabs\Zen\Examples\Utils\AnimalUtil;

class MysqlAnimalRepository implements AnimalRepositoryInterface
{
    private AnimalUtil $util;

    public function __construct(AnimalUtil $util)
    {
        $this->util = $util;
    }
}
