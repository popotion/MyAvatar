<?php

namespace App\Controller\Front;

use App\Controller\AppRouteCollectionTrait;
use App\Controller\RouteCollectionInterface;

enum RouteCollection: string implements RouteCollectionInterface
{
    use AppRouteCollectionTrait;

    case HOMEPAGE = 'homepage';
    case AVATAR = 'avatar';
}