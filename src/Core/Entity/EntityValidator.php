<?php

namespace Afas\Core\Entity;

use Afas\Afas;
use Afas\Core\Exception\SchemaNotFoundException;

/**
 * Class for validating entities.
 */
class EntityValidator implements EntityValidatorInterface {

  /**
   * Constructs a new EntityValidator object.
   */
  public function __construct() {}

  /**
   * {@inheritdoc}
   */
  public function validate(EntityContainerInterface $container) {
    $errors = [];

    try {
      $schema = $this->getSchemaManager()->getSchema($container->getType());
    }
    catch (SchemaNotFoundException $exception) {
      // Schema is not found. Continue without validating schema.
      $schema = [];
    }

    foreach ($container->getObjects() as $entity) {
      $errors = array_merge($errors, $this->validateRecursively($entity, $schema));
    }

    return $errors;
  }

  /**
   * Returns the XSD schema manager.
   *
   * @return \Afas\Core\XSD\SchemaManager
   *   The XSD schema manager.
   */
  protected function getSchemaManager() {
    return Afas::service('afas.xsd_schema.manager');
  }

  /**
   * Validates entities recursively.
   *
   * @param \Afas\Core\Entity\EntityInterface $entity
   *   The entity to validate.
   * @param array $schema
   *   The schema to validate against.
   *
   * @return array
   *   The errors that occurred.
   */
  protected function validateRecursively(EntityInterface $entity, array $schema = []) {
    $errors = [];

    $entity_type = $entity->getEntityType();

    // Validate against schema.
    if (!empty($schema)) {
      // Check if object type is expected.
      if (!isset($schema[$entity_type])) {
        $errors[] = strtr('Unknown type !type.', [
          '!type' => $entity_type,
        ]);

        // Stop here. No need to validate more rules for this type.
        return $errors;
      }
    }

    // Validate against entity's own rules.
    $errors = array_merge($errors, $entity->validate());

    // Validate fields.
    if (!empty($schema)) {
      foreach ($entity->getFields() as $name => $value) {
        if (!isset($schema[$entity_type]['Element']['Fields'][$name])) {
          $errors[] = strtr("Unknown property '!property' in '!type'.", [
            '!property' => $name,
            '!type' => $entity_type,
          ]);

          // Field is unkown. No need to validate this field further.
          continue;
        }

        $errors = array_merge($errors, $this->validateField($entity, $schema[$entity_type]['Element']['Fields'][$name], $name, $value));
      }
    }

    // Validate child objects.
    foreach ($entity->getObjects() as $entity) {
      if (!empty($schema)) {
        $errors = array_merge($errors, $this->validateRecursively($entity, $schema[$entity_type]['Element']['Objects']));
      }
      else {
        $errors = array_merge($errors, $this->validateRecursively($entity));
      }
    }

    return $errors;
  }

  /**
   * Validates a single field of the entity.
   *
   * @param \Afas\Core\Entity\EntityInterface $entity
   *   The entity to validate.
   * @param array $restrictions
   *   The field restrictions.
   * @param string $name
   *   The field's name.
   * @param string $value
   *   The field's value.
   *
   * @return array
   *   The errors that occurred.
   */
  protected function validateField(EntityInterface $entity, array $restrictions, $name, $value) {
    $errors = [];

    $common_args = [
      '!property' => $name,
      '!type' => $entity->getEntityType(),
      '!value' => @(string) $value,
    ];

    if (is_null($value)) {
      // No further validation needed for this field.
      return $errors;
    }

    if (!is_scalar($value)) {
      $errors[] = strtr("The property '!property' of '!type' must be scalar.", $common_args);

      // Field is not scalar. No need to validate further.
      return $errors;
    }

    // Min length.
    if (isset($restrictions['minlength'])) {
      if (strlen($value) < $restrictions['minlength']) {
        $errors[] = strtr("The property '!property' of '!type' must be at least !number chars long.", [
          '!number' => $restrictions['minlength'],
        ] + $common_args);
      }
    }
    // Max length.
    if (isset($restrictions['maxlength'])) {
      if (strlen($value) > $restrictions['maxlength']) {
        $errors[] = strtr("The property '!property' of '!type' must be no longer than !number chars long.", [
          '!number' => $restrictions['maxlength'],
        ] + $common_args);
      }
    }

    // Correct/convert type.
    if (isset($restrictions['type'])) {
      switch ($restrictions['type']) {
        case 'boolean':
          $value = (bool) $value;
          break;

        case 'long':
        case 'decimal':
          if (!is_numeric($value)) {
            $errors[] = strtr("The property '!property' of type '!type' must numeric.", $common_args);
          }
          elseif ($restrictions['type'] === 'long' && strpos((string) $value, '.') !== FALSE) {
            $errors[] = strtr("The property '!property' of type '!type' must a round number.", $common_args);
          }
          break;

        default:
          $value = trim($value);
      }
    }

    // Set value back to entity (in case that it changed).
    $entity->setField($name, $value);

    return $errors;
  }

}
