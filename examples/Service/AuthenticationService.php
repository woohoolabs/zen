<?php
declare(strict_types=1);

namespace WoohooLabs\Zen\Examples\Service;

use WoohooLabs\Zen\Examples\Utils\UserUtil;

class AuthenticationService
{
    /**
     * @var UserUtil
     */
    private $util;

    public function __construct(UserUtil $util)
    {
        $this->util = $util;
    }
}
