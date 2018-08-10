<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
	    $id = 145;
	    
	    $en_id = $this->encrypter->encode($id);
	    $de_id = $this->encrypter->decode($en_id);
	    
// 	    echo $id;
// 	    echo "<br/>";
// 	    echo $en_id;
// 	    echo "<br/>";
// 	    echo $de_id;
// 	    echo "<br/>";
	    
	    $user = new stdClass();
	    
	    $user->username = "scad";
	    $user->lname    = "scad";
	    $user->fname    = "you";
	    
	    
	    $this->session->set_userdata('current_user', $user);
	    
	    echo $this->session->userdata('current_user')->username;
	    echo $this->session->userdata('current_user')->lname;
	    echo $this->session->userdata('current_user')->fname;
	    
// 	    echo $this->config_model->getConfig('Company');
		$this->load->view('welcome_message');
	}
}
