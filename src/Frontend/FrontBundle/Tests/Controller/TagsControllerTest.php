<?php
namespace Frontend\FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TagssControllerTest extends WebTestCase
{ 

  /**
   * Tests register action result.
   * @return Displayed template.
   */
  public function testEscapingTags()
  {
    $client = static::createClient(array(
      'environment' => 'test',
    ));
    $entityManager = $client->getContainer()->get('doctrine')->getEntityManager();
    $modelQuery = "('ad', 'and\'y', 'and\\\"y', 'and`y', 'and\\\`y', '\'\'\'\'')";
    $tagsQuery = explode("IN", $entityManager->getRepository('FrontendFrontBundle:Tags')->testGetTagsIn(array("ad", "and'y", 'and"y', 'and`y', "and\`y", "''''")));
// DEBUG MODE file_put_contents($_SERVER['DOCUMENT_ROOT'].'response_test.txt', trim($tagsQuery[1])."==".$modelQuery);
    $this->assertTrue((bool)(trim($tagsQuery[1]) == $modelQuery));
  }

}