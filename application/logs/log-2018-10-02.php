<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2018-10-02 05:25:48 --> Query error: Unknown column 'service_id' in 'field list' - Invalid query: INSERT INTO `clothes_categories` (`category`, `service_id`) VALUES ('testing 1','1'), ('testing 1','3'), ('testing 1','4'), ('testing 1','5'), ('testing 1','67'), ('testing 1','68'), ('testing 1','69'), ('testing 1','70'), ('testing 1','71'), ('testing 1','72'), ('testing 1','73'), ('testing 1','74'), ('testing 1','75')
ERROR - 2018-10-02 05:41:25 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 142
ERROR - 2018-10-02 05:44:02 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 142
ERROR - 2018-10-02 05:45:50 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 142
ERROR - 2018-10-02 07:02:28 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'DISTINCT(category)
FROM `clothes_categories`
ORDER BY `category` ASC
 LIMIT 25, ' at line 1 - Invalid query: SELECT `clothes_categories`.*, DISTINCT(category)
FROM `clothes_categories`
ORDER BY `category` ASC
 LIMIT 25, 25
ERROR - 2018-10-02 07:13:23 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'DISTINCT(category)
FROM `clothes_categories`
ORDER BY `category` ASC
 LIMIT 25' at line 1 - Invalid query: SELECT `clothes_categories`.*, DISTINCT(category)
FROM `clothes_categories`
ORDER BY `category` ASC
 LIMIT 25
ERROR - 2018-10-02 07:13:48 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'DISTINCT("category")
FROM `clothes_categories`
ORDER BY `category` ASC
 LIMIT 25' at line 1 - Invalid query: SELECT `clothes_categories`.*, DISTINCT("category")
FROM `clothes_categories`
ORDER BY `category` ASC
 LIMIT 25
ERROR - 2018-10-02 07:16:31 --> Query error: Unknown column 'clothes_categoriescategory' in 'field list' - Invalid query: SELECT DISTINCT `clothes_categories`.*, `clothes_categoriescategory`
FROM `clothes_categories`
ORDER BY `category` ASC
 LIMIT 25
ERROR - 2018-10-02 07:42:07 --> 404 Page Not Found: api/Cities/index
ERROR - 2018-10-02 07:47:35 --> 404 Page Not Found: api/Cities/index
ERROR - 2018-10-02 07:49:06 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\api\ApiOrder.php 583
ERROR - 2018-10-02 07:49:06 --> Query error: Column 'qty' cannot be null - Invalid query: INSERT INTO `order_details` (`orderID`, `qty`, `amount`, `rate`, `unit`, `serviceID`) VALUES (133, NULL, NULL, NULL, NULL, '69')
ERROR - 2018-10-02 07:56:05 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\api\ApiOrder.php 583
ERROR - 2018-10-02 07:56:05 --> Query error: Column 'qty' cannot be null - Invalid query: INSERT INTO `order_details` (`orderID`, `qty`, `amount`, `rate`, `unit`, `serviceID`) VALUES (134, NULL, NULL, NULL, NULL, '69')
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> array_keys() expects parameter 1 to be array, string given C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1566
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> sort() expects parameter 1 to be array, null given C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1567
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> array_keys() expects parameter 1 to be array, string given C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1572
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> array_diff(): Argument #1 is not an array C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1572
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1572
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> array_keys() expects parameter 1 to be array, string given C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1572
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> array_diff(): Argument #1 is not an array C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1572
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1572
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> ksort() expects parameter 1 to be array, string given C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1579
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1584
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> array_keys() expects parameter 1 to be array, string given C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1572
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> array_diff(): Argument #1 is not an array C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1572
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1572
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> array_keys() expects parameter 1 to be array, string given C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1572
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> array_diff(): Argument #1 is not an array C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1572
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1572
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> ksort() expects parameter 1 to be array, string given C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1579
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1584
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> array_diff(): Argument #1 is not an array C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1572
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1572
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> array_diff(): Argument #2 is not an array C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1572
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1572
ERROR - 2018-10-02 07:58:59 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 1595
ERROR - 2018-10-02 07:58:59 --> Query error: Column count doesn't match value count at row 3 - Invalid query: INSERT INTO `clothes_categories_ids` () VALUES (), (), (Array,'1')
ERROR - 2018-10-02 08:01:30 --> Query error: Unknown column 'Array' in 'field list' - Invalid query: INSERT INTO `clothes_categories_ids` (`clothes_cat_id`, `service_id`) VALUES (Array,'1'), (Array,'3'), (Array,'4'), (Array,'5'), (Array,'67'), (Array,'68'), (Array,'69'), (Array,'70'), (Array,'71'), (Array,'72'), (Array,'73'), (Array,'74'), (Array,'75')
ERROR - 2018-10-02 08:08:13 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\api\ApiOrder.php 583
ERROR - 2018-10-02 08:11:57 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'IS NULL' at line 3 - Invalid query: SELECT *
FROM `clothes_categories`
WHERE  IS NULL
ERROR - 2018-10-02 08:12:52 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 154
ERROR - 2018-10-02 08:12:53 --> Query error: Unknown column 'Array' in 'field list' - Invalid query: INSERT INTO `table_logs` (`userID`, `host`, `hostname`, `date`, `module`, `table`, `pkey`, `pid`, `operation`, `logs`) VALUES ('1', '::1', '', '2018-10-02 08:12:53', 'Clothes Category', 'clothes_categories', 'clothesCatID', Array, 'Insert', 'Record - ')
ERROR - 2018-10-02 08:13:11 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 154
ERROR - 2018-10-02 08:13:52 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 154
ERROR - 2018-10-02 08:22:32 --> Query error: Not unique table/alias: 'clothes_categories' - Invalid query: SELECT `clothes_categories*`
FROM `clothes_categories`, `clothes_categories`
WHERE `clothesCatID` = '158'
ERROR - 2018-10-02 08:23:00 --> Query error: Not unique table/alias: 'clothes_categories' - Invalid query: SELECT `clothes_categories*`
FROM `clothes_categories`, `clothes_categories`
WHERE `clothesCatID` = '158'
ERROR - 2018-10-02 08:23:08 --> Query error: Not unique table/alias: 'clothes_categories' - Invalid query: SELECT `clothes_categories*`
FROM `clothes_categories`, `clothes_categories`
WHERE `clothesCatID` = '158'
ERROR - 2018-10-02 08:23:26 --> Query error: Unknown column 'clothes_categories*' in 'field list' - Invalid query: SELECT `clothes_categories*`
FROM `clothes_categories`
WHERE `clothesCatID` = '158'
ERROR - 2018-10-02 08:24:52 --> Query error: Unknown column 'clothes_categories*' in 'field list' - Invalid query: SELECT `clothes_categories*`
FROM `clothes_categories`
WHERE `clothesCatID` = '158'
ERROR - 2018-10-02 08:39:57 --> Severity: error --> Exception: syntax error, unexpected '--' (T_DEC) C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 419
ERROR - 2018-10-02 08:39:58 --> Severity: error --> Exception: syntax error, unexpected '--' (T_DEC) C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 419
ERROR - 2018-10-02 08:40:01 --> Severity: error --> Exception: syntax error, unexpected '--' (T_DEC) C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 419
ERROR - 2018-10-02 09:15:38 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 436
ERROR - 2018-10-02 09:15:38 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 436
ERROR - 2018-10-02 09:15:38 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 436
ERROR - 2018-10-02 09:15:38 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 436
ERROR - 2018-10-02 09:15:38 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 436
ERROR - 2018-10-02 09:15:38 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 436
ERROR - 2018-10-02 09:15:38 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 436
ERROR - 2018-10-02 09:15:38 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 436
ERROR - 2018-10-02 09:15:38 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 436
ERROR - 2018-10-02 09:15:38 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 436
ERROR - 2018-10-02 09:15:38 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 436
ERROR - 2018-10-02 09:15:38 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 436
ERROR - 2018-10-02 09:15:38 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 436
ERROR - 2018-10-02 09:23:04 --> 404 Page Not Found: api/Cities/index
ERROR - 2018-10-02 09:24:59 --> Severity: Warning --> array_key_exists() expects exactly 2 parameters, 3 given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 438
ERROR - 2018-10-02 09:24:59 --> Severity: Warning --> array_key_exists() expects exactly 2 parameters, 3 given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 438
ERROR - 2018-10-02 09:24:59 --> Severity: Warning --> array_key_exists() expects exactly 2 parameters, 3 given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 438
ERROR - 2018-10-02 09:24:59 --> Severity: Warning --> array_key_exists() expects exactly 2 parameters, 3 given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 438
ERROR - 2018-10-02 09:24:59 --> Severity: Warning --> array_key_exists() expects exactly 2 parameters, 3 given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 438
ERROR - 2018-10-02 09:24:59 --> Severity: Warning --> array_key_exists() expects exactly 2 parameters, 3 given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 438
ERROR - 2018-10-02 09:24:59 --> Severity: Warning --> array_key_exists() expects exactly 2 parameters, 3 given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 438
ERROR - 2018-10-02 09:24:59 --> Severity: Warning --> array_key_exists() expects exactly 2 parameters, 3 given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 438
ERROR - 2018-10-02 09:24:59 --> Severity: Warning --> array_key_exists() expects exactly 2 parameters, 3 given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 438
ERROR - 2018-10-02 09:24:59 --> Severity: Warning --> array_key_exists() expects exactly 2 parameters, 3 given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 438
ERROR - 2018-10-02 09:24:59 --> Severity: Warning --> array_key_exists() expects exactly 2 parameters, 3 given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 438
ERROR - 2018-10-02 09:25:00 --> Severity: Warning --> array_key_exists() expects exactly 2 parameters, 3 given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 438
ERROR - 2018-10-02 09:25:00 --> Severity: Warning --> array_key_exists() expects exactly 2 parameters, 3 given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 438
ERROR - 2018-10-02 09:36:44 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 154
ERROR - 2018-10-02 10:03:05 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 310
ERROR - 2018-10-02 10:05:36 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 310
ERROR - 2018-10-02 10:49:40 --> Query error: Unknown column 'Array' in 'field list' - Invalid query: INSERT INTO `clothes_child` (`child`, `service_id`) VALUES (Array,169), (Array,169), (Array,169), (Array,169), (Array,169), (Array,169), (Array,169), (Array,169), (Array,169), (Array,169), (Array,169), (Array,169), (Array,169)
ERROR - 2018-10-02 10:50:45 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 195
ERROR - 2018-10-02 10:50:45 --> Could not find the language line "insert_batch() called with no data"
ERROR - 2018-10-02 10:50:59 --> Query error: Unknown column 'Array' in 'field list' - Invalid query: INSERT INTO `clothes_child` (`child`, `parent`) VALUES (Array,182), (Array,182), (Array,182), (Array,182), (Array,182), (Array,182), (Array,182), (Array,182), (Array,182), (Array,182), (Array,182), (Array,182), (Array,182)
ERROR - 2018-10-02 10:53:48 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 154
ERROR - 2018-10-02 10:53:48 --> Query error: Column 'pid' cannot be null - Invalid query: INSERT INTO `table_logs` (`userID`, `host`, `hostname`, `date`, `module`, `table`, `pkey`, `pid`, `operation`, `logs`) VALUES ('1', '::1', '', '2018-10-02 10:53:48', 'Clothes Category', 'clothes_categories', 'clothesCatID', NULL, 'Insert', 'Record - ')
ERROR - 2018-10-02 10:54:11 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 195
ERROR - 2018-10-02 10:54:11 --> Could not find the language line "insert_batch() called with no data"
ERROR - 2018-10-02 10:54:33 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 154
ERROR - 2018-10-02 10:59:25 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 154
ERROR - 2018-10-02 11:05:24 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 154
ERROR - 2018-10-02 11:15:16 --> Severity: 4096 --> Object of class stdClass could not be converted to string C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 2442
ERROR - 2018-10-02 11:15:16 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near '' at line 3 - Invalid query: SELECT `serviceID`
FROM `clothes_categories`
WHERE `clothesCatID` = 
ERROR - 2018-10-02 11:15:48 --> Severity: 4096 --> Object of class stdClass could not be converted to string C:\xampp\htdocs\iwash\system\database\DB_query_builder.php 2442
ERROR - 2018-10-02 11:15:48 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near '' at line 3 - Invalid query: SELECT `serviceID`
FROM `clothes_categories`
WHERE `clothesCatID` = 
ERROR - 2018-10-02 11:19:01 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 154
ERROR - 2018-10-02 12:00:36 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 303
ERROR - 2018-10-02 12:10:38 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 308
ERROR - 2018-10-02 12:10:38 --> Could not find the language line "insert_batch() called with no data"
ERROR - 2018-10-02 12:11:33 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 308
ERROR - 2018-10-02 12:15:48 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 154
ERROR - 2018-10-02 12:16:53 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 154
ERROR - 2018-10-02 12:21:29 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 154
ERROR - 2018-10-02 12:22:01 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 308
ERROR - 2018-10-02 12:22:31 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 308
ERROR - 2018-10-02 12:42:27 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 308
ERROR - 2018-10-02 12:48:09 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 154
ERROR - 2018-10-02 12:48:21 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 341
ERROR - 2018-10-02 12:58:50 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 154
ERROR - 2018-10-02 13:03:17 --> Severity: Warning --> trim() expects parameter 1 to be string, array given C:\xampp\htdocs\iwash\application\controllers\Clothes_category.php 154
