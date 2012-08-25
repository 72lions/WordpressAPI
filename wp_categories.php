<?php
/**
 * Responsible for saving categories and retrieving all the categoris
 *
 * @module 72lionsPHP
 * @class WpCategories
 * @author Thodoris Tsiridis
 * @version 1.0
 */
class WpCategories {
  /**
   * An array containing all the categories
   * @var array
   */
  public static $categories = array();

    /**
     * Adds a category object to the categories array
     *
     * @param {Category} $category The category we want to add to the categories array
     * @author Thodoris Tsiridis
     */
    public static function addCategory($category) {
        self::$categories[] = $category;
    }
}
