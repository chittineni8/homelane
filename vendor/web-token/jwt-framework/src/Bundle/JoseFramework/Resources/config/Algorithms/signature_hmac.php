<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2020 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use Jose\Component\Signature\Algorithm;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {
    $container = $container->services()->defaults()
        ->private()
        ->autoconfigure()
        ->autowire()
    ;

    $container->set(Algorithm\HS256::class)
        ->tag('jose.algorithm', ['alias' => 'HS256'])
    ;

    $container->set(Algorithm\HS384::class)
        ->tag('jose.algorithm', ['alias' => 'HS384'])
    ;

    $container->set(Algorithm\HS512::class)
        ->tag('jose.algorithm', ['alias' => 'HS512'])
    ;
};
