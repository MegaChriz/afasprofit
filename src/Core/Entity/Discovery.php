<?php

namespace Afas\Core\Entity;

use Drupal\Component\Plugin\Discovery\StaticDiscovery;
use Symfony\Component\Finder\Finder;
use hanneskod\classtools\Iterator\ClassIterator;

/**
 * Default class for discovering entity plugins.
 */
class Discovery extends StaticDiscovery {

  /**
   * Constructs a new Discovery object.
   */
  public function __construct() {
    $this->setDefinition('Entity', [
      'class' => Entity::class,
    ]);

    $finder = new Finder();
    $iterator = new ClassIterator($finder->in(__DIR__ . '/Plugin'));

    foreach ($iterator->getClassMap() as $class_name => $file_info) {
      $path = explode('\\', $class_name);
      $plugin_id = array_pop($path);

      $this->setDefinition($plugin_id, [
        'class' => $class_name,
      ]);
    }
  }

}
