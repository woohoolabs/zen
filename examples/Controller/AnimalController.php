<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples\Controller;

use WoohooLabs\Zen\Examples\Service\AnimalServiceInterface;
use WoohooLabs\Zen\Examples\Utils\AnimalUtil;

class AnimalController extends AbstractController
{
    /**
     * @Inject
     * @var AnimalUtil
     */
    private $util;

    /**
     * @Inject
     * @var AnimalServiceInterface
     */
    private $service;
}
