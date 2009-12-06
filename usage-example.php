<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Mytwitterapi extends Controller_Twitter {

/*	
	public function before()
	{
		parent::before();
		$this->request->headers = array('Content-type' => 'application/json');
	}
*/
	
	public function action_home_timeline($page = 0)
	{
		$twitter = $this->check_for_login();
		$twitter->decode_json = FALSE;
		
		$json = json_decode($twitter->get('statuses/home_timeline', array('count' => '10', 'page' => $page)), TRUE);
		
		$better_json = array();
		$json_count = count($json);
		for($i = 0; $i < $json_count; $i++)
		{
			$c = $json[$i];
			$better_json['results'][] = array(
				'id'					=> $c['id'],
				'from_user_id'			=> $c['user']['id'],
				'from_user'				=> $c['user']['screen_name'],
				'profile_image_url'		=> $c['user']['profile_image_url'],
				'created_at'			=> $c['created_at'],
				'to_user_id'			=> $c['in_reply_to_user_id'],
				'text'					=> $c['text'],
				'geo'					=> $c['geo'],
				'source'				=> $c['source'],
			);
		}

		echo json_encode($better_json);
	}
	
	public function action_user_timeline($page = 0)
	{
		$twitter = $this->check_for_login();
		
		print Kohana::debug($twitter->get('statuses/user_timeline', array('count' => '10', 'page' => $page)));
	}

	public function action_search($page = 1)
	{
		$twitter = $this->check_for_login();
		
		$q = Security::xss_clean(Arr::get($_GET, 'q', 'kittens'));
		print Kohana::debug($twitter->get('search', array('q' => $q, 'rpp' => '10', 'page' => $page)));
	}
	
	// get('account/verify_credentials');
	// get('users/show', array('screen_name' => 'obama'));
	// post('statuses/update', array('status' => 'I like #Kohana, #TwitterOAuth PHP lib and #turtles'));
	// post('statuses/destroy', array('id' => 5437877770));
	// post('friendships/create', array('screen_name' => 'obama'));
}