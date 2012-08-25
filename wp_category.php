<?php
/**
 * Responsible for parsing and storing the category data
 *
 * @module 72lionsPHP
 * @class WpCategory
 * @author Thodoris Tsiridis
 * @version 1.0
 */
class WpCategory {

    /**
     * Constants
     */
     public static $SORT_NAME_ASC = 'name ASC';
     public static $SORT_NAME_DESC = 'name DESC';

     /**
      * Public variables
      */
     public $name = '';
     public $slug = '';
     public $id = 0;

    /**
     * Constructor
     * @param {Array} $object The object that we need to parse
     * @author Thodoris Tsiridis
     */
    public function __construct($object) {

        $this->name = $object['name'];
        $this->slug = $object['slug'];
        $this->id = $object['term_id'];

    }

}
?>
