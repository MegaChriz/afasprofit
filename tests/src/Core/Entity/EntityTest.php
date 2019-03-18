<?php

namespace Afas\Tests\Core\Entity;

use Afas\Core\Entity\Entity;
use Afas\Core\Entity\EntityContainerInterface;
use Afas\Core\Entity\EntityInterface;
use Afas\Core\Exception\EntityValidationException;
use Afas\Core\Exception\UndefinedParentException;
use Afas\Core\Mapping\MappingBase;
use Afas\Core\Mapping\MappingInterface;
use Afas\Tests\TestBase;
use DOMDocument;
use InvalidArgumentException;

/**
 * @coversDefaultClass \Afas\Core\Entity\Entity
 * @group AfasCoreEntity
 */
class EntityTest extends TestBase {

  /**
   * The entity under test.
   *
   * @var \Afas\Core\Entity\Entity
   */
  protected $entity;

  /**
   * The entity values.
   *
   * @var array
   */
  protected $values;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->values = [
      'Foo' => 'Bar',
    ];

    $this->entity = new Entity($this->values, 'DummyEntityType');
  }

  /**
   * Returns XML.
   *
   * @return string
   *   An XML string.
   */
  protected function getXml() {
    $doc = new DOMDocument();
    $element = $this->entity->toXml($doc);
    return $doc->saveXML($element);
  }

  /**
   * Asserts that two XML strings are equal wrapped with a root element.
   *
   * The root element has the namespace 'xsi' registered.
   *
   * @param string $expected
   *   The expected XML string.
   * @param string $actual
   *   The actual XML string.
   */
  protected function assertXmlStringEqualsXmlStringWithWrappedRootElement($expected, $actual) {
    $expected = '<Root xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . $expected . '</Root>';
    $actual = '<Root xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . $actual . '</Root>';
    return $this->assertXmlStringEqualsXmlString($expected, $actual);
  }

  /**
   * @covers ::getEntityType
   */
  public function testGetEntityType() {
    $this->assertEquals('DummyEntityType', $this->entity->getEntityType());
  }

  /**
   * @covers ::getType
   */
  public function testGetType() {
    $this->assertEquals('DummyEntityType', $this->entity->getType());
  }

  /**
   * @covers ::getField
   */
  public function testGetField() {
    $this->assertEquals($this->values['Foo'], $this->entity->getField('Foo'));

    // Also ensure NULL is returned for non-existing fields.
    $this->assertNull($this->entity->getField('NonExisting'));
  }

  /**
   * @covers ::getFields
   */
  public function testGetFields() {
    $this->assertEquals($this->values, $this->entity->getFields());
  }

  /**
   * @covers ::fieldExists
   */
  public function testFieldExists() {
    $this->assertTrue($this->entity->fieldExists('Foo'));
    $this->assertFalse($this->entity->fieldExists('Bar'));
  }

  /**
   * @covers ::getRequiredFields
   */
  public function testGetRequiredFields() {
    $this->assertEquals([], $this->entity->getRequiredFields());
  }

  /**
   * @covers ::getAttribute
   */
  public function testGetAttribute() {
    $this->entity->fromArray([
      '@attributes' => [
        'Qux' => 1200,
      ],
    ]);

    $this->assertEquals(1200, $this->entity->getAttribute('Qux'));
  }

  /**
   * @covers ::getObjects
   */
  public function testGetObjects() {
    // Create two objects.
    $objects = [
      $this->createMock(EntityInterface::class),
      $this->createMock(EntityInterface::class),
    ];

    foreach ($objects as $object) {
      $this->entity->addObject($object);
    }

    $this->assertEquals($objects, array_values($this->entity->getObjects()));
  }

  /**
   * @covers ::containsObject
   */
  public function testContainsObject() {
    $subentity = $this->createMock(EntityInterface::class);
    $this->assertFalse($this->entity->containsObject($subentity));

    // Now add the subentity.
    $this->entity->addObject($subentity);
    $this->assertTrue($this->entity->containsObject($subentity));
  }

  /**
   * @covers ::getAction
   */
  public function testGetAction() {
    $this->assertEquals(EntityInterface::FIELDS_INSERT, $this->entity->getAction());
  }

  /**
   * @covers ::toArray
   */
  public function testToArray() {
    $this->assertEquals($this->values, $this->entity->toArray());
  }

  /**
   * @covers ::toArray
   * @covers ::setAttribute
   */
  public function testToArrayWithAttributes() {
    $this->entity->setAttribute('DbId', 12345);

    $expected = [
      '@attributes' => [
        'DbId' => 12345,
      ],
      'Foo' => 'Bar',
    ];
    $this->assertEquals($expected, $this->entity->toArray());
  }

  /**
   * @covers ::toArray
   * @covers ::add
   */
  public function testToArrayWithObjects() {
    $this->entity->add('DummyItem');
    $this->entity->add('DummyItem', ['Baz' => 'Qux']);

    $expected = $this->values + [
      'DummyItem' => [
        0 => [],
        1 => [
          'Baz' => 'Qux',
        ],
      ],
    ];
    $this->assertEquals($expected, $this->entity->toArray());
  }

  /**
   * @covers ::toXml
   */
  public function testToXml() {
    $expected = '<Element>
      <Fields Action="insert">
        <Foo>Bar</Foo>
      </Fields>
    </Element>';
    $this->assertXmlStringEqualsXmlStringWithWrappedRootElement($expected, $this->getXml());
  }

  /**
   * @covers ::toXml
   */
  public function testToXmlWithEmptyFields() {
    $this->entity->setField('Qux', '');
    $this->entity->setField('Boo', NULL);

    $expected = '<Element>
      <Fields Action="insert">
        <Foo>Bar</Foo>
        <Qux xsi:nil="true"/>
        <Boo xsi:nil="true"/>
      </Fields>
    </Element>';
    $this->assertXmlStringEqualsXmlStringWithWrappedRootElement($expected, $this->getXml());
  }

  /**
   * @covers ::toXml
   */
  public function testToXmlWithOneObject() {
    $this->entity->add('FooObject', ['Bar' => 'Baz']);

    $expected = '<Element>
      <Fields Action="insert">
        <Foo>Bar</Foo>
      </Fields>
      <Objects>
        <FooObject>
          <Element>
            <Fields Action="insert">
              <Bar>Baz</Bar>
            </Fields>
          </Element>
        </FooObject>
      </Objects>
    </Element>';
    $this->assertXmlStringEqualsXmlStringWithWrappedRootElement($expected, $this->getXml());
  }

  /**
   * @covers ::toXml
   */
  public function testToXmlWithAttributes() {
    $this->entity->setAttribute('Foo', 'Bar');

    $expected = '<Element Foo="Bar">
      <Fields Action="insert">
        <Foo>Bar</Foo>
      </Fields>
    </Element>';
    $this->assertXmlStringEqualsXmlStringWithWrappedRootElement($expected, $this->getXml());

    // Second attribute.
    $this->entity->setAttribute('Baz', 'Qux');
    $expected = '<Element Baz="Qux" Foo="Bar">
      <Fields Action="insert">
        <Foo>Bar</Foo>
      </Fields>
    </Element>';
    $this->assertXmlStringEqualsXmlStringWithWrappedRootElement($expected, $this->getXml());
  }

  /**
   * @covers ::toXml
   */
  public function testToXmlWithMultipleObjects() {
    $this->entity->add('FooObject', ['Bar' => 'Baz']);
    $this->entity->add('QuxObject', ['ItCd' => 2]);
    $this->entity->add('BarObject', ['DbId' => 1234]);
    $this->entity->add('FooObject', ['Bar' => 'Norf']);

    $expected = '<Element>
      <Fields Action="insert">
        <Foo>Bar</Foo>
      </Fields>
      <Objects>
        <FooObject>
          <Element>
            <Fields Action="insert">
              <Bar>Baz</Bar>
            </Fields>
          </Element>
          <Element>
            <Fields Action="insert">
              <Bar>Norf</Bar>
            </Fields>
          </Element>
        </FooObject>
        <QuxObject>
          <Element>
            <Fields Action="insert">
              <ItCd>2</ItCd>
            </Fields>
          </Element>
        </QuxObject>
        <BarObject>
          <Element>
            <Fields Action="insert">
              <DbId>1234</DbId>
            </Fields>
          </Element>
        </BarObject>
      </Objects>
    </Element>';
    $this->assertXmlStringEqualsXmlStringWithWrappedRootElement($expected, $this->getXml());
  }

  /**
   * @covers ::toXml
   */
  public function testToXmlWithMultipleNestedObjects() {
    $object = $this->entity->add('FooObject', ['Bar' => 'Baz']);
    $object->add('BarObject', ['DbId' => 1234]);

    $expected = '<Element>
      <Fields Action="insert">
        <Foo>Bar</Foo>
      </Fields>
      <Objects>
        <FooObject>
          <Element>
            <Fields Action="insert">
              <Bar>Baz</Bar>
            </Fields>
            <Objects>
              <BarObject>
                <Element>
                  <Fields Action="insert">
                    <DbId>1234</DbId>
                  </Fields>
                </Element>
              </BarObject>
            </Objects>
          </Element>
        </FooObject>
      </Objects>
    </Element>';
    $this->assertXmlStringEqualsXmlStringWithWrappedRootElement($expected, $this->getXml());
  }

  /**
   * @covers ::toXml
   */
  public function testToXmlWithoutDom() {
    $doc = new DOMDocument();
    $element = $this->entity->toXml();
    $doc->appendChild($doc->importNode($element, TRUE));

    $expected = '<Element>
      <Fields Action="insert">
        <Foo>Bar</Foo>
      </Fields>
    </Element>';
    $this->assertXmlStringEqualsXmlString($expected, $doc->saveXML());
  }

  /**
   * @covers ::getParent
   * @covers ::add
   */
  public function testGetParentWithParent() {
    $subentity = $this->entity->add('Dummy2');
    $this->assertSame($this->entity, $subentity->getParent());
  }

  /**
   * @covers ::getParent
   * @covers ::addObject
   */
  public function testGetParentWithParentAndAddingObjectDirectly() {
    $subentity = new Entity([], 'Dummy2');
    $this->entity->addObject($subentity);
    $this->assertSame($this->entity, $subentity->getParent());
  }

  /**
   * @covers ::getParent
   */
  public function testGetParentWithoutParent() {
    // A new entity does not have a parent.
    $this->expectException(UndefinedParentException::class);
    $this->entity->getParent();
  }

  /**
   * @covers ::setField
   * @covers ::toArray
   */
  public function testSetField() {
    // Set a new value.
    $this->entity->setField('Baz', 'Qux');
    $expected = $this->values + [
      'Baz' => 'Qux',
    ];
    $this->assertEquals($expected, $this->entity->toArray());

    // Set an existing value.
    $this->entity->setField('Baz', 'Foo');
    $expected = $this->values + [
      'Baz' => 'Foo',
    ];
    $this->assertEquals($expected, $this->entity->toArray());
  }

  /**
   * @covers ::setField
   * @covers ::compile
   */
  public function testSetFieldWithBoolean() {
    $this->entity->setField('Baz', TRUE);
    $this->entity->setField('Qux', FALSE);

    $expected = '<DummyEntityType xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
      <Element>
        <Fields Action="insert">
          <Foo>Bar</Foo>
          <Baz>1</Baz>
          <Qux>0</Qux>
        </Fields>
      </Element>
    </DummyEntityType>';
    $this->assertXmlStringEqualsXmlString($expected, $this->entity->compile());
  }

  /**
   * @covers ::setField
   * @covers ::toArray
   */
  public function testSetFieldWithMapping() {
    $entity = $this->getMockBuilder(Entity::class)
      ->setConstructorArgs([[], 'DummyEntityType'])
      ->setMethods(['map'])
      ->getMock();
    $entity->expects($this->once())
      ->method('map')
      ->with('Foo')
      ->will($this->returnValue(['Bar', 'Baz']));

    $entity->setField('Foo', 'Qux');

    $expected = [
      'Bar' => 'Qux',
      'Baz' => 'Qux',
    ];
    $this->assertEquals($expected, $entity->toArray());
  }

  /**
   * @covers ::removeField
   * @covers ::getField
   * @covers ::toArray
   */
  public function testRemoveField() {
    $this->entity->removeField('Foo');
    $this->assertEquals(NULL, $this->entity->getField('Foo'));
    $array = $this->entity->toArray();
    $this->assertFalse(isset($array['Foo']));
  }

  /**
   * @covers ::removeField
   * @covers ::getField
   * @covers ::toArray
   */
  public function testRemoveFieldWithMapping() {
    $entity = $this->getMockBuilder(Entity::class)
      ->setConstructorArgs([[], 'DummyEntityType'])
      ->setMethods(['map'])
      ->getMock();

    // 'Foo' is an alias for 'Bar' and 'Baz'. Other values return themselves.
    $entity->expects($this->any())
      ->method('map')
      ->will($this->returnValueMap([
        ['Foo', ['Bar', 'Baz']],
        ['Bar', ['Bar']],
        ['Baz', ['Baz']],
        ['Hello', ['Hello']],
      ]));

    // Set values. Make sure the value map set above works as expected.
    $expected = [
      'Bar' => 'Qux',
      'Baz' => 'Qux',
      'Hello' => 'World',
    ];
    $entity->fromArray($expected);
    $this->assertEquals($expected, $entity->toArray());

    // Now remove field by using the alias name.
    $entity->removeField('Foo');
    $expected = [
      'Hello' => 'World',
    ];
    $this->assertEquals($expected, $entity->toArray());
  }

  /**
   * @covers ::setAttribute
   * @covers ::toArray
   */
  public function testSetAttribute() {
    // Set a new value.
    $this->entity->setAttribute('Baz', 'Qux');
    $expected = [
      '@attributes' => [
        'Baz' => 'Qux',
      ],
    ] + $this->values;
    $this->assertEquals($expected, $this->entity->toArray());

    // Set an existing value.
    $this->entity->setAttribute('Baz', 'Foo');
    $expected = [
      '@attributes' => [
        'Baz' => 'Foo',
      ],
    ] + $this->values;
    $this->assertEquals($expected, $this->entity->toArray());
  }

  /**
   * @covers ::setAttribute
   * @covers ::compile
   */
  public function testSetAttributeWithBoolean() {
    $this->entity->setAttribute('Baz', TRUE);
    $this->entity->setAttribute('Qux', FALSE);

    $expected = '<DummyEntityType xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
      <Element Baz="1" Qux="0">
        <Fields Action="insert">
          <Foo>Bar</Foo>
        </Fields>
      </Element>
    </DummyEntityType>';
    $this->assertXmlStringEqualsXmlString($expected, $this->entity->compile());
  }

  /**
   * @covers ::removeAttribute
   * @covers ::getAttribute
   * @covers ::toArray
   */
  public function testRemoveAttribute() {
    // First, set an attribute.
    $this->entity->setAttribute('Baz', 'Qux');
    $array = $this->entity->toArray();
    $this->assertTrue(isset($array['@attributes']));

    // Now remove it.
    $this->entity->removeAttribute('Baz');
    $this->assertEquals(NULL, $this->entity->getAttribute('Baz'));
    $array = $this->entity->toArray();
    $this->assertFalse(isset($array['@attributes']));
  }

  /**
   * @covers ::add
   */
  public function testAdd() {
    $entity2 = $this->entity->add('Dummy');
    $this->assertNotSame($this->entity, $entity2);
    $this->assertInstanceOf('Afas\Core\Entity\Entity', $entity2);
  }

  /**
   * @covers ::addObject
   * @covers ::__construct
   */
  public function testAddObject() {
    $entity2 = new Entity([], 'Dummy2');
    $this->assertSame($this->entity, $this->entity->addObject($entity2));
  }

  /**
   * @covers ::addObject
   * @covers ::__construct
   */
  public function testAddObjectWithInvalidObject() {
    $entity = $this->getMockBuilder(Entity::class)
      ->setConstructorArgs([$this->values, 'DummyEntityType'])
      ->setMethods(['isValidChild'])
      ->getMock();
    $entity->expects($this->once())
      ->method('isValidChild')
      ->will($this->returnValue(FALSE));

    $entity2 = new Entity([], 'Dummy2');

    $this->expectException(InvalidArgumentException::class);
    $entity->addObject($entity2);
  }

  /**
   * @covers ::removeObject
   */
  public function testRemoveObject() {
    $entity2 = new Entity([], 'Dummy2');
    $this->entity->addObject($entity2);
    $this->assertCount(1, $this->entity->getObjects());
    $this->entity->removeObject($entity2);
    $this->assertEquals([], $this->entity->getObjects());
  }

  /**
   * @covers ::setAction
   * @covers ::compile
   */
  public function testSetActionWithUpdate() {
    $this->entity->setAction(EntityInterface::FIELDS_UPDATE);
    $expected = '<DummyEntityType xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
      <Element>
        <Fields Action="update">
          <Foo>Bar</Foo>
        </Fields>
      </Element>
    </DummyEntityType>';
    $this->assertXmlStringEqualsXmlString($expected, $this->entity->compile());
  }

  /**
   * @covers ::setAction
   * @covers ::compile
   */
  public function testSetActionWithDelete() {
    $this->entity->setAction(EntityInterface::FIELDS_DELETE);
    $expected = '<DummyEntityType xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
      <Element>
        <Fields Action="delete">
          <Foo>Bar</Foo>
        </Fields>
      </Element>
    </DummyEntityType>';
    $this->assertXmlStringEqualsXmlString($expected, $this->entity->compile());
  }

  /**
   * @covers ::setAction
   */
  public function testSetInvalidAction() {
    $this->expectException(InvalidArgumentException::class);
    $this->entity->setAction('Qux');
  }

  /**
   * @covers ::fromArray
   * @covers ::getField
   */
  public function testFromArray() {
    $values = [
      'Qux' => 'Foo',
    ];
    $this->entity->fromArray($values);

    // Assert the field exists.
    $this->assertEquals('Foo', $this->entity->getField('Qux'));
  }

  /**
   * @covers ::fromArray
   * @covers ::getField
   */
  public function testFromArrayWithSingleObject() {
    $values = [
      'DummyItem' => [
        'Bar' => 'Baz',
      ],
    ];
    $this->entity->fromArray($values);

    // Assert the object exists.
    $objects = $this->entity->getObjects();
    $object = current($objects);
    $this->assertEquals('DummyItem', $object->getEntityType());
    $this->assertEquals('Baz', $object->getField('Bar'));
  }

  /**
   * @covers ::fromArray
   * @covers ::getField
   */
  public function testFromArrayWithMultipleObjects() {
    $values = [
      'DummyItem' => [
        [
          'Bar' => 'Baz',
        ],
        [
          'Bar' => 'Qux',
        ],
      ],
    ];
    $this->entity->fromArray($values);

    // Assert the object exists.
    $objects = array_values($this->entity->getObjects());
    $this->assertEquals('DummyItem', $objects[0]->getEntityType());
    $this->assertEquals('Baz', $objects[0]->getField('Bar'));
    $this->assertEquals('DummyItem', $objects[1]->getEntityType());
    $this->assertEquals('Qux', $objects[1]->getField('Bar'));
  }

  /**
   * @covers ::fromArray
   * @covers ::getField
   */
  public function testFromArrayWithAttributes() {
    $values = [
      '@attributes' => [
        'Qux' => 'Foo',
      ],
    ];
    $this->entity->fromArray($values);

    $this->assertEquals('Foo', $this->entity->getAttribute('Qux'));
    $this->assertEquals(NULL, $this->entity->getAttribute('NonExistingAttribute'));
  }

  /**
   * @covers ::setMapper
   * @covers ::map
   */
  public function testMapping() {
    $mapper = $this->createMock(MappingInterface::class);
    $mapper->expects($this->once())
      ->method('map')
      ->with('Foo')
      ->will($this->returnValue(['Bar']));

    $this->assertEquals($this->entity, $this->entity->setMapper($mapper));

    // Assert that 'Foo' is being mapped to 'Bar'.
    $this->assertEquals(['Bar'], $this->entity->map('Foo'));
  }

  /**
   * @covers ::unsetMapper
   * @covers ::map
   */
  public function testMappingWithoutMapper() {
    $this->assertSame($this->entity, $this->entity->unsetMapper());

    // Assert that 'Foo' is being mapped to 'Bar'.
    $this->assertEquals(['Foo'], $this->entity->map('Foo'));
  }

  /**
   * @covers ::setMapper
   * @covers ::map
   */
  public function testMappingWithBaseClass() {
    $mapper = $this->getMockForAbstractClass(MappingBase::class);
    $mapper->expects($this->once())
      ->method('getMappings')
      ->will($this->returnValue([
        'Foo' => 'Bar',
        'Baz' => 'Qux',
      ]));

    $this->assertEquals($this->entity, $this->entity->setMapper($mapper));

    // Assert that 'Foo' is being mapped to 'Bar'.
    $this->assertEquals(['Bar'], $this->entity->map('Foo'));
    // Assert that 'Baz' is being mapped to 'Qux'.
    $this->assertEquals(['Qux'], $this->entity->map('Baz'));

    // Assert that 'Qux' stays 'Qux'.
    $this->assertEquals(['Qux'], $this->entity->map('Qux'));
  }

  /**
   * @covers ::setField
   * @covers ::compile
   */
  public function testCreateEntityWithDefaultMapping() {
    $entity = new Entity([
      '@attributes' => [
        'customer_id' => 100001,
      ],
      'order_id' => 'test001',
    ], 'FbSales');

    $expected = '<FbSales xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
      <Element DbId="100001">
        <Fields Action="insert">
          <OrNu>test001</OrNu>
        </Fields>
      </Element>
    </FbSales>';
    $this->assertXmlStringEqualsXmlString($expected, $entity->compile());
  }

  /**
   * @covers ::setParent
   */
  public function testSetParent() {
    $container = $this->createMock(EntityContainerInterface::class);
    $container->expects($this->once())
      ->method('containsObject')
      ->will($this->returnValue(TRUE));

    $this->assertSame($this->entity, $this->entity->setParent($container));
  }

  /**
   * @covers ::setParent
   */
  public function testSetInvalidParent() {
    $subentity = new Entity([], 'Dummy2');
    $this->expectException(InvalidArgumentException::class);
    $subentity->setParent($this->entity);
  }

  /**
   * @covers ::setSingleObjectData
   * @covers ::getObjects
   */
  public function testSetSingleObjectData() {
    $subentity = $this->callProtectedMethod($this->entity, 'setSingleObjectData', [
      'Dummy2',
      ['Foo' => 'Bar', 'Baz' => 'Qux'],
    ]);

    // Assert type.
    $this->assertInstanceOf(EntityInterface::class, $subentity);
    $this->assertEquals('Dummy2', $subentity->getType());

    // Assert fields.
    $this->assertEquals('Bar', $subentity->getField('Foo'));
    $this->assertEquals('Qux', $subentity->getField('Baz'));

    // Assert that the object was added.
    $objects = $this->entity->getObjects();
    $this->assertSame($subentity, reset($objects));

    // Change value of 'Foo'.
    $this->callProtectedMethod($this->entity, 'setSingleObjectData', [
      'Dummy2',
      ['Foo' => 'Corge'],
    ]);

    // Assert that 'Foo' was changed, but 'Baz' was not.
    $this->assertEquals('Corge', $subentity->getField('Foo'));
    $this->assertEquals('Qux', $subentity->getField('Baz'));

    // Assert that there is only one object.
    $this->assertCount(1, $this->entity->getObjects());
  }

  /**
   * @covers ::setSingleObjectData
   */
  public function testSetSingleObjectDataException() {
    $this->expectException(InvalidArgumentException::class);
    $this->callProtectedMethod($this->entity, 'setSingleObjectData', [
      45,
      ['Foo' => 'Bar', 'Baz' => 'Qux'],
    ]);
  }

  /**
   * @covers ::validate
   */
  public function testValidate() {
    $this->assertInternalType('array', $this->entity->validate());
  }

  /**
   * @covers ::validate
   */
  public function testValidateWithRequiredFields() {
    $entity = $this->getMockBuilder(Entity::class)
      ->setConstructorArgs([$this->values, 'DummyEntityType'])
      ->setMethods(['getRequiredFields'])
      ->getMock();
    $entity->expects($this->once())
      ->method('getRequiredFields')
      ->will($this->returnValue(['Foo', 'Bar', 'Qux']));

    // 'Foo' is already set.
    $expected = [
      'Bar is a required field for type DummyEntityType.',
      'Qux is a required field for type DummyEntityType.',
    ];
    $this->assertEquals($expected, $entity->validate());
  }

  /**
   * @covers ::compile
   */
  public function testCompile() {
    $expected = '<DummyEntityType xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
      <Element>
        <Fields Action="insert">
          <Foo>Bar</Foo>
        </Fields>
      </Element>
    </DummyEntityType>';
    $this->assertXmlStringEqualsXmlString($expected, $this->entity->compile());
  }

  /**
   * @covers ::compile
   * @covers ::isValidationEnabled
   */
  public function testCompileWithValidation() {
    $entity = $this->getMockBuilder(Entity::class)
      ->setConstructorArgs([[], 'DummyEntityType'])
      ->setMethods(['validate'])
      ->getMock();
    $entity->expects($this->once())
      ->method('validate')
      ->will($this->returnValue(['An error.']));

    $this->expectException(EntityValidationException::class);
    $entity->compile();
  }

  /**
   * @covers ::compile
   * @covers ::enableValidation
   * @covers ::disableValidation
   * @covers ::isValidationEnabled
   */
  public function testCompileWithAndWithoutValidation() {
    $entity = $this->getMockBuilder(Entity::class)
      ->setConstructorArgs([[], 'DummyEntityType'])
      ->setMethods(['validate'])
      ->getMock();
    $entity->expects($this->once())
      ->method('validate')
      ->will($this->returnValue(['An error.']));

    // Disable validation.
    $entity->disableValidation();

    $expected = '<DummyEntityType xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
      <Element>
        <Fields Action="insert"/>
      </Element>
    </DummyEntityType>';
    $this->assertXmlStringEqualsXmlString($expected, $entity->compile());

    // Enable validation.
    $entity->enableValidation();
    $this->expectException(EntityValidationException::class);
    $entity->compile();
  }

  /**
   * @covers ::mustValidate
   */
  public function testMustValidate() {
    $entity = $this->getMockBuilder(Entity::class)
      ->setConstructorArgs([$this->values, 'DummyEntityType'])
      ->setMethods(['validate'])
      ->getMock();
    $entity->expects($this->once())
      ->method('validate')
      ->will($this->returnValue([]));

    $this->assertNull($this->callProtectedMethod($entity, 'mustValidate'));
  }

  /**
   * @covers ::mustValidate
   */
  public function testMustValidateException() {
    $entity = $this->getMockBuilder(Entity::class)
      ->setConstructorArgs([$this->values, 'DummyEntityType'])
      ->setMethods(['validate'])
      ->getMock();
    $entity->expects($this->once())
      ->method('validate')
      ->will($this->returnValue(['An error.']));

    $this->expectException(EntityValidationException::class);
    $this->callProtectedMethod($entity, 'mustValidate');
  }

}
