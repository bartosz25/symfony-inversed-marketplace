<?php
namespace Security;

/**
 * Class which makes the CSRF protection token.
 */
class CsrfMaker
{
  /**
   * Key of session token value.
   * @access protected
   * @var string
   */
  protected $sessionKey;
  /**
   * Key of get token value.
   * @access protected
   * @var string
   */
  protected $getKey;
  /**
   * Key of post token value.
   * @access protected
   * @var string
   */
  protected $postKey;
  /**
   * Session storage instance.
   * @access protected
   * @var \Serializable
   */
  protected $storage;
  /**
   * Time limit of CSRF token.
   * By default 6 minutes.
   * @access protected
   * @var int
   */
  protected $timeLimit = 360;
  /**
   * After this time the class will replace token from session.
   * Even when the token generation is locked.
   * By default 10 minutes.
   * @access protected
   * @var int
   */
  protected $timeLockingLimit = 600;
  /**
   * Hashes used to make a CSRF token.
   * @access protected
   * @var array
   */
  protected $hashes = array();

  /**
   * Class constructor.
   * @access public
   * @param \Serializable $storage Session storage instance.
   * @param array $hashes Hashes used in CSRF token.
   * @param array $keys Keys for POST, GET and SESSION token representation.
   * @return void
   */
  public function __construct(\Serializable $storage, $hashes, $keys)
  {
    $this->storage = $storage;
    $this->hashes = $hashes;
    $this->sessionKey = $keys['session'];
    $this->getKey = $keys['get'];
    $this->postKey = $keys['post'];
  }

  /**
   * Initializes the CSRF protection. If the first test is true, we make a new token. Otherwise, we serve the old one.
   * We make the new token when : 
   * - $this->storage->get('ticketLocked') === false && !$this->isPostRequest() && !$this->isGetRequest() : the generation isn't locked, the request doesn't containt the token
   * - $this->isExpired() && $this->storage->get('ticketLocked') === false  && !$this->isPostRequest() && !$this->isGetRequest() : the token is expired and the generation isn't locked
   * - $this->isLockingExpired()  && !$this->isPostRequest() && !$this->isGetRequest() : the locking time is outdated (all requests will return the errors)
   * @access public
   * @return string The CSRF token from current session.
   */
  public function initCsrf()
  {
    if(($this->storage->get('ticketLocked') === false && !$this->isPostRequest() && !$this->isGetRequest()) || ($this->isExpired() && $this->storage->get('ticketLocked') === false && !$this->isPostRequest() && !$this->isGetRequest()) 
    || ($this->isLockingExpired() && !$this->isPostRequest() && !$this->isGetRequest()))
    {//echo 'make new CSRF protection';
      $this->storage->set($this->sessionKey, $this->makeCsrfToken());
      $this->storage->set($this->sessionKey.'_created', time());
    }
    return $this->storage->get($this->sessionKey);
  }

  /**
   * Checks if session token is expired. It will be changed only when the changing isn't blocked.
   * @access private
   * @return bool True if is expired, false otherwise.
   */
  private function isExpired()
  {
    return (bool)((time() - strtotime($this->storage->get($this->sessionKey.'_created'))) > $this->timeLimit);
  }

  /**
   * Checks if session token locking is expired (bigger than $timeLockingLimit).
   * @access private
   * @return bool True if expired, false otherwise.
   */
  private function isLockingExpired()
  {
    return (bool)((time() - (int)($this->storage->get($this->sessionKey.'_created'))) > $this->timeLockingLimit);
  }

  /**
   * Sets if we have to lock the token generation.
   * @access private
   * @param bool $locked True if the generation has to be locked, false otherwise.
   * @return void
   */
  public function setLocked($locked)
  {
    if($locked)
    {
      $this->storage->set('ticketLocked', true);
    }
    elseif((!$locked && $this->isLockingExpired()) || (!$locked && $this->storage->get('ticketLocked') === false))
    {
      $this->storage->set('ticketLocked', false);
    }
  }

  /**
   * Sets locked time.
   * @access public
   * @param int $time Time for lock the token generation.
   * @return void
   */
  public function setTimeLockingLimit($time)
  {
    $this->timeLockingLimit = $time;
  }

  /**
   * Sets the time for outdate the session token.
   * @access public
   * @param int $time Time which outdates the session token.
   * @return void
   */
  public function setTimeLimit($time)
  {
    $this->timeLimit = $time;
  }

  /**
   * Makes CSRF token.
   * @access private
   * @return string CSRF token.
   */
  private function makeCsrfToken()
  {
    return sha1($this->hashes['start'].time().$this->hashes['end'].rand(0,99999));
  }

  /**
   * Checks if we handle a POST request. If yes, we don't change the session token (prevent to invalidate the submit).
   * By default all POST request are protected.
   * Maybe todo : protect requests which contain $_POST[$postKey]
   * @access private
   * @return bool True if POST is handled, false otherwise.
   */
  private function isPostRequest()
  {
    return (bool)(count($_POST) > 0);
  }

  /**
   * Checks if we handle a GET request. If the GET request contains an $getKey, the response is true.
   * @access private
   * @return bool True when GET request is handled, false otherwise.
   */
  private function isGetRequest()
  {
    return (bool)(isset($_GET[$this->getKey])); 
  }

}