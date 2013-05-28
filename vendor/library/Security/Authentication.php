<?php
namespace Security;

class Authentication 
{

  public function __construct($fingerHash = array('start' => 'x', 'end' => 'y'), $user, $flash = 0)  
  {
	$this->fingerprinting = sha1($fingerHash['start'].$_SERVER['HTTP_USER_AGENT']."".$_SERVER['SERVER_ADDR']."".$_SERVER['SERVER_PROTOCOL']."zjablkiem".$_SERVER['HTTP_ACCEPT_ENCODING'].$fingerHash['end']);
    $this->flash = $flash;
	// $this->sessionName = $sessionName; 
	// $this->salt = $salt;
	// $this->storage = $storage;
	$this->user = $user;
	// parent::initialize($dispatcher, $storage, array()); 
  }

  /**
   * Functions used to authenticate an user. 
   * @access public
   * @param array $data List composed by two other arrays : DB (data from database) and FORM (data from form).
   * @return boolean True if the user is logged correctly, otherwise false.
   */
  public function connect($data)
  {
    return (bool)($data['DB']['login'] == $data['FORM']['login'] && $data['DB']['password'] == $data['FORM']['password']);
  }

  public function checkCredentials($credentials, $table)
  {
    $usDb = new $table;
	$this->usRow = $usDb->loginUser($credentials);
	if((int)$this->usRow['id'] > 0)
	{
	  $this->startSession();  
	  $usDb->updateConnection($this->usRow['id']);
	  return true;
	}
	$this->user->setAuthenticated(false);
	return false;
  }

  public function checkCredentialsStandard($user, $table) 
  { //echo $user['pass'].'<br />'.sha1($this->salt);
    $usDb = new $table;
    if($user['pass'] == sha1($this->salt)) 
    {
      $this->usRow = $user;
      $this->startSession(); 
      $usDb->updateConnection($user['id']);  
      return true; //echo 'ok';die();
    }
	$this->user->setAuthenticated(false);
	return false;
  }

  private function startSession() 
  {   
    // TODO: voir si l'on stocke le mot de passe dans la session ? 
	$this->user->setAttribute('pass', $this->saltPassword(), $this->sessionName);
	unset($this->usRow['pass']);
	foreach($this->usRow as $key => $value)
	{
	  $this->user->setAttribute($key, $value, $this->sessionName); 
	}
	$this->user->setAttribute('signature', $this->fingerprinting, $this->sessionName);
	$this->user->setAuthenticated(true);  
  }

  public function compareSession($strict=false, $table, $regenerate=false)
  {
	// TODO: adapt this function for jQuery Uploadify
	// fingerprinting compare
	if(!$this->compareFingerprinting())
	{   
	  return false;
	}
	// id regeneration
	if($regenerate == true)
	{  
	  $this->regenerateId();
	}
	else 
	{  
	  if(time()%2 == 0)
	  {  
	    $this->regenerateId();
	  }
	}
	// database data checking
	if($strict == true)
	{  
	  $usDb = new $table;
	  $this->usRow = $usDb->compareLoggedUser(array('login' => $this->user->getAttribute('login'), 
	  'pass' => $this->user->getAttribute('pass')));
	  if((int)$this->usRow['id'] > 0) 
	  { 
	    // check passwords
		if($this->user->getAttribute('pass') != $this->usRow['pass'])
		{
		  return false;
		}
		return true;
	  }
	  else 
	  {  
	    return false;
	  }
	} 
  }

  public function destroySession()
  {
    $this->user->getAttributeHolder()->removeNamespace($this->sessionName);
    $this->user->setAuthenticated(false);
    $this->user = null;  
  }

  public function regenerateId()
  {
    // TODO: today 
	// TODO: voir comment cela réagit si l'on a une session ouverte dans le BO et dans le FO
	$this->storage->regenerate();
	session_regenerate_id(); 
  }  

  private function compareFingerprinting()
  {  
	if($this->fingerprinting == $this->user->getAttribute('signature'))
	{  
	  return true;
	}
	return false;
  }

}