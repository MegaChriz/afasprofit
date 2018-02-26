<?php

namespace Afas\Core\XSD;

use Afas\Core\Exception\DirNotFoundException;
use Afas\Core\Exception\SchemaNotFoundException;
use Symfony\Component\Finder\Finder;

/**
 * Class for finding and retrieving XSD schema's.
 *
 * @todo add methods to crud paths.
 * @todo add interface.
 */
class SchemaManager {

  /**
   * The paths to look for XSD files.
   *
   * @var array
   */
  private $paths = [];

  /**
   * Constructs a new SchemaManager object.
   */
  public function __construct() {
    $this->addPath(dirname(dirname(dirname(__DIR__))) . '/resources/XMLSchema');
  }

  /**
   * Adds a new path to the list of scanning for XSD files.
   *
   * @param string $path
   *   The path to add.
   *
   * @throws \Afas\Core\Exception\DirNotFoundException
   *   In case the path is not a dir or is not readable.
   */
  public function addPath($path) {
    if (!is_dir($path)) {
      throw new DirNotFoundException(strtr('The !path is not a directory.', [
        '!path' => $path,
      ]));
    }
    if (!is_readable($path)) {
      throw new DirNotFoundException(strtr('The directory !path is not readable.', [
        '!path' => $path,
      ]));
    }

    // Add path to the beginning of the array.
    array_unshift($this->paths, $path);
  }

  /**
   * Returns the definition for a certain update connector.
   *
   * @param string $update_connector
   *   The update connector whose definition to retrieve.
   *
   * @return array
   *   A definition of the update connector.
   *
   * @throws \Afas\Core\Exception\SchemaNotFoundException
   *   In case the XSD file for the update connector could not be found.
   */
  public function getSchema($update_connector) {
    $finder = new Finder();
    $finder->files()
      ->name($update_connector . '.xsd')
      ->in($this->paths);

    $files = iterator_to_array($finder);
    $file = reset($files);
    if (!$file) {
      throw new SchemaNotFoundException(strtr('File for connector !connector not found.', [
        '!connector' => $update_connector,
      ]));
    }

    $reader = new Reader(file_get_contents($file));
    return $reader->getDefinitionArray();
  }

}
