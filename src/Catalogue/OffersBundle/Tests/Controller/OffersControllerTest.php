<?php
namespace Ad\ItemsBundle\Tests\Controller;

use Ad\ItemsBundle\Controller\ItemsController;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Order\OrdersBundle\Entity\Tax;

class OffersControllerTest extends WebTestCase
{

  /**
   * Values to test.
   * @access private
   * @var array
   */
  private $addOffer = array('AddOffer' => array(
    'offerName' => "Test offer", 'offerText' => "Test offer description", 'offerCatalogue' => 2, 
    'offerCategory' => 1, 'offerCountry' => 1, 'offerCity' => 9, 'offerObjetState' => 0,
    'offerPrice' => 15.00, 'offerTax' => 1, 'deliveryYN' => 0, 
    'tag1' => 'house', 'tag2' => 'house selling', 'tag3' => '', 'tag4' => '', 'tag5' => '', 'tag6' => '',
    'tag7' => '', 'tag8' => '', 'tag9' => '', 'tag10' => '', 'siteweb' => 'ab', 
    'technology' => 'ba', 'offerAd' => 0)
  );

  /**
   * Stat values after ad activating.
   * @access private
   * @var array
   */
  private $stats = array('regionOffers' => 1, 'cityOffers' => 1, 'categoryOffers' => 1, 'catalogueOffers' => 1, 'offa' => 1);

  /**
   * Private var with added id.
   * @access private
   * @var int
   */
  private $offerId = 15;

  /**
   * Tests add an offer action.
   * @return Displayed template.
   */
  public function testAddOffer()
  {
    $client = static::createClient(array(
      'environment' => 'test',
    ));
    $client->followRedirects(false);
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $entityManager = $client->getContainer()->get('doctrine')->getEntityManager();
    $cacheManager = new \Database\Doctrine\FileCache();
    $dir = dirname(__FILE__);
    $cacheManager->setBaseDir($dir.'/../../../../../cache/');
    $entityManager->getConfiguration()->setResultCacheImpl($cacheManager);
    // Add new offer to France > Corse > Ajaccio, SEO/SEM category, Test catalogue, object state : little of importance 
    // 19,6% TAX, price 15.00 €
    $crawler = $client->request('POST', 'mon_compte/catalouge/ajouter_offre', $this->addOffer);
// DEBUG MODE file_put_contents($_SERVER['DOCUMENT_ROOT'].'response_test.txt', $client->getResponse());
    $this->assertContains('added_successfully', $client->getResponse()->getContent());
  }

  /**
   * Tests the database after delete of one offer.
   * @return Displayed template.
   */
  public function testDeleteOffer()
  {
    $client = static::createClient(array(
      'environment' => 'test',
    ));
    $client->followRedirects(false);
    $entityManager = $client->getContainer()->get('doctrine')->getEntityManager();
    $cacheManager = new \Database\Doctrine\FileCache();
    $cacheManager->setBaseDir(rootDir.'cache/');
    $cacheManager->setCacheStructure(array(
      'users' => 'users/', 'catalogues' => 'catalogues/', 'tags' => 'tags/', 'ads' => 'ads/', 'categories' => 'categories/',
      'cities' => 'cities/', 'regions' => 'regions/', 'offers' => 'offers/'
    ));
    $entityManager->getConfiguration()->setResultCacheImpl($cacheManager);
    $entityManager->getConfiguration()->addCustomDatetimeFunction('DATE_FORMAT', 'Database\Doctrine\DateFormat');

    $row = $entityManager->getRepository('CatalogueOffersBundle:Offers')->getOneOffer($this->offerId);
    $offerTags = $entityManager->getRepository('CatalogueOffersBundle:OffersTags')->getTagsByOffer($this->offerId);
    $entityManager->getRepository('CatalogueOffersBundle:Offers')->deleteOffer($this->offerId, $row, array('title' => "deleted", 'offersDir' => '',
    'mailer' => $client->get('mailer'), 'from' => 'test@migapi.com'));
    // prepare new stats
    foreach($this->stats as $s => $stat)
    {
      $this->stats[$s] = $stat - 1;
    }
    $offa = $entityManager->getRepository('FrontendFrontBundle:Stats')->getStats('offa');
    $tags = $entityManager->getRepository('FrontendFrontBundle:Tags')->getTagsInId(array((int)$offerTags[0]['id_ta'], (int)$offerTags[1]['id_ta']));
    $tagStats = array();
    foreach($tags as $t => $tag)
    {
      $tagStats[] = (bool)($tag['tagOffers'] == 0);
    }
    // get category, region and city stats separately
    $catStats = $entityManager->getRepository('CategoryCategoriesBundle:Categories')->getById($row['id_ca']);
    $geoStats = $entityManager->getRepository('GeographyCitiesBundle:Cities')->getCityWithRegion($row['id_ci']);
// DEBUG MODE
// echo $offa."==".$this->stats['offa']."&&".
// $geoStats['regionOffers']."==".$this->stats['regionOffers']."&&".$geoStats['cityOffers']."==".$this->stats['cityOffers']."&&".
// $catStats['categoryOffers']."==".$this->stats['categoryOffers']."&&".$tagStats[0]."&&".$tagStats[1];die();
    $this->assertTrue((bool)($offa == $this->stats['offa'] &&
        $geoStats['regionOffers'] == $this->stats['regionOffers'] && $geoStats['cityOffers'] == $this->stats['cityOffers'] &&
        $catStats['categoryOffers'] == $this->stats['categoryOffers'] && $tagStats[0] && $tagStats[1]
      )
    );
  }

  /**
   * Tests offer propose to ad.
   * @access public
   * @return void
   */
  public function testProposeOffer()
  {
    $client = static::createClient(array(
      'environment' => 'test',
    ));   
    $client->followRedirects(false); 
    // propose the first offer to the first ad
    $crawler = $client->request('GET', 'mon_compte/offres/proposer/envoyer/1/1');
// DEBUG MODE file_put_contents($_SERVER['DOCUMENT_ROOT'].'response_test.txt', $client->getResponse());
    $this->assertContains('added_successfully', $client->getResponse()->getContent());
  }

  /**
   * Tests proposition to buy an offer.
   * @access public
   * @return void
   */
  public function testProposeBuyOffer()
  {
    $client = static::createClient(array(
      'environment' => 'test',
    ));   
    $client->followRedirects(false); 
    // propose the first offer to the first ad
    $crawler = $client->request('POST', 'mon_compte/offres/acheter/2/0', array('adChoosen' => 1));
// DEBUG MODE file_put_contents($_SERVER['DOCUMENT_ROOT'].'response_test.txt', $client->getResponse());
    $this->assertContains('proposed_successfully', $client->getResponse()->getContent());
  }

  /**
   * Tests the database after delete of one catalogue. This catalogue has an offer which was choosen as offers_id_of
   * value. The ad is active.
   * @return Displayed template.
   */
  public function testDeleteOfferAdActive()
  {
    $client = static::createClient(array(
      'environment' => 'test',
    ));
    $client->followRedirects(false);
    $entityManager = $client->getContainer()->get('doctrine')->getEntityManager();
    $crawler = $client->request('GET', 'mon_compte/supprimer/offres/13?ticket=X', array());
// DEBUG MODE file_put_contents($_SERVER['DOCUMENT_ROOT'].'response_test.txt', $client->getResponse());
    // If offer was deleted from active ad, the offers_id_of has to be 0
    $ad = $entityManager->getRepository('AdItemsBundle:Ads')->getOneAd(13, false);
    $this->assertTrue((bool)($ad['adOffer'] == 0));
  }


  /**
   * Tests the database after delete of one catalogue. This catalogue has an offer which was choosen as offers_id_of
   * value. The ad isn't active.
   * @return Displayed template.
   */
  public function testDeleteOfferAdNotActive()
  {
    $client = static::createClient(array(
      'environment' => 'test',
    ));
    $client->followRedirects(false);
    $entityManager = $client->getContainer()->get('doctrine')->getEntityManager();
    $crawler = $client->request('GET', 'mon_compte/supprimer/offres/12?ticket=X', array());
// DEBUG MODE file_put_contents($_SERVER['DOCUMENT_ROOT'].'response_test.txt', $client->getResponse());
    // Offer deleted from ended ad : in this case, we keep the offers_id_of value
    // We replace the offer name and set his state to deleted
    $ad = $entityManager->getRepository('AdItemsBundle:Ads')->getOneAd(12, false);
    $this->assertTrue((bool)($ad['adOffer'] != 0));
  }

}