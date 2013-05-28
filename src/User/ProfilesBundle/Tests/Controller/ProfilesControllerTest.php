<?php
namespace User\ProfilesBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfilesControllerTest extends WebTestCase
{

  /**
   * Values to test.
   * @access private
   * @var array
   */
  private $userData = array('Register' => array(
    'login' => "andrew", 'pass1' => "andrew", 'pass2' => "andrew", 
    'email' => "bartosz_test@migapi.com")
  );

  /**
   * Tests register action result.
   * @return Displayed template.
   */
  public function testRegisterUser()
  {
    $client = static::createClient(array(
      'environment' => 'test',
    ));
    $client->followRedirects(false);
    $crawler = $client->request('POST', 'enregistrer', $this->userData);
// DEBUG MODE 
file_put_contents($_SERVER['DOCUMENT_ROOT'].'response_test.txt', $client->getResponse());
    $this->assertContains('registered_successfully', $client->getResponse()->getContent());
  }

}