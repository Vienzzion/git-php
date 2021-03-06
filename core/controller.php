<?php

class Controller {

    var $name;
    var $_config;
    var $_request;
    var $_models = array();
    var $_view;
    var $_breadcrumbs = array('home' => '/');

    public function __set($name, $value) {
        if (file_exists(MODELS . Inflector::underscore($name)  . '.php')) {
            require_once(MODELS . Inflector::underscore($name)  . '.php');
            $this->_models[$name] = $value;
            return;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    function __get($name) {
        if (array_key_exists($name, $this->_models)) {
            return $this->_models[$name];
        }
        
        if (file_exists(MODELS . Inflector::underscore($name)  . '.php')) {
            require_once(MODELS . Inflector::underscore($name)  . '.php');
            $this->_models[$name] = new $name($this->_config);
            return $this->_models[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    function __construct($config, $request) {
        if ($this->name === null) {
            $r = null;
            if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
                printf("Controller::__construct() : Can not get or parse my own class name, exiting.");
                exit();
            }
            $this->name = $r[1];
        }

        $this->_config  = $config;
        $this->_request = $request;
        $this->_view    = new Gears($request, $config, array(
            'ext'           => 'php',
            'element_path'  => VIEWS . 'elements' . DS,
            'path'          => VIEWS . Inflector::underscore($this->name) . DS,
            'layout_path'   => VIEWS . 'layouts' . DS,
            'layout'        => 'default.php'
        ));
    }

    function set($name, $data = null) {
        if ($data) {
            $name = array($name => $data);
        }
        $this->_view->bind($name);
    }

    function layout($layout) {
        $this->_view->setLayout(VIEWS . 'layouts' . DS . $layout);
    }

    function render($template = null) {
        if (!$template) $template = $this->_request->params['action'];
        $this->set('config', $this->_config);
        $this->set('request', $this->_request);
        $this->set('breadcrumbs', $this->_breadcrumbs);
        $this->_view->display($template);
    }

}