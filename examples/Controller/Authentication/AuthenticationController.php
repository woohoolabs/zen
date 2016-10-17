<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Examples\Controller\Authentication;

use WoohooLabs\Dicone\Annotation\Inject;
use WoohooLabs\Dicone\Examples\Service\AuthenticationService;

class AuthenticationController
{
    /**
     * @Inject
     * @var AuthenticationService
     */
    protected $service;
}
