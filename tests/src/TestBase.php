<?php

namespace Afas\Tests;

use Afas\Afas;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Base class for Afas Profit tests.
 */
abstract class TestBase extends TestCase {

  /**
   * {@inheritdoc}
   */
  protected function tearDown(): void {
    Afas::unsetContainer();
  }

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

  /**
   * Gets a ReflectionMethod for a class method.
   *
   * @param string $class
   *   The class to reflect.
   * @param string $name
   *   The method name to reflect.
   *
   * @return \ReflectionMethod
   *   A ReflectionMethod.
   */
  protected function getMethod($class, $name) {
    $class = new ReflectionClass($class);
    $method = $class->getMethod($name);
    $method->setAccessible(TRUE);
    return $method;
  }

  /**
   * Returns a dynamically created closure for the object's method.
   *
   * @param object $object
   *   The object for which to get a closure.
   * @param string $method
   *   The object's method for which to get a closure.
   *
   * @return \Closure
   *   A Closure object.
   */
  protected function getProtectedClosure($object, $method) {
    return $this->getMethod(get_class($object), $method)->getClosure($object);
  }

  /**
   * Calls a protected method on the given object.
   *
   * @param object $object
   *   The object on which to call a protected method.
   * @param string $method
   *   The protected method to call.
   * @param array $args
   *   The arguments to pass to the method.
   *
   * @return mixed
   *   The result of the method call.
   */
  protected function callProtectedMethod($object, $method, array $args = []) {
    $closure = $this->getProtectedClosure($object, $method);
    return call_user_func_array($closure, $args);
  }

  /**
   * Returns base path.
   *
   * @return string
   *   The base path of this project.
   */
  protected function getBasePath() {
    return dirname(dirname(__DIR__));
  }

}
