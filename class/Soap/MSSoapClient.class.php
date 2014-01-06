<?php
class MSSoapClient extends SoapClient {
    protected $m_sAction = '';
    
    // Override so that we can append the xmlns attribute to the "action" node.
    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        $request = preg_replace('/<([a-z0-9\:\ ]*' . $this->m_sAction . ')>/i', '<${1} xmlns="'.$this->uri.'">', $request);
        return parent::__doRequest($request, $location, $action, $version);
    }

    /**
     * Set the action (WS Method) that we are taking.
     * @param string $p_sAction
     * @access public
     * @return void
     */
    public function setAction($p_sAction) {
      $this->m_sAction = $p_sAction;
    }
}