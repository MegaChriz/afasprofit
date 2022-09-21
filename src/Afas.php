<?php

namespace Afas;

use Afas\Core\Entity\Discovery;
use Afas\Core\Entity\EntityManager;
use Afas\Core\Entity\EntityValidator;
use Afas\Core\Mapping\EntityMappingFactory;
use Afas\Core\Locale\CountryManager;
use Afas\Core\Soap\DefaultSoapClientFactory;
use Afas\Core\XSD\SchemaManager;
use LogicException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Static Service Container wrapper.
 */
class Afas {

  /**
   * The currently active container object, or NULL if not initialized yet.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface|null
   */
  protected static $container;

  /**
   * Sets a new global container.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   A new container instance to replace the current.
   */
  public static function setContainer(ContainerInterface $container) {
    static::$container = $container;
  }

  /**
   * Unsets the global container.
   */
  public static function unsetContainer() {
    static::$container = NULL;
  }

  /**
   * Returns the currently active global container.
   *
   * @return \Symfony\Component\DependencyInjection\ContainerInterface
   *   The current active container.
   */
  public static function getContainer() {
    if (static::$container === NULL) {
      static::setDefaultContainer();
    }
    return static::$container;
  }

  /**
   * Returns if the container has been initialized.
   *
   * @return bool
   *   True if the container has been initialized, false otherwise.
   */
  public static function hasContainer() {
    return static::$container !== NULL;
  }

  /**
   * Retrieves a service from the container.
   *
   * Use this method if the desired service is not one of those with a dedicated
   * accessor method below. If it is listed below, those methods are preferred
   * as they can return useful type hints.
   *
   * @param string $id
   *   The ID of the service to retrieve.
   *
   * @return mixed
   *   The specified service.
   */
  public static function service($id) {
    return static::getContainer()->get($id);
  }

  /**
   * Returns whether or not the symfony event dispatcher component is installed.
   *
   * @return bool
   *   True if it is installed. False otherwise.
   */
  public static function hasEventDispatcher(): bool {
    return static::getEventDispatcherClass() ? TRUE : FALSE;
  }

  /**
   * Returns the symfony event dispatcher class in case it exists.
   *
   * @return string|null
   *   The class to use if there is one. Null otherwise.
   */
  public static function getEventDispatcherClass(): ?string {
    if (class_exists('\Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher')) {
      return ContainerAwareEventDispatcher::class;
    }
    elseif (class_exists('\Symfony\Component\EventDispatcher\EventDispatcher')) {
      return EventDispatcher::class;
    }
    return NULL;
  }

  /**
   * Adds a subscriber service.
   *
   * @param string $id
   *   The service identifier.
   * @param string $class
   *   The service class.
   *
   * @return \Symfony\Component\DependencyInjection\Definition
   *   The created service definition.
   *
   * @throws \LogicException
   *   In case the symfony event dispatcher component is not installed.
   */
  public static function addSubscriberService($id, $class) {
    $event_dispatcher_class = static::getEventDispatcherClass();
    if (!$event_dispatcher_class) {
      throw new LogicException('The symfony event dispatcher component is not installed.');
    }

    $container = static::getContainer();

    // Register service.
    $definition = $container->register($id, $class);

    // Symfony 3.3.
    if ($event_dispatcher_class == ContainerAwareEventDispatcher::class) {
      // Add subscriber.
      $container->getDefinition('event_dispatcher')
        ->addMethodCall('addSubscriberService', [$id, $class]);
    }
    // Symfony 4.0+.
    elseif ($event_dispatcher_class == EventDispatcher::class) {
      // Add subscriber.
      $container->getDefinition('event_dispatcher')
        ->addMethodCall('addSubscriber', [$container->get($id)]);
    }

    return $definition;
  }

  /**
   * Instantiates a container with default services.
   */
  public static function setDefaultContainer() {
    $container = new ContainerBuilder();
    $container->register('afas.entity.discovery', Discovery::class);
    $container->register('afas.entity.manager', EntityManager::class);
    $container->register('afas.entity.mapping_factory', EntityMappingFactory::class);
    $container->register('afas.entity.validator', EntityValidator::class);
    $container->register('afas.country.manager', CountryManager::class);
    $container->register('afas.soap_client_factory', DefaultSoapClientFactory::class);
    $container->register('afas.xsd_schema.manager', SchemaManager::class);

    // Optionally add event dispatcher.
    $event_dispatcher_class = static::getEventDispatcherClass();
    if ($event_dispatcher_class) {
      $container->register('event_dispatcher', $event_dispatcher_class)
        ->setArguments([$container]);
    }

    static::setContainer($container);
  }

}
