ERROR - 2018-09-28 06:23:23 --> Severity: error --> Exception: Call to undefined method Order::getDetails() C:\xampp\htdocs\iwash\application\controllers\ApiHelpers.php 141
ERROR - 2018-09-28 06:26:10 --> Severity: error --> Exception: Call to undefined method Order::order_detail_categories() C:\xampp\htdocs\iwash\application\controllers\Order.php 1253
ERROR - 2018-09-28 06:33:06 --> Severity: error --> Exception: Class 'ApiOrder' not found C:\xampp\htdocs\iwash\application\controllers\Order.php 765
ERROR - 2018-09-28 06:36:22 --> Severity: error --> Exception: Call to undefined method Order::getCategoriesById() C:\xampp\htdocs\iwash\application\controllers\Order.php 762
ERROR - 2018-09-28 06:36:23 --> Severity: error --> Exception: Call to undefined method Order::getCategoriesById() C:\xampp\htdocs\iwash\application\controllers\Order.php 762
ERROR - 2018-09-28 06:59:58 --> Query error: Not unique table/alias: 'order_headers' - Invalid query: SELECT `branches`.`branchName`, `branches`.`branchCode`, `customers`.`fname`, `customers`.`mname`, `customers`.`lname`, `users`.`firstName`, `users`.`middleName`, `users`.`lastName`, `cancelledUser`.`firstName` as `cancelledFirstName`, `cancelledUser`.`middleName` as `cancelledMiddleName`, `cancelledUser`.`lastName` as `cancelledLastName`, `order_headers`.*, `order_headers`.`status` as `order_status`, `order_headers`.*, `service_types`.*, `customers`.*, `branches`.`branchName`
FROM (`order_headers`, `order_headers`)
LEFT JOIN `branches` ON `order_headers`.`branchID`=`branches`.`branchID`
LEFT JOIN `customers` ON `order_headers`.`custID`=`customers`.`custID`
LEFT JOIN `users` ON `order_headers`.`createdBy`=`users`.`userID`
LEFT JOIN `users` as `cancelledUser` ON `order_headers`.`cancelledBy`=`users`.`userID`
LEFT JOIN `service_types` ON `order_headers`.`serviceID`=`service_types`.`serviceID`
LEFT JOIN `branches` ON `order_headers`.`branchID`=`branches`.`branchID`
LEFT JOIN `customers` ON `order_headers`.`custID`=`customers`.`custID`
WHERE `orderID` = '123'
AND `order_headers`.`orderID` = '123'
ERROR - 2018-09-28 07:02:33 --> Query error: Not unique table/alias: 'order_headers' - Invalid query: SELECT `branches`.`branchName`, `branches`.`branchCode`, `customers`.`fname`, `customers`.`mname`, `customers`.`lname`, `users`.`firstName`, `users`.`middleName`, `users`.`lastName`, `cancelledUser`.`firstName` as `cancelledFirstName`, `cancelledUser`.`middleName` as `cancelledMiddleName`, `cancelledUser`.`lastName` as `cancelledLastName`, `order_headers`.*, `order_headers`.`status` as `order_status`, `order_headers`.*, `service_types`.*, `customers`.*, `branches`.`branchName`
FROM (`order_headers`, `order_headers`)
LEFT JOIN `branches` ON `order_headers`.`branchID`=`branches`.`branchID`
LEFT JOIN `customers` ON `order_headers`.`custID`=`customers`.`custID`
LEFT JOIN `users` ON `order_headers`.`createdBy`=`users`.`userID` as `table1`
LEFT JOIN `users` as `cancelledUser` ON `order_headers`.`cancelledBy`=`users`.`userID`
LEFT JOIN `service_types` ON `order_headers`.`serviceID`=`service_types`.`serviceID`
LEFT JOIN `branches` ON `order_headers`.`branchID`=`branches`.`branchID`
LEFT JOIN `customers` ON `order_headers`.`custID`=`customers`.`custID`
WHERE `orderID` = '123'
AND `order_headers`.`orderID` = '123'
ERROR - 2018-09-28 07:03:50 --> Query error: Not unique table/alias: 'order_headers' - Invalid query: SELECT `order_headers`.*, `order_headers`.`status` as `order_status`, `order_headers`.*, `service_types`.*, `customers`.*, `branches`.`branchName`
FROM (`order_headers`, `order_headers`)
LEFT JOIN `users` ON `order_headers`.`createdBy`=`users`.`userID` as `table1`
LEFT JOIN `users` as `cancelledUser` ON `order_headers`.`cancelledBy`=`users`.`userID`
LEFT JOIN `service_types` ON `order_headers`.`serviceID`=`service_types`.`serviceID`
LEFT JOIN `branches` ON `order_headers`.`branchID`=`branches`.`branchID`
LEFT JOIN `customers` ON `order_headers`.`custID`=`customers`.`custID`
WHERE `orderID` = '123'
AND `order_headers`.`orderID` = '123'
ERROR - 2018-09-28 07:05:50 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 122
ERROR - 2018-09-28 07:08:10 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 122
ERROR - 2018-09-28 07:08:50 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 122
ERROR - 2018-09-28 07:09:56 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 122
ERROR - 2018-09-28 07:10:14 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 122
ERROR - 2018-09-28 07:10:37 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 122
ERROR - 2018-09-28 07:10:51 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 122
ERROR - 2018-09-28 07:12:04 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 122
ERROR - 2018-09-28 07:12:31 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 118
ERROR - 2018-09-28 07:13:55 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 118
ERROR - 2018-09-28 07:14:20 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 118
ERROR - 2018-09-28 07:14:21 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 118
ERROR - 2018-09-28 07:14:49 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 118
ERROR - 2018-09-28 07:15:34 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 118
ERROR - 2018-09-28 07:15:35 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 118
ERROR - 2018-09-28 07:16:04 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 118
ERROR - 2018-09-28 07:17:50 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 130
ERROR - 2018-09-28 07:17:50 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 164
ERROR - 2018-09-28 07:22:09 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 152
ERROR - 2018-09-28 07:22:31 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 152
ERROR - 2018-09-28 07:23:30 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\view.php 150
ERROR - 2018-09-28 07:24:34 --> 404 Page Not Found: api/Cities/index
ERROR - 2018-09-28 07:26:10 --> 404 Page Not Found: api/Cities/index
ERROR - 2018-09-28 07:33:05 --> Query error: Unknown column 'order_details.clothesCatID' in 'field list' - Invalid query: SELECT `order_details`.`qty`, `order_details`.`clothesCatID`, `clothes_categories`.`category`
FROM `order_details`
RIGHT JOIN `clothes_categories` ON `order_details`.`clothesCatID`=`clothes_categories`. `clothesCatID`
WHERE `orderID` = ''
ERROR - 2018-09-28 09:42:01 --> Query error: Unknown column 'order_details.clothesCatID' in 'field list' - Invalid query: SELECT `order_details`.`qty`, `order_details`.`clothesCatID`, `clothes_categories`.`category`
FROM `order_details`
RIGHT JOIN `clothes_categories` ON `order_details`.`clothesCatID`=`clothes_categories`. `clothesCatID`
WHERE `orderID` = '130'
ERROR - 2018-09-28 09:44:21 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 47
ERROR - 2018-09-28 09:44:21 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 72
ERROR - 2018-09-28 09:44:21 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 406
ERROR - 2018-09-28 09:44:21 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 431
ERROR - 2018-09-28 09:45:39 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 47
ERROR - 2018-09-28 09:45:39 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 72
ERROR - 2018-09-28 09:45:53 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 47
ERROR - 2018-09-28 09:45:53 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 72
ERROR - 2018-09-28 09:48:55 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 47
ERROR - 2018-09-28 09:48:55 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 88
ERROR - 2018-09-28 09:50:05 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 88
ERROR - 2018-09-28 09:53:23 --> Severity: error --> Exception: Cannot use object of type stdClass as array C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 23
ERROR - 2018-09-28 09:53:25 --> Severity: error --> Exception: Cannot use object of type stdClass as array C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 23
ERROR - 2018-09-28 09:53:52 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 122
ERROR - 2018-09-28 09:54:07 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 122
ERROR - 2018-09-28 09:54:39 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 122
ERROR - 2018-09-28 09:56:42 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 123
ERROR - 2018-09-28 10:00:12 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 123
ERROR - 2018-09-28 10:00:57 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 123
ERROR - 2018-09-28 10:01:32 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 123
ERROR - 2018-09-28 10:03:34 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 123
ERROR - 2018-09-28 10:03:49 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\views\modules\inventory\withdrawal_slip\create.php 33
ERROR - 2018-09-28 10:04:07 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\views\modules\inventory\withdrawal_slip\edit.php 34
ERROR - 2018-09-28 10:08:03 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 123
ERROR - 2018-09-28 10:08:40 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\views\modules\inventory\withdrawal_slip\edit.php 34
ERROR - 2018-09-28 10:09:34 --> Severity: Warning --> count(): Parameter must be an array or an object that implements Countable C:\xampp\htdocs\iwash\application\views\modules\inventory\receiving_report\edit.php 34
ERROR - 2018-09-28 10:12:46 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 147
ERROR - 2018-09-28 10:15:57 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 147
ERROR - 2018-09-28 10:16:29 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 147
ERROR - 2018-09-28 10:16:56 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 147
ERROR - 2018-09-28 10:17:32 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 147
ERROR - 2018-09-28 10:18:24 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 147
ERROR - 2018-09-28 10:42:40 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 147
ERROR - 2018-09-28 10:42:41 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 147
ERROR - 2018-09-28 10:55:15 --> Query error: Unknown column 'order_details.clothesCatID' in 'field list' - Invalid query: SELECT `order_details`.`qty`, `order_details`.`clothesCatID`, `clothes_categories`.`category`
FROM `order_details`
RIGHT JOIN `clothes_categories` ON `order_details`.`clothesCatID`=`clothes_categories`. `clothesCatID`
WHERE `orderID` = '124'
ERROR - 2018-09-28 10:58:19 --> Query error: Unknown column 'order_detail_categories.serviceID' in 'field list' - Invalid query: SELECT `order_detail_categories`.`serviceID`, `order_detail_categories`.`category`
FROM `order_details`
RIGHT JOIN `order_detail_categories` ON `order_details`.`id`=`order_detail_categories`.`order_detail_id`
RIGHT JOIN `clothes_categories` ON `clothes_categories`.`clothesCatID`=`order_detail_categories`.`clothes_catID`
WHERE `orderID` = '124'
ERROR - 2018-09-28 11:02:49 --> Severity: Warning --> Invalid argument supplied for foreach() C:\xampp\htdocs\iwash\application\views\modules\order\order\edit.php 147
