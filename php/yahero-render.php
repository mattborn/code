<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

// Custom controller to adopt Rails-like naming conventions for views

class Front_controller Extends MY_Controller {

  function __construct() {
    parent::__construct();
  }
  
  protected function render($template='layout_front_updated') {
    $view_path = $this->controller_name . '/' . $this->action_name . '.tpl.php';
    
    if (file_exists(APPPATH . 'views/' . $view_path)) {
      $this->data['content'] .= $this->load->view($view_path, $this->data, true);
    }
    
    $this->load->view("$template.tpl.php", $this->data);
  }

}