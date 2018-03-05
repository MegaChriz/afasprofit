<?php

namespace Afas\Core\Locale;

/**
 * Provides a list of countries.
 */
interface CountryManagerInterface {

  /**
   * Get an array of all country code => profit country code pairs.
   *
   * @return array
   *   An array of all country code => profit country code pairs.
   */
  public function getList();

  /**
   * Get an array of all country numcode => country iso 2 code pairs.
   *
   * @return array
   *   An array of all country numcode => country iso 2 code pairs.
   */
  public function getListNum3toIso2();

  /**
   * Returns list of countries codes from CSV file.
   *
   * @return array
   *   A list of country codes, with the following keys:
   *   - coid
   *     Profit's country ID.
   *   - name
   *     Name of the country
   *   - eu
   *   - iso-alpha2
   *   - iso-alpha3
   *   - iso-num3
   *
   * @throws \RuntimeException
   *   In case the kzykhys/php-csv-parser library is not installed.
   */
  public function getListFromCsv();

}
