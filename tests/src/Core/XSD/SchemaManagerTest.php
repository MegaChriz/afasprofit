<?php

namespace Afas\Tests\Core;

use Afas\Core\Exception\DirNotFoundException;
use Afas\Core\Exception\SchemaNotFoundException;
use Afas\Core\XSD\SchemaManager;
use Afas\Tests\TestBase;

/**
 * @coversDefaultClass \Afas\Core\XSD\SchemaManager
 * @group AfasCoreXSD
 */
class SchemaManagerTest extends TestBase {

  /**
   * The schema manager.
   *
   * @var \Afas\Core\XSD\SchemaManager
   */
  protected $manager;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->manager = new SchemaManager();
  }

  /**
   * @covers ::getPaths
   * @covers ::__construct
   */
  public function testGetPaths() {
    $expected = [
      $this->getBasePath() . '/resources/XMLSchema',
    ];
    $this->assertEquals($expected, $this->manager->getPaths());
  }

  /**
   * @covers ::addPath
   * @covers ::getPaths
   */
  public function testAddPath() {
    // Set a path that exists.
    $this->manager->addPath($this->getBasePath() . '/tests/resources');

    $expected = [
      $this->getBasePath() . '/tests/resources',
      $this->getBasePath() . '/resources/XMLSchema',
    ];
    $this->assertEquals($expected, $this->manager->getPaths());
  }

  /**
   * @covers ::addPath
   */
  public function testAddInvalidPath() {
    $this->expectException(DirNotFoundException::class);
    $this->manager->addPath($this->getBasePath() . '/non_existing_dir');
  }

  /**
   * @covers ::__construct
   * @covers ::getPaths
   */
  public function testResetPaths() {
    // Set a path that exists.
    $this->manager->addPath($this->getBasePath() . '/tests/resources');

    // Re-initialize.
    $this->manager->__construct();

    $expected = [
      $this->getBasePath() . '/resources/XMLSchema',
    ];
    $this->assertEquals($expected, $this->manager->getPaths());
  }

  /**
   * @covers ::getSchema
   */
  public function testGetSchema() {
    $schema = $this->manager->getSchema('FbSales');
    $this->assertArrayHasKey('Fields', $schema['FbSales']['Element']);
    $this->assertArrayHasKey('Objects', $schema['FbSales']['Element']);
  }

  /**
   * @covers ::getSchema
   */
  public function testGetNonExistingSchema() {
    $this->expectException(SchemaNotFoundException::class);
    $this->manager->getSchema('DummyType');
  }

}
