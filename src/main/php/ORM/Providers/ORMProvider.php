<?php

namespace HHIT\Doctrine\Illuminate\ORM\Providers;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use HHIT\Doctrine\DBAL\Contracts\DBALConnectionFactory;
use HHIT\Doctrine\Illuminate\ORM\IlluminateEntityManagerConfigurationSource;
use HHIT\Doctrine\ORM\BindEntity;
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
