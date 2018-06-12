<?php
/**
 * 定福庄迁移新memcache--passport数据预热
 * Created by PhpStorm.
 * User: xiaolongrong
 * Date: 15/12/4
 * Time: 下午5:26
 */

namespace Inauth\Package\Session\Helper;

class Dfz_new_memcache {

    private static $singleton = NULL;

    /**
     * Singleton.
     */
    public static function instance() {
        $class = get_called_class();
        is_null(self::$singleton) && self::$singleton = new $class();
        return self::$singleton;
    }

    public $config = NULL;
    private $engine = NULL;

    /**
     * Connection pools.
     *
     * @var array
     */
    private $pools = array();
    protected static $useMemcached = TRUE;

    //protected static $useMemcached = FALSE;

    /**
     * Constructor.
     */
    protected function __construct() {
        is_null($this->config) && $this->config = \Frame\ConfigFilter::instance()->getConfig('memcachedfznew');
        $this->pools = new \Memcached();
        $this->pools->setOption(\Memcached::OPT_NO_BLOCK, TRUE);
        $this->pools->setOption(\Memcached::OPT_DISTRIBUTION, \Memcached::DISTRIBUTION_CONSISTENT);
        $this->pools->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, TRUE);
        $config = array();

        $unixnum = count($this->config['unixsock']);
        $num = rand(0, $unixnum - 1);
        $weight = 10;
        $config = array();
        $config[] = array($this->config['unixsock'][$num]['host'], $this->config['unixsock'][$num]['port'], $weight);

        $result = $this->pools->addServers($config);
        return TRUE;

    }

    public function __call($method, $arguments) {
        return $this->$method($arguments);
    }

    /**
     * 支持从外界设置memcache engine
     * @param string $_engine
     */
    protected function setEngine($_engine) {
        $this->engine = $_engine[0];
    }

    /**
     * Delete an item.
     *
     * @param string $key The key to be deleted.
     * @param int $time The amount of time the server will wait to delete
     * the item.
     *
     * @return Returns TRUE on success or FALSE on failure.
     */
    protected function delete($args) {
        $key = $args[0];
        if (!isset($args[1])) {
            $args[1] = 0;
        }
        $time = $args[1];
        $result = TRUE;
        $result = $result && $this->pools->delete($key, $time);
        return $result;
    }

    /**
     * Store an item.
     *
     * @param string $key The key under which to store the value.
     * @param mixed $value The value to store.
     * @param int $expiration The expiration time, defaults to 0.
     *
     * @return Returns TRUE on success or FALSE on failure.
     */
    protected function set($args) {
        $key = $args[0];
        $value = $args[1];
        if (!isset($args[2])) {
            $args[2] = 0;
        }
        $expiration = $args[2];
        $result = TRUE;
        $result = $result && $this->pools->set($key, $value, $expiration);
        return $result;
    }

    /**
     * Retrieve an item.
     *
     * @param string $key The key of the item to retrieve.
     *
     * @return Returns the value stored in the cache or FALSE otherwise.
     */
    protected function get($args) {
        $key = $args[0];
        $result = FALSE;
        $result = $this->pools->get($key);

        return $result;
    }

    /**
     * Increment numeric item's value.
     */
    protected function increment($args) {
        $key = $args[0];
        if (!isset($args[1])) {
            $args[1] = 1;
        }
        $offset = $args[1];
        $result = FALSE;
        $result = $this->pools->increment($key, $offset);
        return $result;
    }

    /**
     * Decrement numeric item's value.
     */
    protected function decrement($args) {
        $key = $args[0];
        if (!isset($args[1])) {
            $args[1] = 1;
        }
        $offset = $args[1];
        $result = FALSE;
        $result = $this->pools->decrement($key, $offset);
        return $result;
    }

    /**
     * Retrieve multiple items.
     */
    protected function getMulti($args) {
        $keys = $args[0];
        if (empty($keys) || !is_array($keys)) {
            return FALSE;
        }
        switch ($this->engine) {
            case 'Memcached':
                if (count($keys) > 10) {
                    return $this->pools->getMulti($keys);
                }
            case 'Memcache':
                $values = array();
                foreach ($keys as $key) {
                    $val = $this->pools->get($key);
                    if ($val !== FALSE) {
                        $values[$key] = $val;
                    }
                }
                return $values;
        }
    }

    /**
     * Store multiple items.
     */
    protected function setMulti($args) {
        $items = $args[0];
        if (!isset($args[1])) {
            $args[1] = 0;
        }
        $expiration = $args[1];
        $result = $this->pools->setMulti($items, $expiration);
        return $result;
    }

    /**
     * 返回最后一次操作的结果代码
     */
    protected function getResultCode() {
        return $this->pools->getResultCode();
    }

    /**
     * 判断是否是定福庄机房的机器
     */
    protected function IsDfz() {
        $sIp = $_SERVER['SERVER_ADDR'];
        if (!empty($sIp) && substr($sIp,0,5) == '10.5.'){
            return TRUE;
        }
        return FALSE;
    }
}
