<?php

namespace Afas\Core\Exception;

use UnexpectedValueException;

/**
 * Thrown in case a dir was not found or not readable.
 */
class DirNotFoundException extends UnexpectedValueException {}
