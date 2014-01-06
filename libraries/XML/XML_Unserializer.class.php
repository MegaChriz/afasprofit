<?php
/**
 * @file
 * This class contains a collection of function for
 * parsing XML files
 */
class XML_Unserializer {
  var $stack;
  var $arr_output;
  var $null_token = "null";

  function unserialize($str_input_xml) {
    $p = xml_parser_create();
    xml_set_element_handler($p, array(&$this, 'start_handler'), array(&$this, 'end_handler'));
    xml_set_character_data_handler($p, array(&$this, 'data_handler'));
    $this->stack = array(
      array(
        'name' => 'document',
        'attributes' => array(),
        'children' => array(),
        'data' => '',
      ),
    );
    if (!xml_parse($p, $str_input_xml)) {
      trigger_error(xml_error_string(xml_get_error_code($p)) . "\n" . $str_input_xml, E_USER_NOTICE);
      xml_parser_free($p);
      return;
    }
    xml_parser_free($p);

    $tmp = $this->build_array($this->stack[0]);
    if (count($tmp) == 1) {
      $this->arr_output = array_pop($tmp);
    }
    else {
      $this->arr_output = array();
    }
    unset($this->stack);
    return $this->arr_output;
  }

  function get_unserialized_data() {
    return $this->arr_output;
  }

  function build_array($stack) {
    $result = array();
    if (count($stack['attributes']) > 0) {
      $result = array_merge($result, $stack['attributes']);
    }

    if (count($stack['children']) > 0) {
      if (count($stack['children']) == 1) {
        $key = array_keys($stack['children']);
        //krumo($stack['children']);
        if ($stack['children'][$key[0]]['name'] === $this->null_token) {
          return NULL;
        }
      }
      $keycount = array();
      foreach ($stack['children'] as $child) {
        $keycount[] = $child['name'];
      }
      if (count(array_unique($keycount)) != count($keycount)) {
        // enumerated array
        $children = array();
        foreach ($stack['children'] as $child) {
          $children[] = $this->build_array($child);
        }
      }
      else {
        // indexed array
        $children = array();
        foreach ($stack['children'] as $child) {
          $children[$child['name']] = $this->build_array($child);
        }
      }
      $result = array_merge($result, $children);
    }

    if (count($result) == 0) {
      return trim($stack['data']);
    }
    else {
      return $result;
    }
  }

  function start_handler($parser, $name, $attribs = array()) {
    $token = array();
    $token['name'] = strtolower($name);
    $token['attributes'] = $attribs;
    $token['data'] = '';
    $token['children'] = array();
    $this->stack[] = $token;
  }

  function end_handler($parser, $name, $attribs = array()) {
    $token = array_pop($this->stack);
    $this->stack[count($this->stack) - 1]['children'][] = $token;
  }

  function data_handler($parser, $data) {
    $this->stack[count($this->stack) - 1]['data'] .= $data;
  }
}
