<?php 
/**
 * File: metadata.php
 * Description: Defination of this module and its roles and sub-modules
 * 
 * Example of sub module
 * 
 * $module['sub'][] = array(
		'title'=>'Item Category',
		'description'=>'Manage all item categories in the commissary',
		'icon'=>'category.png',
		'roles'=>array('Add','View','Edit','Delete')
	);
 */

$submenu = array();

if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Branch')) {
    $submenu[] = array('label'=>'Branches','url'=>site_url('branch/show'),'icon'=>'bullet','subitem'=>array());
}

if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Supplier')) {
    $submenu[] = array('label'=>'Suppliers','url'=>site_url('supplier/show'),'icon'=>'bullet','subitem'=>array());
}

if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Clothes Category')) {
    $submenu[] = array('label'=>'Clothes Category','url'=>site_url('clothes_category/show'),'icon'=>'bullet','subitem'=>array());
}

if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Service Type')) {
    $submenu[] = array('label'=>'Service Types','url'=>site_url('service_type/show'),'icon'=>'bullet','subitem'=>array());
}

if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Items')) {
    $submenu[] = array('label'=>'Items','url'=>site_url('item/show'),'icon'=>'bullet','subitem'=>array());
}


if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Equipment')) {
    $submenu[] = array('label'=>'Equipments','url'=>site_url('equipment/show'),'icon'=>'bullet','subitem'=>array());
}

if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Expense Particular')) {
    $submenu[] = array('label'=>'Expense Particulars','url'=>site_url('expense_particular/show'),'icon'=>'bullet','subitem'=>array());
}

if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Complaint')) {
    $submenu[] = array('label'=>'Complaints','url'=>site_url('complaint/show'),'icon'=>'bullet','subitem'=>array());
}



// main module
$module = array();
$module['main']['title'] 		= 'Master Files';
$module['main']['controller'] 	= 'master_files';
$module['main']['description'] 	= 'Manage all Master Files';
$module['main']['icon'] 		= 'la-folder';
$module['main']['roles'] 		= array();


// sub module

$module['sub']['Customer'] = array(
    'title'=>'Customers',
    'module_label'=>'Customer', // this will display on create/view/edit form
    'controller'=>'Customer',
    'description'=>'Manage all Customers',
    'icon'=>'la-building',
    'roles'=>array(     'Add Customer',
                        'View Customer',
                        'Edit Existing Customer',
                        'Delete Existing Customer',
    )
);

$module['sub']['Branch'] = array(
    'title'=>'Branches',
    'module_label'=>'Branch', // this will display on create/view/edit form
    'controller'=>'branch',
    'description'=>'Manage all Branches',
    'icon'=>'la-building',
    'roles'=>array(     'Add Branch',
                        'View Branch',
                        'Edit Existing Branch',
                        'Delete Existing Branch',
    )
);

$module['sub']['Suppliers'] = array(
		'title'=>'Suppliers',
		'module_label'=>'Suppliers', // this will display on create/view/edit form
		'controller'=>'supplier',
		'description'=>'Manage all Suppliers',
		'icon'=>'la-building',
		'roles'=>array(   'Add Suppliers',
		                  'View Suppliers',
		                  'Edit Existing Suppliers',
		                  'Delete Existing Suppliers',
		)
);

$module['sub']['Clothes Category'] = array(
    'title'=>'Clothes Category',
    'module_label'=>'Clothes Category', // this will display on create/view/edit form
    'controller'=>'clothes_category',
    'description'=>'Manage all Clothes Category',
    'icon'=>'la-map',
    'roles'=>array(     'Add Clothes Category',
                        'View Clothes Category',
                        'Edit Existing Clothes Category',
                        'Delete Existing Clothes Category',
    )
);

$module['sub']['Service Types'] = array(
    'title'=>'Service Types',
    'module_label'=>'Service Types', // this will display on create/view/edit form
    'controller'=>'service_type',
    'description'=>'Manage all Service Types',
    'icon'=>'la-map',
    'roles'=>array(     'Add Service Types',
                        'View Service Types',
                        'Edit Existing Service Types',
                        'Delete Existing Service Types',
    )
);

$module['sub']['Items'] = array(
    'title'=>'Items',
    'module_label'=>'Items', // this will display on create/view/edit form
    'controller'=>'item',
    'description'=>'Manage all Items',
    'icon'=>'la-bookmark',
    'roles'=>array(     'Add Items',
                        'View Items',
                        'Edit Existing Items',
                        'Delete Existing Items',
    )
);

$module['sub']['Equipments'] = array(
    'title'=>'Equipments',
    'module_label'=>'Equipments', // this will display on create/view/edit form
    'controller'=>'equipment',
    'description'=>'Manage all Equipments',
    'icon'=>'la-bookmark',
    'roles'=>array(     'Add Equipments',
                        'View Equipments',
                        'Edit Existing Equipments',
                        'Delete Existing Equipments',
    )
);

$module['sub']['Expense Particulars'] = array(
    'title'=>'Expense Particulars',
    'module_label'=>'Expense Particulars', // this will display on create/view/edit form
    'controller'=>'expense_particular',
    'description'=>'Manage all Expense Particulars',
    'icon'=>'la-bookmark',
    'roles'=>array(     'Add Expense Particulars',
                        'View Expense Particulars',
                        'Edit Existing Expense Particulars',
                        'Delete Existing Expense Particulars',
    )
);

$module['sub']['Complaints'] = array(
    'title'=>'Complaints',
    'module_label'=>'Complaints', // this will display on create/view/edit form
    'controller'=>'complaint',
    'description'=>'Manage all Complaints',
    'icon'=>'la-bookmark',
    'roles'=>array(     'Add Complaints',
                        'View Complaints',
                        'Edit Existing Complaints',
                        'Delete Existing Complaints',
    )
);


$module['main']['menu'] = $submenu;
$this->modules['Master Files']  = $module;
?>

