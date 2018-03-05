<?php

namespace Afas\Tests\resources\Plugin;

use Afas\Core\Entity\Entity;

/**
 * A plugin which should *not* be discovered.
 *
 * @see \Afas\Tests\Core\Entity\DiscoveryTest::testIndexDir()
 */
abstract class AbstractPlugin extends Entity {}
