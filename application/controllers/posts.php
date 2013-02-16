<?php
class Posts extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		define('BASE','https://bookswap.northwestern.edu/dev/');
		//echo('controller start<br/>');
		$this->load->model('post_model');
		$this->load->model('account_model');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->library('session');
		date_default_timezone_set ( 'America/Chicago' );
		//echo('models loaded<br/>');

		if($this->session->userdata('bookswap_user')){
			//var_dump($this->session->userdata('bookswap_user'));
		}
		//echo('<br/>');
		$this->seller = $this->get_current_userdata();
		if($this->seller){
			$this->seller = $this->seller->netid;
		}
		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
	}

	public function index(){
		$data['title'] = 'Home';

		//$this->load->view('templates/header', $data);
		$this->load->view('header', $data);
		$this->load->view('index', $data);
		$this->load->view('footer', $data);
		//$this->load->view('templates/footer');
	}


	public function need_update($book){
		if($book->amzn_link==NULL){
			return true;
		}
		if($book->amzn_updated_at == NULL){
			return true;
		}
		$curr = getcwd();
		$imgFile = $curr."/img/book-covers/".$book->isbn.".jpg";
		if (!(file_exists($imgFile))){
			return true;
		}
		if(filesize($imgFile)==0){
			return true;
		}
		return false;
	}
	public function looking($q=""){
		$data['seller'] = $this->seller;
		$q = urldecode($q);
		if($this->input->post('q')!=NULL){
			header( 'Location: https://bookswap.northwestern.edu/dev/index.php/looking/'.$this->input->post('q') ) ;
		}
		$data['q'] = $q;
		$data['books'] = $this->results($q);
		$data['title'] = "Results";
		foreach ($data['books'] as $book){
			if($this->need_update($book)){
				$result = $this->post_model->get_amazon_from_isbn($book->isbn);
				if (property_exists($result,"Error")) break;
				if (($result!=NULL)&&($result->Items->Item!=NULL)&&($result->Items->Item->ItemAttributes!=NULL)&&($result->Items->Item->ItemAttributes->ListPrice!=NULL)){
					$url = $result->Items->Item->DetailPageURL;
					$listPrice = $result->Items->Item->ItemAttributes->ListPrice->FormattedPrice;
					$lowestNewPrice = $result->Items->Item->OfferSummary->LowestNewPrice->FormattedPrice;
					$lowestUsedPrice = $result->Items->Item->OfferSummary->LowestUsedPrice->FormattedPrice;
					$imgURL = $result->Items->Item->MediumImage->URL;
					$title = $result->Items->Item->ItemAttributes->Title;
					$curr = getcwd();
					$imgFile = $curr."/img/book-covers/".$book->isbn.".jpg";
					//echo filesize($imgFile);
					if ((!(file_exists($imgFile)))||(filesize($imgFile)==0)){
						ini_set('allow_url_fopen', 1);
						$ch = curl_init($imgURL);
						$fp = fopen($imgFile, 'w');
						curl_setopt($ch, CURLOPT_FILE, $fp);
						curl_setopt($ch, CURLOPT_HEADER, 0);
						curl_exec($ch);
						curl_close($ch);
						fclose($fp);
						$this->post_model->have_cover($book->isbn);
					}
					$book->amzn_link="$url";
					if (!($lowestUsedPrice==NULL)){
						$book->amzn_used_price=substr($lowestUsedPrice,1);
					}else{
						//echo("has no used price");
						//var_dump($book->isbn);
						//var_dump($result);
						// object(SimpleXMLElement)#97 (2) { ["Error"]=> object(SimpleXMLElement)#105 (2) { ["Code"]=> string(16) "RequestThrottled" ["Message"]=> string(41) "Request from A1X3VU29WDDDXL is throttled." } ["RequestId"]=> string(36) "bbf4e67f-e591-4859-809f-2863eb18e0c9" } 
					}
					if (!($lowestNewPrice==NULL)){
						$book->amzn_new_price=substr($lowestNewPrice,1);
					}else{
						//echo "has no new price";
					}
					if (!($listPrice==NULL)){
						$book->amzn_list_price=substr($listPrice,1);
					}else{
						//echo "has no list price";
					}
					if(!($title==NULL)){
						$book->title=$title;
						$this->post_model->update_amazon_data($book,true);
					}
					$this->post_model->update_amazon_data($book,false);
				}else{
					//echo($book->isbn);
					//var_dump($result);
				}
			}
		}
		foreach ($data['books'] as $book){
			if($book->stock>=1){
				$book->posts=$this->post_model->get_posts_by_bid($book->id);
				//var_dump($book->posts);
				foreach ($book->posts as $post){
					$post->sellerdata = $this->account_model->get_user($post->seller);
				}
				
				$book->from=$this->min($book->id);
			}else{
				$book->posts=array();
			}

		}
		//var_dump($data['books']);
		$this->load->view('header', $data);
		$this->load->view('looking',$data);
		$this->load->view('footer',$data);
	}


	public function my_posts(){
		$user = $this->get_current_userdata();
		if($user){
			$data['user'] = $user;
			$data['posts'] = $this->post_model->get_posts_by_seller($user->netid);
			foreach ($data['posts'] as $post){
				$post->book = $this->post_model->get_books_by_id($post->bid);
				$post->book = $post->book[0];
			}
		}
		$data['title']='My Posts';
		$this->load->view('header', $data);
		$this->load->view('my_posts',$data);
		$this->load->view('footer',$data);
	}
	public function post_book(){
		$seller = $this->get_current_userdata();
		if($seller){
			$seller = $seller->netid;
		}
		$data['title'] = 'Posted';
		$data['notice'] = "Succesfully posted your book!";
		$this->post_model->add_post($seller);
		$this->load->view('header', $data);
		$this->load->view('notice',$data);
		$this->load->view('footer',$data);
	}
	public function update_post(){

		$data['title'] = 'Updated';
		$this->post_model->update_post();
		$this->load->view('header', $data);
		$this->load->view('footer',$data);
	}
	public function remove_post(){

		$data['title'] = 'Updated';
		echo $this->post_model->remove_post();
	}
	public function min($q){
		return $this->post_model->get_min_price($q);
	}
	public function isbn($q){
		return $this->post_model->get_books_by_isbn($q);		
	}
	public function bid($q){
		return $this->post_model->get_books_by_id($q);		
	}
	public function results($q){
		if(intval($q)>999){
			$results = $this->post_model->get_books_by_isbn($q);	
		}else{
			$results = $this->post_model->get_books_by_all($q);
		}

		if($q==NULL){
			$results = $this->post_model->get_books();
		}
   		return $results;
	}
	public function login(){
		$result=$this->post_model->login();
		$this->session->set_userdata('last_page',$result['last_page']);
		var_dump($result);
		//save last page, just add userdata to session and go back to last page. If failed, go to failure page and keep last page saved for when they succeed or hit back.
		if($result['logged_in']){
			$this->session->set_userdata('bookswap_user', $result['userdata']);
		}else{
			$this->session->set_flashdata('headernotice',"Invalid username/password");
		}
		redirect($result['last_page']);
	}
	public function logout(){
		$this->session->sess_destroy();
		redirect(BASE.'index.php');
	}
	public function my_account(){
		if($this->session->userdata('bookswap_user')){
			$data['userdata'] = $this->session->userdata('bookswap_user');
			$data['title'] = 'Account Settings';
			$this->load->view('header', $data);
			$this->load->view('my_account',$data);
			$this->load->view('footer', $data);
		}else{
			echo('please log in');
		}
	}
	public function update_account(){
		if($this->account_model->update_user($this->session->userdata('bookswap_user'))){
			
		}else{
		}
		$netid=$this->session->userdata('bookswap_user');
		$netid=$netid['netid'];
		$data['userdata'] = $this->post_model->get_user($netid);
		$this->load->view('header', $data);
		$this->load->view('my_account',$data);
		$this->load->view('footer', $data);
	}
	public function get_current_userdata(){
		$user = $this->session->userdata('bookswap_user');
		if(is_array($user)){
			return $user[0];
		}else{
			return $user;
		}
	}
}