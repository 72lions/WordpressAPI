Wordpress PHP API
=============

A PHP API for read-only access to the Wordpress database. It also has support for caching implementations. Defaults to Memcache.

### Getting Categories
```php
/**
 * Returns an array with all the categories
 *
 * @param {Number} $start The beginning of the result set
 * @param {Number} $total The total items to laod
 * @param {String} $sort The sorting
 * @return {Array}
 */
getCategories($start = 0, $total = 10, $sort = 'name ASC')
```
Example:
```php
$api = new WpApi();
$api->getCategories(0, 10);
```

### Getting Posts
```php
/**
 * Returns an array with all the posts
 *
 * @param {Number} $categoryId The id of the parent category
 * @param {Number} $start The beginning of the result set
 * @param {Number} $total The total items to laod
 * @param {String} $sort The sorting
 *
 * @return {Array}
 */
getPosts($categoryId = null, $tagId = null, $start = 0, $total = 10, $sort = 'post_date DESC')
```
Example:
```php
$api = new WpApi();
$api->getPosts(10, null, 0, 10);
```

### Getting Pages
```php
/**
 * Returns an array with all the pages
 *
 * @param {Number} $categoryId The id of the parent category
 * @param {Number} $start The beginning of the result set
 * @param {Number} $total The total items to laod
 *
 * @return {Array}
 */
getPages($categoryId = null, $start = 0, $total = 10, $sort = 'post_date DESC')
```
Example:
```php
$api = new WpApi();
$api->getPages(12, 0, 10);
```

### Getting the details of a Post / Page
```php
/**
 * Gets the details of a post
 *
 * @param {Number} $postId The id of the post that we want to get the details

 * @return {Object}
 */
getPostDetails($postId)
```
Example

```php
$api = new WpApi();
$api->getPostDetails(1012);
```
