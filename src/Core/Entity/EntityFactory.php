<?php

namespace Afas\Core\Entity;

use Afas\Afas;
use Drupal\Component\Plugin\Factory\DefaultFactory;

/**
 * Factory for generating entities.
 */
class EntityFactory extends DefaultFactory {

  /**
   * {@inheritdoc}
   */
  public function createInstance($plugin_id, array $configuration = []) {
    $plugin_definition = $this->discovery->getDefinition($plugin_id);
    $plugin_class = static::getPluginClass($plugin_id, $plugin_definition, $this->interface);
    return new $plugin_class($configuration['values'], $configuration['entity_type']);
  }

}
