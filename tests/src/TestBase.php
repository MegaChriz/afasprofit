<?php

namespace Afas\Tests;

use PHPUnit_Framework_TestCase;

/**
 * Base class for Afas Profit tests.
 */
abstract class TestBase extends PHPUnit_Framework_TestCase {

  /**
   * Returns the contents of one of the file resources.
   *
   * @param string $file
   *   The path of the file to get, starting in resources directory.
   *
   * @return string
   *   The file contents.
   */
  protected function getFileContents($file) {
    $filename = dirname(__DIR__) . '/resources/' . $file;

    // Check if the file exists.
    if (!is_file($filename)) {
      throw new \Exception(strtr('File @filename not found.', [
        '@filename' => $filename,
      ]));
    }

    return file_get_contents($filename);
  }

}
