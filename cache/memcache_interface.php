<?php
/**
 * The memcached class is responsible for connecting, getting/setting values and disconnecting from memcached
 *
 * @module 72lionsPHP
 * @class MemcacheInterface
 * @extends BaseCacheInterface
 * @author Thodoris Tsiridis
 * @version 1.0
 */
class MemcacheInterface implements BaseCacheInterface {
    /**
     * Is set to true when the interface is connected to the service
     * @var boolean
     */
    public $isConnected = false;

    /**
     * Stores the host of the interface
     * @var string
     */
    protected $host = '';

    /**
     * The port number of the host
     * @var integer
     */
    protected $port = 0;
    /**
     * The memcache instance
     * @var Memcache
     */
    protected $memcache;

    /**
     * The prefix that will be used in front of all keys
     * @var string
     */
    protected $prefix = '';

    /**
     * The group that will be used in front of all keys
     * @var string
     */
    protected $group = '';

    /**
     * The constructor fo the interface
     * @param  {String} $prefix The prefix that will be used in front of all keys.
     * @param  {String} $group  The group that will be used in front of all keyes.
     */
    public function __construct($host, $port, $prefix = '', $group = '') {
        $this->prefix = $prefix;
        $this->group = $group;
        $this->host = $host;
        $this->port = $port;

        $this->memcache = new Memcache;

    }

    /**
     * Connects to memcache
     * @param {String} $host The host that the interface will connect
     * @param {Number} $port The port that the interface will connect to
     * @author Thodoris Tsiridis
     */
    public function connect() {

         // Connect to memecache
        $this->memcache->connect($this->host, $this->port) or die ("Memcache could not connect");
        $this->isConnected = true;
    }

    /**
     * Returns an object from memcache
     * @param {String} $key The name of the key that we want to get
     * @return {Object} The object that we want
     * @author Thodoris Tsiridis
     */
    public function get($key) {

        // Check if it is connected
        if(!$this->isConnected){
            // Connect
            $this->connect();
        }

        return $this->memcache->get($this->prefix . $this->group . ':'.md5($key));

    }

    /**
     * Returns an object from memcache
     * @param {String} $key The name of the key that we want to save
     * @param {Object} $value The object that we want to save
     * @param {Number} $time The time that the object will stay in memory
     * @author Thodoris Tsiridis
     */
    public function set($key, $value, $time = 864000) {

        // Check if it is connected
        if(!$this->isConnected){
            // Connect
            $this->connect();
        }

        $this->memcache->set($this->prefix . $this->group . ':' . md5($key), $value, false, $time);
    }

    /**
     * Clears memcache
     * @author Thodoris Tsiridis
     */
    public function flush() {
        // Check if it is connected
        if(!$this->isConnected){
            // Connect
            $this->connect();
        }

        $this->memcache->flush();

    }

    /**
     * Closes memcache connection
     * @author Thodoris Tsiridis
     */
    public function close() {
        // Check if it is connected
        if($this->isConnected){
            $this->memcache->close();
            $this->isConnected = false;
        }



    }

}
?>
