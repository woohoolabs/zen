<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Examples\Controller;

use WoohooLabs\Dicone\Examples\Service\PlantServiceInterface;
use WoohooLabs\Dicone\Examples\Utils\PlantUtil;
use WoohooLabs\Dicone\Examples\View\PlantView;

class PlantController
{
    /**
     * @Inject
     * @var PlantUtil
     */
    private $util;

    /**
     * @Inject
     * @var PlantServiceInterface
     */
    private $service;

    /**
     * @Inject
     * @var PlantView
     */
    private $view;
}
