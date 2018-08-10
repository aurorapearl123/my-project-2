<?php  
$submenu = array();
//if ($this->userrole_model->has_access($this->session->userdata('current_userID'),'Kiss Apple')) {
$submenu[] = array('label'=>'Employees','url'=>site_url(''),'icon'=>'bullet','subitem'=>array());
$submenu[] = array('label'=>'Custom List','url'=>site_url(''),'icon'=>'bullet','subitem'=>array());
$submenu[] = array('label'=>'Batch Update','url'=>site_url(''),'icon'=>'bullet','subitem'=>array());
$submenu[] = array('label'=>'Credentials','url'=>site_url('apple/show'),'icon'=>'la-group',
    'subitem'=> array(
        array('label'=>'Family Members','url'=>site_url('apple/show'),'icon'=>'bullet'),
        array('label'=>'Education Backgrounds','url'=>site_url('apple/show'),'icon'=>'bullet'),
        array('label'=>'Service Eligibilities','url'=>site_url('apple/show'),'icon'=>'bullet'),
        array('label'=>'Work Experiences','url'=>site_url('apple/show'),'icon'=>'bullet'),
        array('label'=>'Volunteer Works','url'=>site_url('apple/show'),'icon'=>'bullet'),
        array('label'=>'Training Programs','url'=>site_url('apple/show'),'icon'=>'bullet'),
        array('label'=>'Special Skills','url'=>site_url('apple/show'),'icon'=>'bullet'),
        array('label'=>'Awards and Recognitions','url'=>site_url('apple/show'),'icon'=>'bullet'),
        array('label'=>'Organizations','url'=>site_url('apple/show'),'icon'=>'bullet')
    )
);

//$submenu[] = array('label'=>'Reports','url'=>site_url(''),'icon'=>'bullet','subitem'=>array());

// main module
$module = array();
$module['main']['title'] 		= 'Employees';
$module['main']['controller'] 	= 'employee';
$module['main']['description'] 	= 'Manage all Employees';
$module['main']['icon'] 		= 'la-group';
$module['main']['roles'] 		= array();


//}
$module['main']['menu'] = $submenu;
$this->modules['Employee']  = $module;
?>