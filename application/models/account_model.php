<?php
class Account_model extends CI_Model{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		//echo("Account_model");
	}

	public function update_user($user_session){
		$newEmail =  $this->input->post('userEmail');
		$newPhone =  $this->input->post('userPhone');
		$newdata = array('email' => $newEmail,
						'phone' => $newPhone,
					);

		$this->db->where('netid', $user_session->netid);
		$this->db->update('users', $newdata); 
		return TRUE;
	}
	public function get_user($netid){
		$query = $this->db->get_where('users', array('netid' => $netid));
		return $query->result();
	}

}