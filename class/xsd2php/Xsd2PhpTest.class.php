<?php

set_include_path(get_include_path().PATH_SEPARATOR.
                realpath("../src"));

use oasis\names\specification\ubl\schema\xsd\CommonBasicComponents_2;
use dk\nordsign\schema\ContactCompany;
use com\mikebevz\xsd2php;

//require_once 'PHPUnit/Framework.php';
require_once "Xsd2Php.class.php";

class Xsd2PhpTest
{
    /**
     * XSD to PHP convertor class
     * @var Xsd2Php
     */
    private $tclass; 
    
    private $xsd;
    
    protected function setUp ()
    {
        $this->xsd = drupal_get_path('module', 'afas') . '/testpages/XMLSchema/KnContact.xsd';
        $phpfile = file_directory_path() . '/afas/KnContact';
        
        $this->tclass = new xsd2php\Xsd2Php($this->xsd);
    }
    protected function tearDown ()
    {
        $this->tclass = null;        
    }
    
    public function testContact() {
      $xsd_path = drupal_get_path('module', 'afas') . '/testpages/XMLSchema/KnContact.xsd';
      $phpfile = file_directory_path() . '/afas/KnContact';
      $oClass = new xsd2php\Xsd2Php($xsd_path, true);
      $xml = $oClass->getXML();
      $oClass->saveClasses($phpfile, true);
    }
    
    /*
    private function rmdir_recursive($dir) {
        if (is_dir($dir)) { 
         $objects = scandir($dir); 
         foreach ($objects as $object) { 
           if ($object != "." && $object != "..") { 
             if (filetype($dir."/".$object) == "dir") rmdir_recursive($dir."/".$object); else unlink($dir."/".$object); 
           } 
         } 
         reset($objects); 
         rmdir($dir); 
       } 
    }*/
    
    public function testXSDMustBeConvertedToXML() {
        $xml = $this->tclass->getXML();
        $actual = $xml->saveXml();
        //file_put_contents(dirname(__FILE__).'/data/expected/ubl2.0/XSDConvertertoXML.xml', $xml->saveXml());
        $expected = file_get_contents(dirname(__FILE__).'/data/expected/ubl2.0/XSDConvertertoXML.xml');
        $this->assertEquals($expected, $actual);
    }
    
    public function testPHPFilesMustBeSaved() {
        $orderModelExpected = array(
                    'data/expected/ubl2.0/oasis/names/specification/ubl/schema/xsd/Order_2/Order.php',
                    'data/expected/ubl2.0/oasis/names/specification/ubl/schema/xsd/Order_2/OrderType.php',
                    'data/expected/ubl2.0/oasis/names/specification/ubl/schema/xsd/CommonBasicComponents_2/AcceptedIndicator.php',
                    'data/expected/ubl2.0/oasis/names/specification/ubl/schema/xsd/CommonBasicComponents_2/AcceptedIndicatorType.php',
                    'data/expected/ubl2.0/oasis/names/specification/ubl/schema/xsd/CommonBasicComponents_2/AccountID.php',
                    'data/expected/ubl2.0/oasis/names/specification/ubl/schema/xsd/CommonBasicComponents_2/AccountIDType.php',
                    'data/expected/ubl2.0/oasis/names/specification/ubl/schema/xsd/CommonBasicComponents_2/AccountingCost.php'
                    );
        $orderModelActual = array(
                    'data/generated/ubl2.0/oasis/names/specification/ubl/schema/xsd/Order_2/Order.php',
                    'data/generated/ubl2.0/oasis/names/specification/ubl/schema/xsd/Order_2/OrderType.php',
                    'data/generated/ubl2.0/oasis/names/specification/ubl/schema/xsd/CommonBasicComponents_2/AcceptedIndicator.php',
                    'data/generated/ubl2.0/oasis/names/specification/ubl/schema/xsd/CommonBasicComponents_2/AcceptedIndicatorType.php',
                    'data/generated/ubl2.0/oasis/names/specification/ubl/schema/xsd/CommonBasicComponents_2/AccountID.php',
                    'data/generated/ubl2.0/oasis/names/specification/ubl/schema/xsd/CommonBasicComponents_2/AccountIDType.php',
                    'data/generated/ubl2.0/oasis/names/specification/ubl/schema/xsd/CommonBasicComponents_2/AccountingCost.php');
        
        if (file_exists(dirname(__FILE__).'/data/generated/ubl2.0')) {
            rmdir_recursive(realpath('data/generated/ubl2.0'));
        }
        $this->tclass->saveClasses(dirname(__FILE__).'/data/generated/ubl2.0', true);
        
        $i = 0;
        foreach ($orderModelExpected as $model) {
            $this->assertEquals(file_get_contents($model), file_get_contents($orderModelActual[$i]));
            $i++;
        }
        
        if (file_exists('data/generated/ubl2.0')) {
           rmdir_recursive(realpath('data/generated/ubl2.0'));
        }
        
    }
    
    public function testSimpleSchema1() {
        $this->tclass = new xsd2php\Xsd2Php("../resources/simple1/simple.xsd");
        $xml = $this->tclass->getXML();
        //file_put_contents(dirname(__FILE__).'/data/expected/simple1/generated.xml', $xml->saveXml());
        $expectedXml = file_get_contents(dirname(__FILE__).'/data/expected/simple1/generated.xml');
        $this->assertEquals($expectedXml, $xml->saveXml());
        
        
        if (file_exists(dirname(__FILE__).'/data/generated/simple1')) {
            rmdir_recursive(realpath('data/generated/simple1'));
        }
        
        $shipModelExpected = array(
                    'data/expected/simple1/address.php',
                    'data/expected/simple1/city.php',
                    'data/expected/simple1/country.php',
                    'data/expected/simple1/item.php',
                    'data/expected/simple1/name.php',
                    'data/expected/simple1/note.php',
                    'data/expected/simple1/orderperson.php',
                    'data/expected/simple1/price.php',
                    'data/expected/simple1/quantity.php',
                    'data/expected/simple1/shiporder.php',
                    'data/expected/simple1/shipto.php',
                    'data/expected/simple1/title.php'
                    );
        $shipModelActual = array(
                    'data/generated/simple1/address.php',
                    'data/generated/simple1/city.php',
                    'data/generated/simple1/country.php',
                    'data/generated/simple1/item.php',
                    'data/generated/simple1/name.php',
                    'data/generated/simple1/note.php',
                    'data/generated/simple1/orderperson.php',
                    'data/generated/simple1/price.php',
                    'data/generated/simple1/quantity.php',
                    'data/generated/simple1/shiporder.php',
                    'data/generated/simple1/shipto.php',
                    'data/generated/simple1/title.php'
                    );
        
        
        $this->tclass->saveClasses(dirname(__FILE__).'/data/generated/simple1', true);
        
        $i = 0;
        foreach ($shipModelExpected as $model) {
            $this->assertEquals(file_get_contents($model), file_get_contents($shipModelActual[$i]));
            $i++;
        }
        
        if (file_exists(dirname(__FILE__).'/data/generated/simple1')) {
            rmdir_recursive(realpath('data/generated/simple1'));
        }
    }
    
    public function testMultiLevelImportAndIncludes() {
         
         $this->tclass = new xsd2php\Xsd2Php("../resources/MultiLevelImport/ContactPerson.xsd");
         $xml = $this->tclass->getXML();
         //file_put_contents(dirname(__FILE__).'/data/expected/MultiLevelImport/generated.xml', $xml->saveXml());
         $expectedXml = file_get_contents(dirname(__FILE__).'/data/expected/MultiLevelImport/generated.xml');
         $this->assertEquals($expectedXml, $xml->saveXml());
         
         if (file_exists(dirname(__FILE__).'/data/generated/MultiLevelImport')) {
            rmdir_recursive(realpath(dirname(__FILE__).'/data/generated/MultiLevelImport'));
         } 
         
         $this->tclass->saveClasses(dirname(__FILE__).'/data/generated/MultiLevelImport', true);
         
         if (file_exists(dirname(__FILE__).'/data/generated/MultiLevelImport')) {
            rmdir_recursive(realpath(dirname(__FILE__).'/data/generated/MultiLevelImport'));
         } 
    }
    
 public function testContactPerson() {
        
        $expPath = dirname(__FILE__).'/data/expected/ContactPerson1/';
        $genPath = dirname(__FILE__).'/data/generated/ContactPerson1/';
        
         $this->tclass = new xsd2php\Xsd2Php("../resources/ContactPerson1/ContactPerson.xsd");
         $xml = $this->tclass->getXML();
         //file_put_contents($expPath.'generated.xml', $xml->saveXml());
         $expectedXml = file_get_contents($expPath.'generated.xml');
         $this->assertEquals($expectedXml, $xml->saveXml());
         
         if (file_exists($genPath)) {
            rmdir_recursive(realpath($genPath));
         } 
         
         $this->tclass->saveClasses($genPath, true);
         
         if (file_exists($genPath)) {
           rmdir_recursive(realpath($genPath));
         } 
        
        
    }
 
    
    public function testContactCompany() {
        $expPath = dirname(__FILE__).'/data/expected/ContactCompany/';
        $genPath = dirname(__FILE__).'/data/generated/ContactCompany/';
        
         $this->tclass = new xsd2php\Xsd2Php("../resources/ContactCompany/ContactCompany.xsd");
         $xml = $this->tclass->getXML();
         //file_put_contents($expPath.'generated.xml', $xml->saveXml());
         $expectedXml = file_get_contents($expPath.'generated.xml');
         $this->assertEquals($expectedXml, $xml->saveXml());
         
         if (file_exists($genPath)) {
            rmdir_recursive(realpath($genPath));
         } 
         
         $this->tclass->saveClasses($genPath, true);
         
         //$cp = new ContactCompany\ContactCompany();
         //$id = new CommonBasicComponents_2\ID();
            
         
         if (file_exists($genPath)) {
           rmdir_recursive(realpath($genPath));
         } 
    }
    
   
}