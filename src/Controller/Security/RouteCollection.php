<?php

namespace App\Controller\Security;

use App\Controller\AppRouteCollectionTrait;
use App\Controller\RouteCollectionInterface;

enum RouteCollection: string implements RouteCollectionInterface
{
    use AppRouteCollectionTrait;

    case REGISTER = 'security_register';
    case EMAIL_REGISTER = 'verify_email';
}