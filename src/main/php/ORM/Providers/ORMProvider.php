<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2018 Hendrik Heneke
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace HHIT\Doctrine\Illuminate\ORM\Providers;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use HHIT\Doctrine\DBAL\Contracts\DBALConnectionFactory;
use HHIT\Doctrine\Illuminate\ORM\IlluminateEntityManagerConfigurationSource;
use HHIT\Doctrine\ORM\Annotation\BindEntity;
use HHIT\Doctrine\ORM\Contracts\EntityManagerConfigurationFactory;
use HHIT\Doctrine\ORM\Contracts\EntityManagerConfigurationSource;
use HHIT\Doctrine\ORM\Contracts\EntityManagerFactory;
use HHIT\Doctrine\ORM\EntityManagerDefaultConfigurationFactory;
use HHIT\Doctrine\ORM\EntityManagerDefaultFactory;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class ORMProvider extends ServiceProvider
{
    protected $defer = true;

    public function provides()
    {
        return [
            EntityManagerConfigurationSource::class,
            EntityManagerConfigurationFactory::class,
            Configuration::class,
            EntityManagerFactory::class,
            EntityManagerInterface::class,
        ];
    }

    public function register()
    {
        $this->registerConfigurationSource();
        $this->registerConfigurationFactory();
        $this->registerEntityManagerFactory();
    }

    public function boot(Configuration $configuration, AnnotationReader $annotationReader, Router $router, EntityManagerInterface $entityManager)
    {
        $this->bindEntities($configuration, $annotationReader, $router, $entityManager);
    }

    protected function registerConfigurationSource()
    {
        $this->app->singleton(EntityManagerConfigurationSource::class, IlluminateEntityManagerConfigurationSource::class);
    }

    protected function registerConfigurationFactory()
    {
        $this->app->singleton(EntityManagerConfigurationFactory::class, function () {
            return new EntityManagerDefaultConfigurationFactory(
                $this->app->make(EntityManagerConfigurationSource::class),
                $this->createCache()
            );
        });
        $this->app->singleton(Configuration::class, function () {
            return $this->app->make(EntityManagerConfigurationFactory::class)->createConfiguration();
        });
    }

    protected function registerEntityManagerFactory()
    {
        $this->app->singleton(EntityManagerFactory::class, function () {
            return new EntityManagerDefaultFactory(
                $this->app->make(EntityManagerConfigurationFactory::class),
                $this->app->make(DBALConnectionFactory::class),
                $this->createEventManager()
            );
        });
        $this->app->singleton(EntityManagerInterface::class, function () {
            return $this->app->make(EntityManagerFactory::class)->createEntityManager();
        });
    }

    protected function registerAnnotationReader()
    {
        if (!$this->app->bound(AnnotationReader::class)) {
            $this->app->singleton(AnnotationReader::class, function () {
                return new AnnotationReader();
            });
        }
    }

    protected function createCache()
    {
        if ($this->app->environment('production')) {
            $path = storage_path('doctrine/cache');
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            return new FilesystemCache(realpath($path));
        }

        return null;
    }

    protected function createEventManager()
    {
        return null;
    }

    protected function bindEntities(Configuration $configuration, AnnotationReader $reader, Router $router, EntityManagerInterface $entityManager)
    {
        foreach ($configuration->getMetadataDriverImpl()->getAllClassNames() as $className) {
            $reflectionClass = new \ReflectionClass($className);
            /**
             * @var $bindEntityAnnotation BindEntity
             */
            $bindEntityAnnotation = $reader->getClassAnnotation($reflectionClass, BindEntity::class);
            if ($bindEntityAnnotation != null) {
                $router->bind($bindEntityAnnotation->name, function ($value) use ($className, $entityManager) {
                    $entityInstance = $entityManager->getRepository($className)->find($value);
                    return $entityInstance ?: abort(404, "Entity {$className}:{$value} not found!");
                });
            }
        }
    }
}
