<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/** @var ContainerInterface $container */
$container = require __DIR__.'/../config/bootstrap.php';

return $container->get(EntityManagerInterface::class);
