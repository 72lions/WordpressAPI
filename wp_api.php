<?php
/**
 * Responsible for retrieving all the necessary data from the wp database
 *
 * @module 72lionsPHP
 * @class WpApi
 * @author Thodoris Tsiridis
 * @version 1.1
 */
class WpApi {

    /**
     * The username that will be used for connecting to the DB
     * @var string
     */
     protected static $DB_USERNAME = 'root';

     /**
      * The password that will be used for connecting to the DB
      * @var string
      */
     protected static $DB_PASSWORD = '';

     /**
      * The database host
      * @var string
      */
     protected static $DB_HOST = 'localhost';

     /**
      * The database name
      * @var string
      */
     protected static $DB_NAME = '72lionswp';

     /**
      * The cache host that will be used for connecting to the cache
      * @var string
      */
     protected static $CACHE_HOST = 'localhost';

     /**
      * The cache port that will be used for connecting
      * @var integer
      */
     protected static $CACHE_PORT = 11211;

     /**
      * The cache prefix that will be used for prefixing the cache keys
      * @var string
      */
     protected static $CACHE_PREFIX = '72lions';

     /**
      * The cache group taht will be used for prefixing the cache keys
      * @var string
      */
     protected static $CACHE_GROUP = 'default';

     /**
      * An array containing all the posts
      * @var array
      */
     protected $posts = array();

     /**
      * An array containing all the pages
      * @var array
      */
     protected $pages = array();

     /**
      * The cache interface
      * @var {BaseCacheInterface}
      */
     protected $cache;

     /**
      * The constructor of the api class
      */
     public function __construct() {
        $this->cache = new MemCacheInterface(self::$CACHE_HOST, self::$CACHE_PORT, self::$CACHE_PREFIX, self::$CACHE_GROUP);
     }

    /**
     * Returns an array with all the categories
     *
     * @param {Number} $start The beginning of the result set
     * @param {Number} $total The total items to laod
     * @param {String} $sort The sorting
     * @return {Array}
     * @author Thodoris Tsiridis
     */
    public function getCategories($start = 0, $total = 10, $sort = 'name ASC') {

        $query = "SELECT WT.* FROM wp_terms WT, wp_term_taxonomy WTT
                WHERE WT.term_id =  WTT.term_id
                AND taxonomy='category'
                ORDER BY WT.".$sort."
                LIMIT ".$start.",".$total;

        if($this->cache->get($query) == null){

            $db = new MysqlDB();
            $db->connect(self::$DB_USERNAME, self::$DB_PASSWORD, self::$DB_HOST, self::$DB_NAME);
            $result = mysql_query($query) or die('Class '.__CLASS__.' -> '.__FUNCTION__.' : ' . mysql_error());
            while($row = mysql_fetch_array($result, MYSQL_ASSOC)){

                $category = new WpCategory($row);
                WpCategories::addCategory($category);
                $this->cache->set('category'.$category->id, $category);

            }

            $db->disconnect();
            unset($db);

            $this->cache->set($query, WpCategories::$categories);

            unset($result);
        }

        return $this->cache->get($query);
    }

    /**
     * Returns an array with all the posts
     *
     * @param {Number} $categoryId The id of the parent category
     * @param {Number} $start The beginning of the result set
     * @param {Number} $total The total items to laod
     * @param {String} $sort The sorting
     * @return {Array}
     * @author Thodoris Tsiridis
     */
    public function getPosts($categoryId = null, $tagId = null, $start = 0, $total = 10, $sort = 'post_date DESC') {

        $db = new MysqlDB();
        $db->connect(self::$DB_USERNAME, self::$DB_PASSWORD, self::$DB_HOST, self::$DB_NAME);

        if ($tagId !== null) {
            $query = "SELECT * FROM wp_posts WPP,
            wp_term_taxonomy WPTT,
            wp_term_relationships WPTR
            WHERE WPP.post_status='publish'
            AND WPP.post_type='post'
            AND WPTT.term_id=".mysql_real_escape_string($tagId)."
            AND WPTT.taxonomy='post_tag'
            AND WPTR.term_taxonomy_id = WPTT.term_taxonomy_id
            AND WPTR.object_id = WPP.ID
            ORDER BY ".$sort."
            LIMIT ".$start.",".$total;

        } else if ($categoryId !== null){

            $query = "SELECT * FROM wp_posts WPP,
            wp_term_taxonomy WPTT,
            wp_term_relationships WPTR
            WHERE WPP.post_status='publish'
            AND WPP.post_type='post'
            AND WPTT.term_id=".mysql_real_escape_string($categoryId)."
            AND WPTT.taxonomy='category'
            AND WPTR.term_taxonomy_id = WPTT.term_taxonomy_id
            AND WPTR.object_id = WPP.ID
            ORDER BY ".$sort."
            LIMIT ".$start.",".$total;

        } else {

            $query = "SELECT * FROM wp_posts
            WHERE post_status='publish'
            AND post_type='post'
            ORDER BY ".$sort."
            LIMIT ".$start.",".$total;

        }

        if($this->cache->get($query) == null){

            $result = mysql_query($query) or die('Class '.__CLASS__.' -> '.__FUNCTION__.' : ' . mysql_error());
            while($row = mysql_fetch_array($result, MYSQL_ASSOC)){

                // Create a post
                $post = $this->createPostObject($row);
                $this->posts[] = $post;

                $this->cache->set('post'.$post->id, $post);
                $this->cache->set($post->slug, $post);

            }

            unset($result);
            $this->cache->set($query, $this->posts);
        }

        $db->disconnect();
        unset($db);

        return $this->cache->get($query);


    }

    /**
     * Returns an array with all the pages
     *
     * @param {Number} $categoryId The id of the parent category
     * @param {Number} $start The beginning of the result set
     * @param {Number} $total The total items to laod
     *
     * @return {Array}
     * @author Thodoris Tsiridis
     */
    public function getPages($categoryId = null, $start = 0, $total = 10, $sort = 'post_date DESC') {

       if($categoryId !== null){

            $query = "SELECT * FROM wp_posts
            WHERE post_status='publish'
            AND post_type='page'
            ORDER BY ".$sort."
            LIMIT ".$start.",".$total;

        } else {

            $query = "SELECT * FROM wp_posts
            WHERE post_status='page'
            AND post_type='post'
            ORDER BY ".$sort."
            LIMIT ".$start.",".$total;

        }

        if($this->cache->get($query) == null){

            $db = new MysqlDB();
            $db->connect(self::$DB_USERNAME, self::$DB_PASSWORD, self::$DB_HOST, self::$DB_NAME);
            $result = mysql_query($query) or die('Class '.__CLASS__.' -> '.__FUNCTION__.' : ' . mysql_error());
            while($row = mysql_fetch_array($result, MYSQL_ASSOC)){

                $post = new WpPost($row);
                $this->pages[] = $post;
                $this->cache->set('page'.$post->id, $post);
                $this->cache->set($post->slug, $post);

            }

            $db->disconnect();
            unset($db);

            $this->cache->set($query, $this->pages);
        }

        return $this->cache->get($query);
    }

    /**
     * Gets the details of a post
     *
     * @param {Number} $postId The id of the post that we want to get the details
     * @return {Object}
     * @author Thodoris Tsiridis
     */
    public function getPostDetails($postId) {

        if($this->cache->get($postId) == null) {

            $db = new MysqlDB();
            $db->connect(self::$DB_USERNAME, self::$DB_PASSWORD, self::$DB_HOST, self::$DB_NAME);

            $query = "SELECT * FROM wp_posts
            WHERE post_status='publish'
            AND (post_type='post' || post_type='page')
            AND post_name='".mysql_real_escape_string($postId)."'";

            $result = mysql_query($query) or die('Class '.__CLASS__.' -> '.__FUNCTION__.' : ' . mysql_error());
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            $total = mysql_num_rows($result);

            if($total > 0) {

                $post = $this->createPostObject($row);

                if($row['post_type'] === 'post'){
                    $this->cache->set('post'.$post->id, $post);
                } else {
                    $this->cache->set('page'.$post->id, $post);
                }

                $this->cache->set($post->slug, $post);
            }

            $db->disconnect();
            unset($db);

        }

        return $this->cache->get($postId);

    }

    /**
     * Gets the details of a post
     *
     * @param {Number} $postId The id of the post that we want to get the details
     * @return {Object}
     * @author Thodoris Tsiridis
     */
    public function getComments($postId) {
        //TODO: First check memcache
    }

    protected function createPostObject($row) {

        $post = new WpPost($row);

        // Get the attachments
        $queryAt = "SELECT * FROM wp_postmeta
        WHERE post_id=".$row['ID'];

        $resultAt = mysql_query($queryAt) or die('Class '.__CLASS__.' -> '.__FUNCTION__.' : ' . mysql_error());
        while($rowAt = mysql_fetch_array($resultAt, MYSQL_ASSOC)){

            //Get the thumbnail
            if($rowAt['meta_key'] === '_thumbnail_id') {

                $queryThumbnail = "SELECT * FROM wp_postmeta
                WHERE post_id=" . $rowAt['meta_value'];

                $resultTh = mysql_query($queryThumbnail) or die('Class '.__CLASS__.' -> '.__FUNCTION__.' : ' . mysql_error());
                while($rowTh = mysql_fetch_array($resultTh, MYSQL_ASSOC)){

                    if($rowTh['meta_key'] === '_wp_attached_file') {
                        $post->thumbnail['File'] = $rowTh['meta_value'];
                    }

                    if($rowTh['meta_key'] === '_wp_attachment_metadata') {
                        $post->thumbnail['Data'] = unserialize($rowTh['meta_value']);
                    }

                }

                unset($resultTh);

            } else {
                // Get the rest of the data
                $post->addMeta($rowAt['meta_key'], $rowAt['meta_value']);
            }


        }

        // Get the categories
        $queryCats = "SELECT WT.* FROM wp_terms WT, wp_term_taxonomy WTT, wp_term_relationships WPTR
        WHERE WT.term_id =  WTT.term_id
        AND WPTR.term_taxonomy_id = WTT.term_taxonomy_id
        AND WPTR.object_id = ".$post->id."
        AND WTT.taxonomy='category'
        ORDER BY WT.name ASC";

        $resultCats = mysql_query($queryCats) or die('Class '.__CLASS__.' -> '.__FUNCTION__.' : ' . mysql_error());
        while($rowCat = mysql_fetch_array($resultCats, MYSQL_ASSOC)){

            if($this->cache->get('category'.$rowCat['term_id']) == null){

                $category = new WpCategory($rowCat);
                WpCategories::addCategory($category);
                $this->cache->set('category'.$category->id, $category);

            }

            $post->addToCategory($this->cache->get('category'.$rowCat['term_id']));
        }

        unset($resultCats);

        // Get the tags
        $queryTags = "SELECT WT.* FROM wp_terms WT, wp_term_taxonomy WTT, wp_term_relationships WPTR
        WHERE WT.term_id =  WTT.term_id
        AND WPTR.term_taxonomy_id = WTT.term_taxonomy_id
        AND WPTR.object_id = ".$post->id."
        AND WTT.taxonomy='post_tag'
        ORDER BY WT.name ASC";

        $resultTags= mysql_query($queryTags) or die('Class '.__CLASS__.' -> '.__FUNCTION__.' : ' . mysql_error());
        while($rowTags = mysql_fetch_array($resultTags, MYSQL_ASSOC)){

            $post->addTag($rowTags['name'], $rowTags['slug'], $rowTags['term_id']);
        }

        unset($resultTags);

        unset($resultAt);

        return $post;
    }

}
?>
