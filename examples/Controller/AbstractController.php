<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples\Controller;

use WoohooLabs\Zen\Examples\Service\AuthenticationService;

abstract class AbstractController implements ControllerInterface
{
    /**
     * @Inject
     * @var AuthenticationService
     */
    protected $authenticationService;
}
