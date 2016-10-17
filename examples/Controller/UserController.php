<?php
declare(strict_types=1);

namespace WoohooLabs\Dicone\Examples\Controller;

use WoohooLabs\Dicone\Annotation\Inject;
use WoohooLabs\Dicone\Examples\Service\AuthenticationService;
use WoohooLabs\Dicone\Examples\Service\UserService;
use WoohooLabs\Dicone\Examples\View\UserView;

class UserController extends AuthenticationService
{
    /**
     * @Inject
     * @var UserService
     */
    private $service;

    /**
     * @Inject
     * @var UserView
     */
    private $view;
}
