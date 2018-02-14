<?php

namespace Afas\Tests\Core\Entity;

use Afas\Core\Entity\Entity;
use Afas\Core\Entity\EntityInterface;
use Afas\Tests\TestBase;
use DOMDocument;

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
   * @covers ::getField
   */
  public function testGetField() {
    $this->assertEquals($this->values['Foo'], $this->entity->getField('Foo'));

    // Also ensure NULL is returned for non-existing fields.
    $this->assertNull($this->entity->getField('NonExisting'));
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
      $this->getMock(EntityInterface::class),
      $this->getMock(EntityInterface::class),
    ];

    foreach ($objects as $object) {
      $this->entity->addObject($object);
    }

    $this->assertEquals($objects, $this->entity->getObjects());
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
   * @covers ::setAction
   */
  public function testSetAction() {
    $this->entity->setAction(EntityInterface::FIELDS_UPDATE);
  }

  /**
   * @covers ::setAction
   * @expectedException InvalidArgumentException
   */
  public function testSetInvalidAction() {
    $this->entity->setAction('Qux');
  }

  /**
   * @covers ::fromArray
   * @covers ::getField
   */
  public function testFromArray() {
    $values = [
      'Qux' => 'Foo',
      'DummyItem' => [
        0 => [
          'Bar' => 'Baz',
        ],
      ],
    ];
    $this->entity->fromArray($values);

    // Assert the field exists.
    $this->assertEquals('Foo', $this->entity->getField('Qux'));

    // Assert the object exists.
    $objects = $this->entity->getObjects();
    $object = current($objects);
    $this->assertEquals('DummyItem', $object->getEntityType());
    $this->assertEquals('Baz', $object->getField('Bar'));
  }

  /**
   * @covers ::save
   */
  public function testSave() {
    // @todo
  }

  /**
   * @covers ::delete
   */
  public function testDelete() {
    // @todo
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

}
