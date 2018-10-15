<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2018-10-15 04:37:12 --> Query error: No tables used - Invalid query: SELECT *
ERROR - 2018-10-15 04:41:15 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'WHERE  order_headers.osNo  LIKE '%1%' ESCAPE '!' 
AND  customers.fname  LIKE '%1' at line 2 - Invalid query: SELECT *
WHERE  order_headers.osNo  LIKE '%1%' ESCAPE '!' 
AND  customers.fname  LIKE '%1%' ESCAPE '!' 
AND  branches.branchName  LIKE '%1%' ESCAPE '!' 
AND  order_headers.date  LIKE '%1%' ESCAPE '!' 
AND  order_headers.isDiscounted  LIKE '%1%' ESCAPE '!' 
AND `order_headers`.`status` = '1'
ORDER BY `isDiscounted` ASC, `osNo` DESC
 LIMIT 25
