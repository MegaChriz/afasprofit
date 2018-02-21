<?php

namespace Afas\Core\Entity;

use Afas\Afas;
use Afas\Component\ItemList\ItemList;
use Afas\Core\Exception\EntityValidationException;
use DOMDocument;
use Exception;
use InvalidArgumentException;

/**
 * Class containing items to send to Profit.
 */
class EntityContainer extends ItemList implements EntityContainerInterface {

  use ActionTrait;
  use EntityCreateTrait;

  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * The update connector to use.
   *
   * @var string
   */
  protected $connectorType;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Constructs a new EntityContainer object.
   *
   * @param string $connector_type
   *   The update connector to use.
   * @param \Afas\Core\Entity\EntityManagerInterface $manager
   *   (optional) The manager to use.
   *   Defaults to \Afas\Core\Entity\EntityManager.
   */
  public function __construct($connector_type, EntityManagerInterface $manager = NULL) {
    $this->connectorType = $connector_type;
    if (!isset($manager)) {
      $manager = Afas::service('afas.entity.manager');
    }
    $this->setManager($manager);
    $this->setAction(EntityInterface::FIELDS_INSERT);
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

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
   * @param \Afas\Core\Entity\EntityManagerInterface $manager
   *   The entity manager.
   */
  public function setManager(EntityManagerInterface $manager) {
    $this->manager = $manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getManager() {
    return $this->manager;
  }

  /**
   * {@inheritdoc}
   */
  protected function addItem($item, $key = NULL) {
    if (!($item instanceof EntityInterface)) {
      throw new InvalidArgumentException('\Afas\Core\Entity\EntityContainer::addItem() only accepts instances of \Afas\Core\Entity\EntityInterface.');
    }
    return parent::addItem($item);
  }

  /**
   * {@inheritdoc}
   */
  public function fromArray(array $data) {
    if ($this->isAssociative($data)) {
      $item = $this->add($this->connectorType, $data);
      $item->setAction($this->getAction());
    }
    else {
      foreach ($data as $subdata) {
        $item = $this->add($this->connectorType, $subdata);
        $item->setAction($this->getAction());
      }
    }
    return $this;
  }

  /**
   * Checks if an array is associative.
   */
  protected function isAssociative($arr) {
    foreach ($arr as $key => $value) {
      if (is_string($key)) {
        return TRUE;
      }
    }
    return FALSE;
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

  /**
   * {@inheritdoc}
   */
  public function toArray() {
    foreach ($this->getObjects() as $object) {
      $return[$object->getEntityType()][] = $object->toArray();
    }
    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->connectorType;
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function compile() {
    // Validation *must* pass.
    $this->mustValidate();

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

  /**
   * {@inheritdoc}
   */
  public function validate() {
    return Afas::service('afas.entity.validator')->validate($this);
  }

  /**
   * Validates the entity and throws an exception if validation fails.
   *
   * @throws \Afas\Core\Exception\EntityValidationException
   *   When validation fails.
   */
  protected function mustValidate() {
    $errors = $this->validate();
    if (!empty($errors)) {
      throw new EntityValidationException($this, $errors);
    }
  }

  // --------------------------------------------------------------
  // UTIL
  // --------------------------------------------------------------

  /**
   * Updates childs.
   */
  protected function updateChilds() {
    foreach ($this->getItems() as $item) {
      $item->setAction($this->getAction());
    }
  }

}
