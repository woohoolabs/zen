<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples\Controller\Authentication;

use WoohooLabs\Zen\Examples\Service\AuthenticationService;

class AuthenticationController
{
    #[Inject]
    protected AuthenticationService $service;
}
