<?php

namespace Afas\Core\Entity;

use DOMDocument;
use Exception;
use InvalidArgumentException;
use Afas\Component\ItemList\ItemList;

/**
 * Class containing items to send to Profit.
 */
class EntityContainer extends ItemList implements EntityContainerInterface {

  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * The update connector to use.
   *
   * @var string
   */
  protected $connectorType;

  /**
   * The entity factory.
   *
   * @var \Afas\Core\Entity\EntityFactoryInterface
   */
  private $factory;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Constructs a new EntityContainer object.
   *
   * @param string $connector_type
   *   The update connector to use.
   * @param \Afas\Core\Entity\EntityFactoryInterface $factory
   *   (optional) The factory to use.
   *   Defaults to \Afas\Core\Entity\EntityFactoryInterface.
   */
  public function __construct($connector_type, EntityFactoryInterface $factory = NULL) {
    $this->connectorType = $connector_type;
    if (!isset($factory)) {
      $factory = new EntityFactory();
    }
    $this->setFactory($factory);
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function add($entity_type, array $values = array()) {
    $entity = $this->factory->createEntity($entity_type, $values);
    $this->addObject($entity);
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function addObject(EntityInterface $entity) {
    $this->addItem($entity);
    return $this;
  }

  /**
   * Sets the factory that generates the objects.
   *
   * @param \Afas\Core\Entity\EntityFactoryInterface $factory
   *   The factory that generates entity objects.
   *
   * @return void
   */
  public function setFactory(EntityFactoryInterface $factory) {
    $this->factory = $factory;
  }

  /**
   * {@inheritdoc}
   */
  protected function addItem($item, $key = NULL) {
    if (!($item instanceof EntityInterface)) {
      throw new InvalidArgumentException('\Afas\Core\Entity\Entity\EntityContainer::addItem() only accepts instances of \Afas\Core\Entity\Entity\EntityInterface.');
    }
    return parent::addItem($item);
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getObjects() {
    return $this->getItems();
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function compile() {
    if ($this->count()) {
      $doc = new DOMDocument();

      // Create root element.
      $root = $doc->createElement($this->connectorType);
      $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
      $doc->appendChild($root);

      // Add childs.
      foreach ($this->getItems() as $entity) {
        $node = $entity->toXML($doc);
        $root->appendChild($node);
      }

      return $doc->saveXML($root);
    }
  }

  /**
   * Implements PHP magic __toString().
   *
   * Converts the filter group to a string.
   *
   * @return string
   *   A string version of the entity container.
   */
  public function __toString() {
    try {
      $result = $this->compile();
    }
    catch (Exception $e) {
      // __toString() must not throw an exception.
    }
    if (empty($result)) {
      return '';
    }
    return $result;
  }

}
