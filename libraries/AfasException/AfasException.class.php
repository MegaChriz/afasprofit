<?php

/**
 * @file
 * AFAS Exception class.
 */

/**
 * Base class for Afas Exceptions.
 *
 * @todo Maybe move these errors to specific classes, so they are grouped better.
 */
class AfasException extends Exception {
  // --------------------------------------------------------------
  // CONSTANTS
  // --------------------------------------------------------------

  /**
   * Occurs when an item already exists in Profit.
   */
  const ERROR_APP_ALREADY_EXISTS = "Afas: (AntaObject) The primary key already exists for field 'Organisatie/persoon'";

  /**
   * Occurs when inserting duplicate rows.
   */
  const ERROR_DUPLICATE_KEY = "Afas: [Microsoft][ODBC SQL Server Driver][SQL Server]Cannot insert duplicate key row in object 'dbo.AfasKnSalRelation' with unique index 'AfasKnBasicContact'.";

  /**
   * Occurs when connection fails.
   */
  const ERROR_CONNECTION = "Afas: Could not connect to host";

  /**
   * Occurs when fetching data from Profit fails.
   */
  const ERROR_FETCHING = "Afas: Error Fetching http headers";

  /**
   * Occurs when organisation exists multiple times in Profit.
   */
  const ERROR_DOUBLE_ORG = "Afas: General message: Er is meer dan 1 organisatie gevonden die voldoet aan het zoekprofiel.";

  /**
   * Occurs in case of an incorrect address.
   */
  const ERROR_ADDRESS_POSTBUS_NRT = "Afas: General message: Als het veld 'Postbusadres' is aangevinkt, mag 'Toevoeging aan huisnummer' niet ingevuld zijn.";

  /**
   * Occurs in case of an invalid VAT number.
   */
  const ERROR_VAT_NUMBER = "Afas: The number didn't pass the mod 11 check";

  /**
   * Occurs when organisation already has an invoice contact.
   */
  const ERROR_INVOICE_CONTACT = "SoapFault: sending invoice contact to Afas leaded to errors: General message: Type rapport mag niet vaker dan één keer voorkomen.";

  /**
   * Occurs when an item already exists in Profit.
   */
  const ERROR_CONTACT_ALREADY_EXISTS = "Afas: (AntaObject) The primary key already exists for field 'Nummer'";

  /**
   * Occurs when sending a person without gender.
   */
  const ERROR_REQUIRED_GENDER = "Afas: General message: Bij een persoon is het geslacht verplicht.";

  /**
   * Occurs when an address has errors.
   */
  const ERROR_ADDRESS_INCOMPLETE = "Afas: General message: Een onvolledig adres mag niet geïmporteerd worden.";

  /**
   * Occurs when an item already exists in Profit.
   */
  const ERROR_PERSON_ALREADY_EXISTS = "Afas: General message: Er is meer dan 1 contactpersoon gevonden die voldoet aan het zoekprofiel.";

  /**
   * Occurs when an object is incomplete.
   */
  const ERROR_INCOMPLETE_OBJECT = "Afas: Server was unable to process request. ---> The work item caused an exception, see the inner exception for details ---> Object reference not set to an instance of an object.";
}