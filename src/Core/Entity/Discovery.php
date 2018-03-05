<?php

namespace Afas\Core\Entity;

use Afas\Component\classtools\Iterator\Filter\AbstractClassFilter;
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

    $this->indexDir(__DIR__ . '/Plugin');
  }

  /**
   * Scans a directory for class files and registers these as plugins.
   *
   * @param string $path
   *   The directory to explore.
   */
  public function indexDir($dir) {
    $finder = new Finder();
    $iterator = new ClassIterator($finder->in($dir));

    // Filter out abstract classes.
    $abstract = $iterator->filter(new AbstractClassFilter());

    foreach ($iterator->not($abstract) as $class_name => $file_info) {
      $path = explode('\\', $class_name);
      $plugin_id = array_pop($path);

      $this->setDefinition($plugin_id, [
        'class' => $class_name,
      ]);
    }
  }

}
