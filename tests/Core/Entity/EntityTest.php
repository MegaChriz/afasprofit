<?php

/**
 * @file
 * Contains \Afas\Core\Entity\EntityTest
 */

namespace Afas\Core\Entity;

use \DOMDocument;
use Afas\Core\Entity\Entity;

/**
 * @coversDefaultClass \Afas\Core\Entity\Entity
 * @group AfasCoreEntity
 */
class EntityTest extends \PHPUnit_Framework_TestCase {
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
   * @covers ::id
   */
  public function testId() {
    $this->assertSame($this->values['id'], $this->entity->id());
  }

  /**
   * @covers ::isNew
   * @covers ::enforceIsNew
   */
  public function testIsNew() {
    // We provided an ID, so the entity is not new.
    $this->assertFalse($this->entity->isNew());
    // Force it to be new.
    $this->assertSame($this->entity, $this->entity->enforceIsNew());
    $this->assertTrue($this->entity->isNew());
  }

  /**
   * @covers ::getField
   */
  public function testGetField() {
    $this->assertEquals($this->values['Foo'], $this->entity->getField('Foo'));

    // Also ensure NULL is returned for non-existing fields.
    $this->assertNull($entity->getField('NonExisting'));
  }

  /**
   * @covers ::getObjects
   */
  public function testGetObjects() {
    // Create two objects.
    $objects = [
      $this->getMock('Afas\Core\Entity\EntityInterface'),
      $this->getMock('Afas\Core\Entity\EntityInterface'),
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
    $this->assertEquals($expected, $entity->toArray());
  }

  /**
   * @covers ::toXML
   */
  public function testToXML() {
    $doc = new DOMDocument();
    $element = $this->entity->toXML($doc);

    $expected = '<Element>
      <Fields Action="insert">
        <Foo>Bar</Foo>
      </Fields>
    </Element>';
    $this->assertXmlStringEqualsXmlString($expected, $doc->saveXML($element));
  }

  /**
   * @covers ::toXML
   */
  public function testToXMLWithEmptyFields() {
    $this->entity->setField('Qux', '');
    $this->entity->setField('Boo', NULL);
    $doc = new DOMDocument();
    $element = $this->entity->toXML($doc);

    $expected = '<Element>
      <Fields Action="insert">
        <Foo>Bar</Foo>
        <Qux xsi:nil="true"/>
        <Boo xsi:nil="true"/>
      </Fields>
    </Element>';
    $this->assertXmlStringEqualsXmlString($expected, $doc->saveXML($element));
  }

  /**
   * @covers ::toXML
   */
  public function testToXMLWithOneObject() {
    $this->entity->add('FooObject', ['Bar' => 'Baz']);
    $doc = new DOMDocument();
    $element = $this->entity->toXML($doc);

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
    $this->assertXmlStringEqualsXmlString($expected, $doc->saveXML($element));
  }

  /**
   * @covers ::toXML
   */
  public function testToXMLWithMultipleObjects() {
    $this->entity->add('FooObject', ['Bar' => 'Baz']);
    $this->entity->add('QuxObject', ['ItCd' => 2]);
    $this->entity->add('BarObject', ['DbId' => 1234]);
    $this->entity->add('FooObject', ['Bar' => 'Norf']);
    $doc = new DOMDocument();
    $element = $this->entity->toXML($doc);

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
    $this->assertXmlStringEqualsXmlString($expected, $doc->saveXML($element));
  }

  /**
   * @covers ::toXML
   */
  public function testToXMLWithMultipleNestedObjects() {
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
    $this->assertXmlStringEqualsXmlString($expected, $this->entity->toXML());
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
   * @covers ::toArray
   */
  public function testRemoveField() {
    $this->entity->removeField('Foo');
    $array = $this->entity->toArray();
    $this->assertFalse(isset($array['Foo']));
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
    $this->assertEquals('DummyItem', $object->getEntityTypeId());
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
