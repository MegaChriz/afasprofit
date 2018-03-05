<?php

namespace Afas\Tests\Core\Entity;

use Afas\Core\Entity\EntityValidator;
use Afas\Core\Entity\EntityInterface;
use Afas\Core\Entity\EntityContainerInterface;
use Afas\Core\XSD\SchemaManager;
use Afas\Tests\TestBase;
use PHPUnit_Framework_MockObject_Matcher_InvokedRecorder;

/**
 * @coversDefaultClass \Afas\Core\Entity\EntityValidator
 * @group AfasCoreEntity
 */
class EntityValidatorTest extends TestBase {

  /**
   * The class under test.
   *
   * @var \Afas\Core\Entity\EntityValidator
   */
  protected $validator;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->validator = new EntityValidator();
  }

  /**
   * Returns a mocked entity.
   *
   * @param array $errors
   *   (optional) The expected errors.
   * @param array $arguments
   *   (optional) Return values for other methods.
   * @param \PHPUnit_Framework_MockObject_Matcher_InvokedRecorder $matcher
   *   (optional) How many times the validate function is expected to be called.
   *   Defaults to 'once'.
   *
   * @return \Afas\Core\Entity\EntityInterface
   *   A mocked entity.
   */
  protected function getMockedEntity(array $errors = [], array $arguments = [], PHPUnit_Framework_MockObject_Matcher_InvokedRecorder $matcher = NULL) {
    $arguments += [
      'getObjects' => [],
      'getFields' => [],
    ];

    if (empty($matcher)) {
      $matcher = $this->once();
    }

    $entity = $this->getMock(EntityInterface::class);
    $entity->expects($matcher)
      ->method('validate')
      ->will($this->returnValue($errors));

    foreach ($arguments as $method => $return_value) {
      $entity->expects($this->any())
        ->method($method)
        ->will($this->returnValue($return_value));
    }

    return $entity;
  }

  /**
   * Returns a mocked entity container.
   *
   * @param array $objects
   *   (optional) The child objects.
   *
   * @return \Afas\Core\Entity\EntityContainerInterface
   *   A mocked entity container.
   */
  protected function getMockedEntityContainer(array $objects = []) {
    $container = $this->getMock(EntityContainerInterface::class);
    $container->expects($this->any())
      ->method('getObjects')
      ->will($this->returnValue($objects));

    return $container;
  }

  /**
   * Returns a mocked validator with a mocked schema manager.
   *
   * @param \PHPUnit_Framework_MockObject_Matcher_InvokedRecorder $matcher
   *   (optional) How many times the schema manager is expected to be called.
   *   Defaults to 'at least once'.
   *
   * @return \Afas\Core\Entity\EntityValidator
   *   A mocked entity validator.
   */
  protected function getMockedValidatorWithSchemaManager(PHPUnit_Framework_MockObject_Matcher_InvokedRecorder $matcher = NULL) {
    if (empty($matcher)) {
      $matcher = $this->atLeastOnce();
    }

    $schema_manager = $this->getMock(SchemaManager::class, ['getSchema']);
    $schema_manager->expects($matcher)
      ->method('getSchema')
      ->will($this->returnValue([
        'DummyEntityType' => [
          '#name' => 'DummyEntityType',
          'Element' => [
            '#name' => 'Element',
            '#maxOccurs' => 'unbounded',
            'Fields' => [
              'Foo' => [
                'title' => 'Foo',
                'type' => 'string',
                'minlength' => 1,
                'maxlength' => 15,
                'description' => 'Foo Description',
              ],
              'Bar' => [
                'title' => 'Bar',
                'type' => 'boolean',
                'description' => 'Bar Description',
              ],
              'Baz' => [
                'title' => 'Baz',
                'type' => 'decimal',
                'description' => 'Baz Description',
              ],
              'Qux' => [
                'title' => 'Qux',
                'type' => 'long',
                'description' => 'Qux Description',
              ],
            ],
            'Objects' => [
              '#name' => 'Objects',
              '#minOccurs' => 0,
              'DummyChild' => [
                '#name' => 'DummyChild',
                'Element' => [
                  '#name' => 'Element',
                  '#maxOccurs' => 'unbounded',
                  'Fields' => [
                    'Foo' => [
                      'title' => 'Foo',
                      'type' => 'string',
                      'minlength' => 1,
                      'maxlength' => 15,
                      'description' => 'Foo Description',
                    ],
                  ],
                ],
              ],
            ],
          ],
        ],
      ]));

    $validator = $this->getMock(EntityValidator::class, ['getSchemaManager']);
    $validator->expects($matcher)
      ->method('getSchemaManager')
      ->will($this->returnValue($schema_manager));

    return $validator;
  }

  /**
   * @covers ::validate
   */
  public function testValidateWithEmptyEntityContainer() {
    $this->assertEquals([], $this->validator->validate($this->getMockedEntityContainer()));
  }

  /**
   * @covers ::validate
   * @covers ::validateRecursively
   */
  public function testValidateWithSingleFailingObject() {
    $entity = $this->getMockedEntity(['An error.']);
    $entity_container = $this->getMockedEntityContainer([$entity]);
    $this->assertEquals(['An error.'], $this->validator->validate($entity_container));
  }

  /**
   * @covers ::validate
   * @covers ::validateRecursively
   */
  public function testValidateWithMultipleFailingObjects() {
    $entity1 = $this->getMockedEntity(['An error.']);
    $entity2 = $this->getMockedEntity(['Another error.']);
    $entity_container = $this->getMockedEntityContainer([$entity1, $entity2]);

    $this->assertEquals(['An error.', 'Another error.'], $this->validator->validate($entity_container));
  }

  /**
   * @covers ::validate
   * @covers ::validateRecursively
   */
  public function testValidateWithSomeFailingObjects() {
    $entity1 = $this->getMockedEntity(['An error.']);
    $entity2 = $this->getMockedEntity();
    $entity3 = $this->getMockedEntity(['Another error.']);
    $entity_container = $this->getMockedEntityContainer([
      $entity1,
      $entity2,
      $entity3,
    ]);

    $this->assertEquals(['An error.', 'Another error.'], $this->validator->validate($entity_container));
  }

  /**
   * @covers ::validate
   * @covers ::validateRecursively
   */
  public function testValidateWithMultipleNestedObjects() {
    // Create entities.
    $child_entity1 = $this->getMockedEntity();
    $child_entity2 = $this->getMockedEntity(['Error child entity 2']);
    $entity1 = $this->getMockedEntity(['Error entity 1'], ['getObjects' => [$child_entity1, $child_entity2]]);

    $child_entity3 = $this->getMockedEntity(['Error child entity 3']);
    $entity2 = $this->getMockedEntity(['Error entity 2'], ['getObjects' => [$child_entity3]]);

    $entity_container = $this->getMockedEntityContainer([$entity1, $entity2]);

    $expected = [
      'Error entity 1',
      'Error child entity 2',
      'Error entity 2',
      'Error child entity 3',
    ];
    $this->assertEquals($expected, $this->validator->validate($entity_container));
  }

  /**
   * @covers ::validate
   * @covers ::validateRecursively
   */
  public function testValidateWithKnownEntity() {
    $validator = $this->getMockedValidatorWithSchemaManager($this->atLeastOnce());

    $entity = $this->getMockedEntity();
    $entity->expects($this->any())
      ->method('getEntityType')
      ->will($this->returnValue('DummyEntityType'));
    $entity_container = $this->getMockedEntityContainer([$entity]);

    $this->assertEquals([], $validator->validate($entity_container));
  }

  /**
   * @covers ::validate
   * @covers ::validateRecursively
   */
  public function testValidateWithUnknownEntity() {
    $validator = $this->getMockedValidatorWithSchemaManager($this->atLeastOnce());

    $entity = $this->getMockedEntity([], [], $this->any());
    $entity->expects($this->any())
      ->method('getEntityType')
      ->will($this->returnValue('UnknownEntity'));
    $entity_container = $this->getMockedEntityContainer([$entity]);

    $this->assertEquals(['Unknown type UnknownEntity.'], $validator->validate($entity_container));
  }

  /**
   * @covers ::validate
   * @covers ::validateRecursively
   * @covers ::validateField
   *
   * @dataProvider dataProviderFieldValidation
   */
  public function testValidateWithFields(array $fields, array $expected_errors) {
    $validator = $this->getMockedValidatorWithSchemaManager($this->atLeastOnce());

    $entity = $this->getMockedEntity([], [
      'getFields' => $fields,
    ]);
    $entity->expects($this->any())
      ->method('getEntityType')
      ->will($this->returnValue('DummyEntityType'));
    $entity_container = $this->getMockedEntityContainer([$entity]);

    $this->assertEquals($expected_errors, $validator->validate($entity_container));
  }

  /**
   * Data provider for ::testValidateWithFields.
   */
  public function dataProviderFieldValidation() {
    return [
      [
        // Known property.
        ['Foo' => 'Bar'],
        [],
      ],
      [
        // Unknown property.
        ['FooBar' => 'Bar'],
        ["Unknown property 'FooBar' in 'DummyEntityType'."],
      ],
      [
        // Not a scalar value.
        ['Foo' => []],
        ["The property 'Foo' of 'DummyEntityType' must be scalar."],
      ],
      [
        // Min length.
        ['Foo' => ''],
        ["The property 'Foo' of 'DummyEntityType' must be at least 1 chars long."],
      ],
      [
        // Max length.
        ['Foo' => str_pad('', 16, 'x')],
        ["The property 'Foo' of 'DummyEntityType' must be no longer than 15 chars long."],
      ],
      [
        // Boolean.
        ['Bar' => 0],
        [],
      ],
      [
        // Valid decimal.
        ['Baz' => 0],
        [],
      ],
      [
        // Valid decimal.
        ['Baz' => 1.2],
        [],
      ],
      [
        // Valid decimal.
        ['Baz' => '1.2'],
        [],
      ],
      [
        // Valid decimal.
        ['Baz' => '1'],
        [],
      ],
      [
        // Invalid decimal.
        ['Baz' => 'abc'],
        ["The property 'Baz' of type 'DummyEntityType' must numeric."],
      ],
      [
        // Valid long.
        ['Qux' => 12],
        [],
      ],
      [
        // Valid long.
        ['Qux' => '12'],
        [],
      ],
      [
        // Invalid long.
        ['Qux' => 12.5],
        ["The property 'Qux' of type 'DummyEntityType' must a round number."],
      ],
      [
        // Multiple errors.
        [
          'Foo' => 'Bar',
          'FooBar' => 'Bar',
          'Bar' => FALSE,
          'Baz' => 'Not valid',
          'Qux' => 12.5,
        ],
        [
          "Unknown property 'FooBar' in 'DummyEntityType'.",
          "The property 'Baz' of type 'DummyEntityType' must numeric.",
          "The property 'Qux' of type 'DummyEntityType' must a round number.",
        ],
      ],
    ];
  }

  /**
   * @covers ::validate
   * @covers ::validateRecursively
   */
  public function testValidateWithSchemaWithMultipleNestedObjects() {
    $validator = $this->getMockedValidatorWithSchemaManager($this->atLeastOnce());

    // Create entities.
    $child_entity1 = $this->getMockedEntity([], [
      'getEntityType' => 'DummyChild',
      'getFields' => [
        'Foo' => '',
      ],
    ], $this->any());
    $child_entity2 = $this->getMockedEntity([], [
      'getEntityType' => 'UnknownDummyChild',
    ], $this->any());
    $child_entity3 = $this->getMockedEntity([], [
      'getEntityType' => 'DummyChild',
      'getFields' => [
        'Foo' => 'Bar',
      ],
    ], $this->any());

    $entity1 = $this->getMockedEntity(['Error entity 1'], [
      'getEntityType' => 'DummyEntityType',
      'getObjects' => [$child_entity1, $child_entity2, $child_entity3],
    ]);

    $entity_container = $this->getMockedEntityContainer([$entity1]);

    $expected = [
      'Error entity 1',
      "The property 'Foo' of 'DummyChild' must be at least 1 chars long.",
      'Unknown type UnknownDummyChild.',
    ];
    $this->assertEquals($expected, $validator->validate($entity_container));
  }

}
