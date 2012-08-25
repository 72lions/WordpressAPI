<?php
/**
 * Provides a basic cache interface
 *
 * @module 72lionsPHP
 * @class BaseCacheInterface
 * @author Thodoris Tsiridis
 * @version 1.0
 */
  interface BaseCacheInterface {

    /**
     * The constructor fo the interface
     * @param  {String} $prefix The prefix that will be used in front of all keys.
     * @param  {String} $group  The group that will be used in front of all keyes.
     */
    public function __construct($host, $port, $prefix = '', $group = '');

    /**
     * Connects to caching interface
     * @param {String} $host The host that the interface will connect
     * @param {Number} $port The port that the interface will connect to
     * @author Thodoris Tsiridis
     */
    public function connect();

    /**
     * Returns an object from memcache
     * @param {String} $key The name of the key that we want to get
     * @return {Object} The object that we want
     * @author Thodoris Tsiridis
     */
    public function get($key);

    /**
     * Returns an object from memcache
     * @param {String} $key The name of the key that we want to save
     * @param {Object} $value The object that we want to save
     * @param {Number} $time The time that the object will stay in memory
     * @author Thodoris Tsiridis
     */
    public function set($key, $value, $time = 864000);

    /**
     * Clears cache
     * @author Thodoris Tsiridis
     */
    public function flush();

    /**
     * Closes memcache connection
     * @author Thodoris Tsiridis
     */
    public function close();
  }
?>
