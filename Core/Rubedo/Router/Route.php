<?php

/**
 * Rubedo -- ECM solution Copyright (c) 2013, WebTales
 * (http://www.webtales.fr/). All rights reserved. licensing@webtales.fr
 * Open Source License
 * ------------------------------------------------------------------------------------------
 * Rubedo is licensed under the terms of the Open Source GPL 3.0 license.
 *
 * @category Rubedo
 * @package Rubedo
 * @copyright Copyright (c) 2012-2013 WebTales (http://www.webtales.fr)
 * @license http://www.gnu.org/licenses/gpl.html Open Source GPL 3.0 license
 */
namespace Rubedo\Router;

Use Rubedo\Services\Manager;

/**
 * Zend_Controller_Router_Route implementation for frontend pages
 *
 *
 * @author jbourdin
 * @category Rubedo
 * @package Rubedo
 */
class Route extends \Zend_Controller_Router_Route_Abstract implements \Zend_Controller_Router_Route_Interface
{

    /**
     * Request Values
     *
     * @var array
     */
    protected $_values = array();

    /**
     * Instantiates route based on passed Zend_Config structure
     */
    public static function getInstance (\Zend_Config $config)
    {
        unset($config);//not used, just an interface requirement
        $frontController = \Zend_Controller_Front::getInstance();
        
        $defs = array();
        $dispatcher = $frontController->getDispatcher();
        $request = $frontController->getRequest();
        
        return new self($defs, $dispatcher, $request);
    }

    /**
     * Assembles user submitted parameters forming a URL path defined by this
     * route
     *
     * @param array $data
     *            An array of variable and value pairs used as
     *            parameters
     * @param bool|string $reset
     *            should we reset the current params
     * @return string Route path with user submitted parameters
     */
    public function assemble ($data = array(), $reset = false, $encode = false)
    {
        if (isset($this->_prefix)) {
            
            foreach ($data as $key => $value) {
                if ($key == $this->_prefix) {
                    continue;
                }
                unset($data[$key]);
                $data[$this->_prefix . '_' . $key] = $value;
            }
        }
        
        if ($reset === true) {
            $params = array(
                'pageId' => isset($this->_values["pageId"]) ? $this->_values["pageId"] : null
            );
        } else {
            $params = \Zend_Controller_Front::getInstance()->getRequest()->getParams();
        }
        if ($reset === 'add') {
            
            foreach ($data as $key => $value) {
                if (! isset($params[$key])) {
                    $params[$key] = array();
                }
                if (! is_array($value)) {
                    $value = array(
                        $value
                    );
                }
                $data[$key] = array_unique(array_merge($params[$key], $value));
            }
            $data = array_merge($params, $data);
        } elseif ($reset === 'sub') {
            foreach ($data as $key => $value) {
                if (! isset($params[$key])) {
                    $params[$key] = array();
                } elseif (! is_array($params[$key])) {
                    $params[$key] = array(
                        $params[$key]
                    );
                }
                if (! is_array($value)) {
                    $value = array(
                        $value
                    );
                }
                $data[$key] = array_diff($params[$key], $value);
            }
            $data = array_merge($params, $data);
        } else {
            $data = array_merge($params, $data);
        }
        
        foreach ($data as $key => $value) {
            if ($value !== null) {
                $params[$key] = $value;
            } elseif (isset($params[$key])) {
                unset($params[$key]);
            }
        }
        $url = Manager::getService('Url')->getUrl($params, $encode);
        return $url;
    }

    /**
     * Matches a user submitted path.
     * Assigns and returns an array of variables
     * on a successful match.
     * If a request object is registered, it uses its setModuleName(),
     * setControllerName(), and setActionName() accessors to set those values.
     * Always returns the values as an array.
     *
     * @param string $path
     *            Path used to match against this routing map
     * @return array An array of assigned values or a false on a mismatch
     */
    public function match ($path)
    {
        try {
            $pageId = Manager::getService('Url')->getPageId($path->getRequestUri(), $path->getHttpHost());
        } catch (\Rubedo\Exceptions\Server $exception) {
            $pageId = null;
        }
        if ($pageId === null) {
            return false;
        } else {
            $this->_values = array(
                'controller' => 'index',
                'action' => 'index',
                'pageId' => $pageId
            );
            return $this->_values;
        }
    }

    public function setPrefix ($prefix)
    {
        $this->_prefix = $prefix;
    }

    public function getPrefix ()
    {
        return $this->_prefix;
    }

    public function clearPrefix ()
    {
        $this->_prefix = null;
    }
}

?>