<?php

class User extends BS_Controller {

  public function login() {
    $result = $this->post_model->login();
    $this->session->set_userdata('last_page', $result['last_page']);
    //save last page, just add userdata to session and go back to last page. If failed, go to failure page and keep last page saved for when they succeed or hit back.
    if ($result['logged_in']) {
      $this->session->set_userdata('bookswap_user', $result['userdata']);
    } else {
      $this->session->set_flashdata('headernotice', "Invalid username/password");
    }
    redirect($result['last_page']);
  }

  public function logout() {
    $this->session->sess_destroy();
    redirect(base_url() . 'index.php');
  }

}