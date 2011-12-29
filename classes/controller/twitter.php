<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Twitter extends Controller {
	
	public $access_token;
	
	public function before()
	{
		session_start();
	}
	
	public function check_for_login()
	{
		$this->access_token = isset($_SESSION['access_token']) ? $_SESSION['access_token'] : NULL;
		if (empty($this->access_token['oauth_token']) || empty($this->access_token['oauth_token_secret']))
		{
			$this->request->redirect('twitter/logout');
		}
		else
		{
			return new TwitterOAuth(
							Kohana::$config->load('twitteroauth.consumer_key'), Kohana::$config->load('twitteroauth.consumer_secret'),
							$this->access_token['oauth_token'], $this->access_token['oauth_token_secret']
						);
		}
	}
	
	public function action_registered()
	{
		if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token'])
		{
			$_SESSION['oauth_status'] = 'oldtoken';
			$this->request->redirect('twitter/logout');
		}
		$twitter = new TwitterOAuth(
							Kohana::$config->load('twitteroauth.consumer_key'), Kohana::$config->load('twitteroauth.consumer_secret'),
							$_SESSION['oauth_token'], $_SESSION['oauth_token_secret']
						);
		
		/* Save the access tokens. Normally these would be saved in a database for future use. */
		$_SESSION['access_token'] = $twitter->getAccessToken($_REQUEST['oauth_verifier']);
		
		/* Remove no longer needed request tokens */
		unset($_SESSION['oauth_token']);
		unset($_SESSION['oauth_token_secret']);
		
		/* If HTTP response is 200 continue otherwise send to connect page to retry */
		if ($twitter->http_code == 200)
		{
			/* The user has been verified and the access tokens can be saved for future use */
			$_SESSION['status'] = 'verified';
			$this->request->redirect('/');
		}
		else
		{
			/* Save HTTP status for error dialog on connnect page.*/
			$this->request->redirect('twitter/logout');
		}
	}
	
	public function action_login()
	{
		$twitter = new TwitterOAuth(Kohana::$config->load('twitteroauth.consumer_key'), Kohana::$config->load('twitteroauth.consumer_secret'));
		$request_token = $twitter->getRequestToken(Kohana::$config->load('twitteroauth.oauth_callback'));
		
		/* Save request token to session */
		$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
		
		/* If last connection fails don't display authorization link */
		if ($twitter->http_code == 200)
		{
			$this->request->redirect($twitter->getAuthorizeURL($token));
		}
		echo 'Could not connect to Twitter. Refresh the page or try again later.';
	}
	
	public function action_logout()
	{
		session_destroy();
		echo html::anchor('twitter/login', 'login');
	}
}
