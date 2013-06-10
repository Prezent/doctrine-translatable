<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('Prezent\\Tests', __DIR__);
$loader->add('Prezent\\Doctrine\\Translatable', __DIR__ . '/../lib');

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
AnnotationRegistry::registerAutoloadNamespace('Prezent\\Doctrine\\Translatable\\Annotation', __DIR__ . '/../lib');
