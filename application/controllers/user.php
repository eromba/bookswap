<?php

class User extends BS_Controller {

  public function login() {
    $result = $this->authenticate();
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

  public function authenticate(){
    $my_result = array(
      'logged_in' => FALSE,
      'last_page' => $this->input->post('current_page'),
    );
    if ($this->input->post('login')) {
      $netid = $this->input->post('username');
      $my_result['logged_in'] = TRUE;
      $my_result['userdata'] = $this->user_model->get_users(array('netid' => $netid));
      if ( ! $my_result['userdata']) {
        $newuser = array(
          'netid' => $netid,
          'email' => 'john@example.edu',
          'first_name' => 'John',
        );
        $this->user_model->add_user($newuser);
        $my_result['userdata'] = $this->user_model->get_users(array('netid' => $netid));
        if ( ! $my_result['userdata']) {
          return FALSE;
        }
      }
    }
    return $my_result;
  }

}
