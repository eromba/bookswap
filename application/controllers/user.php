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

  public function my_account() {
    if ($this->session->userdata('bookswap_user')) {
      $data['userdata'] = $this->session->userdata('bookswap_user');
      $data['title'] = 'Account Settings';
      $this->load->view('header', $data);
      $this->load->view('my_account', $data);
      $this->load->view('footer', $data);
    } else {
      echo('please log in');
    }
  }

  public function update_account() {
    if ($this->account_model->update_user($this->session->userdata('bookswap_user'))) {
      
    } else {

    }
    $netid = $this->session->userdata('bookswap_user');
    $netid = $netid['netid'];
    $data['userdata'] = $this->post_model->get_user($netid);
    $this->load->view('header', $data);
    $this->load->view('my_account', $data);
    $this->load->view('footer', $data);
  }

}