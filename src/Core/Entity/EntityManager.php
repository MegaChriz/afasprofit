<?php

namespace Afas\Core\Entity;

use Afas\Afas;
use Drupal\Component\Plugin\PluginManagerBase;
use Drupal\Component\Plugin\FallbackPluginManagerInterface;

/**
 * Manages entity type plugin definitions.
 */
class EntityManager extends PluginManagerBase implements EntityManagerInterface, FallbackPluginManagerInterface {

  /**
   * {@inheritdoc}
   */
  protected function getDiscovery() {
    if (!$this->discovery) {
      $this->discovery = Afas::service('afas.entity.discovery');
    }
    return $this->discovery;
  }

  /**
   * {@inheritdoc}
   */
  protected function getFactory() {
    if (!$this->factory) {
      $this->factory = new EntityFactory($this, EntityInterface::class);
    }
    return $this->factory;
  }

  /**
   * {@inheritdoc}
   */
  public function createInstance($plugin_id, array $values = []) {
    $configuration = [
      'values' => $values,
      'entity_type' => $plugin_id,
    ];
    return parent::createInstance($plugin_id, $configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function getFallbackPluginId($plugin_id, array $configuration = []) {
    return 'Entity';
  }

}
