<?php

namespace Afas\Core\Entity;

use InvalidArgumentException;

/**
 * Trait for setting actions.
 */
trait ActionTrait {

  /**
   * The fields action.
   *
   * @var string
   */
  protected $action;

  /**
   * {@inheritdoc}
   */
  public function setAction($action) {
    switch ($action) {
      case EntityInterface::FIELDS_INSERT:
      case EntityInterface::FIELDS_UPDATE:
      case EntityInterface::FIELDS_DELETE:
        $this->action = $action;
        $this->updateChilds();
        break;

      default:
        throw new InvalidArgumentException(strtr('Invalid field action %action.', [
          '%action' => $action,
        ]));
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAction() {
    return $this->action;
  }

  /**
   * Updates childs after changing the action.
   */
  abstract protected function updateChilds();

}
