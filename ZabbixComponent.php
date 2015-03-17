<?php
/**
 * Created by PhpStorm.
 * User: kist
 * Date: 17.03.15
 * Time: 15:05
 */

namespace fiamma06\zabbix;

use yii\base\Component;
use yii\base\Exception;
use yii\base\UnknownPropertyException;
use yii\base\InvalidCallException;

/**
 * Class Zabbix
 * @package frontend\components
 */
class ZabbixComponent extends Component
{
    /**
     * @var ZabbixApi
     */
    private $_zabbixObject;

    /**
     * @var string
     */
    private $_user;

    /**
     * @var string
     */
    private $_password;

    /**
     * Auth user
     *
     * @param string $login
     */
    public function setUser($login) {
        $this->_user = $login;
    }

    /**
     * Auth password
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->_password = $password;
    }

    public function getZabbixObject()
    {
        if ($this->_zabbixObject === null) {
            $this->_zabbixObject = new ZabbixApi;
        }
        return $this->_zabbixObject;
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function init() {
        try {
            $this->getZabbixObject()->userLogin(['user' => $this->_user, 'password' => $this->_password]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param string $methodName
     * @param array $methodParams
     * @return mixed
     */
    public function __call($methodName, $methodParams) {
        if ( method_exists( $this->getZabbixObject(), $methodName ) ) {
            return call_user_func_array(array($this->getZabbixObject(), $methodName), $methodParams);
        }
    }

    /**
     * @param string $name
     * @return mixed
     * @throws UnknownPropertyException
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this->getZabbixObject(), $getter)) {
            return $this->getZabbixObject()->$getter();
        } elseif (method_exists($this, $getter)) {
            return parent::__get($name);
        } elseif (method_exists($this, 'set' . $name)) {
            throw new InvalidCallException('Getting write-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws UnknownPropertyException
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this->getZabbixObject(), $setter)) {
            $this->getZabbixObject()->$setter($value);
        } elseif (method_exists($this, $setter)) {
            parent::__set($name, $value);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Setting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }
}