<?php
class Post_model extends CI_Model{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	//	echo("Post_model");
	}

	public function get_books(){
		$query = $this->db->get('books');
		return $query->result();
	}
	public function get_books_by_all($q){
		$this->db->from('books');
		$query = $this->db->like('subj_class', $q);
		$query = $this->db->or_like('title', $q);
		$query = $this->db->or_like('subject', $q);
		$query = $this->db->get();
		return $query->result();
	}
	public function get_books_by_id($id){
		$query = $this->db->get_where('books', array('id' => $id));
		return $query->result();
	}
	public function get_books_by_isbn($isbn){
		$query = $this->db->get_where('books', array('isbn' => $isbn));
		return $query->result();
	}
	public function get_books_by_title($title){
		$this->db->from('books');
		$query=$this->db->like('title', $title);
		$query = $this->db->get();
		return $query->result();
	}
	public function get_books_by_course($course){
		$this->db->from('books');
		$query=$this->db->like('subj_class', $course);
		$query = $this->db->get();
		return $query->result();
	}
	public function get_books_by_subject($subject){
		$this->db->from('books');
		$query=$this->db->like('subject', $subject);
		$query = $this->db->get();
		return $query->result();
	}
	public function add_book(){
		$seller = $this->input->post('seller');
		$price =  $this->input->post('price');
		$id = mt_rand();
		$title = $this->input->post('title');
		$isbn =  $this->input->post('isbn');
		$course =  $this->input->post('course');
		$subject =  $this->input->post('subject');
		$cover =  $this->input->post('cover');
		$section =  $this->input->post('cover');
		$data = array(
			'isbn' => $isbn,
			'course' => $course,
			'subject' => $subject,
			'cover' => $cover,
			'section' => $section,
			'title' => $title,
			'seller' => $seller,
			'price' => $price
		);
		return ($this->db->insert('books', $data));
	}

	public function remove_book(){
		
	}
	public function add_post($seller){
		//https://plus.google.com/+LilPeck/posts/bLm9S75srcm
		$value = strval(strip_tags($this->input->post('price'))); //figured it couldn't hurt to make it string
		$myval = htmlentities($value); //important part
		$price = intval(preg_replace('/[\$\,]/', '', $myval)); //remove $ and comma
		$notes =  $this->input->post('notes');
		$bid =  $this->input->post('bid');
		$edition =  $this->input->post('edition');
		$data = array(
			'bid' => $bid,
			'seller' => $seller,
			'price' => $price,
			'notes' => $notes,
			'edition' => $edition
		);
		//var_dump($data);
		$this->db->insert('posts', $data);

		$this->db->where('id', $bid);
		$this->db->set('stock', 'stock+1', FALSE);
		$this->db->update('books');
	}
	public function update_post(){
		$this->db->where('id', $this->input->post('pid'));
		$this->db->set('price', $this->input->post('price'));
		$this->db->set('notes', $this->input->post('notes'));
		$this->db->set('edition', $this->input->post('edition'));
		$this->db->set('condition', $this->input->post('condition'));
		$this->db->update('posts');
	}
	public function have_cover($isbn){
		$this->db->where('isbn', $isbn);
		$this->db->set('have_cover', 1);
		$this->db->update('books');
	}
	public function remove_post(){
		$netid=$this->session->userdata('bookswap_user');
		$netid=$netid->netid;
		//var_dump($netid);
		$id = $this->input->post('post_id');
		$post = $this->get_posts_by_id($id);
		if($post){
			$post=$post[0];
			if($post->seller==$netid){
				$this->db->insert('removed_posts',$post);
				$this->db->delete('posts', array('id' => $id)); 
				return $id;
			}
			return "Error";
		}else{
			return "Error";
		}

	}
	public function get_posts_by_bid($bid){
		$query = $this->db->get_where('posts', array('bid' => $bid));
		return $query->result();
	}
	public function get_posts_by_id($id){
		$query = $this->db->get_where('posts', array('id' => $id));
		return $query->result();
	}
	public function get_posts_by_seller($netid){
		$query = $this->db->get_where('posts', array('seller' => $netid));
		return $query->result();
	}
	public function get_min_price($bid){
		$this->db->select_min('price');
		$query = $this->db->get_where('posts', array('bid' => $bid));
		// Produces: SELECT MIN(age) as age FROM members
		//$query = $this->db->get_where('posts', array('bid' => $bid,'price <'=>$price));
		return $query->result();
	}
	public function get_post_by_user(){

	}
	public function get_post_by_price(){

	}
	public function change_stock($bid,$amt){

	}
	public function get_user($netid){
		$query = $this->db->get_where('users', array('netid' => $netid));
		return $query->result();
	}
	public function add_user($data){
	
		return ($this->db->insert('users', $data));	
	}
	        /**
         * Your Amazon Access Key Id
         * @access private
         * @var string
         */
        private $public_key     = "";
        
        /**
         * Your Amazon Secret Access Key
         * @access private
         * @var string
         */
        private $private_key    = "";
        
        /**
         * Your Amazon Associate Tag
         * Now required, effective from 25th Oct. 2011
         * @access private
         * @var string
         */
        private $associate_tag  = "";
	private function verifyXmlResponse($response){
		//var_dump($response);
		if ($response === False){
			throw new Exception("Could not connect to Amazon");
		}else{
			if (isset($response->Items->Item->ItemAttributes->Title)){
				return ($response);
			}else{
		    		throw new Exception("Invalid xml response.");
			}
		}
	}
	public function get_amazon_from_isbn($isbn){
		require('aws_signed_request.php');
		//require('amazon_api_class.php');
		$parameters = array("Operation"     => "ItemLookup",
                                                    "ItemId"        => $isbn,
                                                    "SearchIndex"   => "Books",
                                                    "IdType"        => "EAN",
                                                    "ResponseGroup" => "Medium");
		try
		{
			//$obj = new AmazonProductAPI();
			$result = aws_signed_request("com", $parameters, $this->public_key, $this->private_key, $this->associate_tag);
			return $result;
		}
			catch(Exception $e)
		{
			echo $e->getMessage();
		}
	}
	public function update_amazon_data($book,$uptitle){
		$this->db->where('isbn', $book->isbn);
		if($book->amzn_used_price) $this->db->set('amzn_used_price', $book->amzn_used_price, FALSE);
		if($book->amzn_new_price) $this->db->set('amzn_new_price', $book->amzn_new_price, FALSE);
		if($book->amzn_list_price) $this->db->set('amzn_list_price', $book->amzn_list_price, FALSE);
		$this->db->set('amzn_updated_at', 'NOW()', FALSE);
		if($uptitle) $this->db->set('title', "$book->title");
		$this->db->set('amzn_link', "$book->amzn_link");
		$this->db->update('books');
	}
	public function login(){
		$my_result = array(
			'logged_in' => FALSE,
			'last_page' => $this->input->post('current_page'),
		);
		if ($this->input->post('login')) {
			$netid = $this->input->post('username');
			$my_result['logged_in'] = TRUE;
			$my_result['userdata'] = $this->get_user($netid);
			if ($my_result['userdata']) {
				$my_result['userdata'] = $my_result['userdata'][0];
			} else {
				$newuser = array(
					'netid' => $netid,
					'name' => 'John Smith',
					'email' => 'john@example.edu',
					'first_name' => 'John',
				);
				$this->add_user($newuser);
				$my_result['userdata'] = $this->get_user($netid);
				if ( ! $my_result['userdata']) {
					return FALSE;
				}
			}
		}
		return $my_result;
	}
	
}