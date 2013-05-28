<?php
namespace Ad\ItemsBundle\Tests\Controller;

use Ad\ItemsBundle\Controller\ItemsController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Order\OrdersBundle\Entity\Tax;

class ItemsControllerTest extends WebTestCase
{

  /**
   * Values to test.
   * @access private
   * @var array
   */
  private $addAd = array('AddAd' => array(
    'adName' => "Test ad", 'adText' => "Test ad description", 'adCategory' => 4, 
    'adCountry' => 1, 'adCity' => 9, 'adMinOpinion' => 0, 'adObjetState' => 0,
    'adSellerType' => 0, 'adSellerGeo' => 0, 'adPayments' => array(1 => 1), 'adTax' => 1,
    'adValidity' => 1, 'adAtHomePage' => 2, 'adBuyTo' => 20.00,
    'tag1' => 'car', 'tag2' => 'car selling', 'tag3' => '', 'tag4' => '', 'tag5' => '', 'tag6' => '',
    'tag7' => '', 'tag8' => '', 'tag9' => '', 'tag10' => '', 'siteweb' => 'ab', 
    'technology' => 'ba')
  );

  /**
   * Stat values after ad activating.
   * @access private
   * @var array
   */
  private $stats = array('regionAds' => 1, 'cityAds' => 1, 'categoryAds' => 1, 'adsa' => 1, 'adsn' => 0);

  /**
   * Private var with added id.
   * @access private
   * @var int
   */
  private $adId = 15;

  /**
   * Tests add an ad item action.
   * @return Displayed template.
   */
  public function testAddAd()
  {
    $client = static::createClient(array(
      'environment' => 'test',
    ));
    $client->followRedirects(false);
    $entityManager = $client->getContainer()->get('doctrine')->getEntityManager();
    // Add new ad to France > Corse > Ajaccio, SEO/SEM category, no minimal opinion, object state : little of importance 
    // seller type not precised (both), seller localization from everywhere, all payment methods accepted,  19,6% TAX,
    // ad validity 1 week, ad not shown at the home page, offer price between 10 and 20 €
    $crawler = $client->request('POST', 'mon_compte/annonces/ajouter', $this->addAd);
    $entityManager->getRepository('AdItemsBundle:Ads')->acceptAd($this->adId, array(
      'city' => $this->addAd['AddAd']['adCity'], 'category' => $this->addAd['AddAd']['adCategory']
    ));
// DEBUG MODE file_put_contents($_SERVER['DOCUMENT_ROOT'].'response_test.txt', $client->getResponse());
    $this->assertContains('added_successfully', $client->getResponse()->getContent());
  }

  /**
   * Tests if ad was correctly inserted (data not deformed etc.).
   * @return Displayed template.
   */
  public function testInsertAd()
  {
    $client = static::createClient(array(
      'environment' => 'test',
    ));
    $client->followRedirects(false);
    $entityManager = $client->getContainer()->get('doctrine')->getEntityManager();
    // for the empty database, inserted ad has to have the id number 1
    $row = $entityManager->getRepository('AdItemsBundle:Ads')->getOneAd($this->adId, false);
    // now compare the database values with inserted ones
    $areTheSame = false;
    $badField = '';
    if(isset($row['id_ad']))
    {
      // get form fields values too
      $formFields = $entityManager->getRepository('AdItemsBundle:AdsFormFields')->getFieldsByAd($this->adId, $row['id_ca']);
      foreach($formFields as $f => $field)
      {
        $row[$field['codeName']] = $field['fieldValue'];
      }
      $tags = $entityManager->getRepository('AdItemsBundle:AdsTags')->getTagsByAd($this->adId);
      $restTags = 0;
      foreach($tags as $t => $tag)
      {
        $i = $t + 1;
        $row['tag'.$i] = $tag['tagName'];
        $restTags++;
      }
      for($i = ($restTags+1); $i < 11; $i++)
      {
        $row['tag'.$i] = '';
      }
      $payments = $entityManager->getRepository('AdItemsBundle:AdsPayments')->getPaymentsByAd($this->adId);
      foreach($payments as $p => $payment)
      {
        $row['adPayments'][(int)$payment['payments_id_pa']] = (int)$payment['payments_id_pa'];
      }
      $row['adCountry'] = (int)$row['id_co'];
      $row['adCity'] = (int)$row['id_ci'];
      $row['adCategory'] = (int)$row['id_ca'];
      $row['adTax'] = Tax::getTaxByValue((float)$row['adTax']);
// TODO : validate adValidity too
unset($this->addAd['AddAd']['adValidity']);
unset($this->addAd['AddAd']['adAtHomePage']);
      $areTheSame = true;
      foreach($this->addAd['AddAd'] as $k => $value)
      {
        if($value != $row[$k])
        {
// DEBUG MODE echo $value.'-------'.$row[$k].'------'.$k;
          $areTheSame = false;
          $badField = $k;
          break;
        }
      }
    }
    $this->assertTrue($areTheSame, 'Bad filled field value for field '.$badField);
  }


  /**
   * Tests if stats are corrects after the ad activation.
   * @return Displayed template.
   */
  public function testActivateAd()
  {
    $client = static::createClient(array(
      'environment' => 'test',
    ));
    // $client->followRedirects(false);
    $entityManager = $client->getContainer()->get('doctrine')->getEntityManager();
    // $entityManager->getRepository('AdItemsBundle:Ads')->acceptAd($this->adId, array(
      // 'city' => $this->addAd['AddAd']['adCity'], 'category' => $this->addAd['AddAd']['adCategory']
    // ));
    $adsa = $entityManager->getRepository('FrontendFrontBundle:Stats')->getStats('adsa');
    $adsn = $entityManager->getRepository('FrontendFrontBundle:Stats')->getStats('adsn');
    $row = $entityManager->getRepository('AdItemsBundle:Ads')->getOneAd($this->adId, false);
    $tags = $entityManager->getRepository('AdItemsBundle:AdsTags')->getTagsByAd($this->adId);
    $tagStats = array();
    foreach($tags as $t => $tag)
    {
      $tagStats[] = (bool)($tag['tagAds'] == 1);
    }
// echo $adsa."==".$this->stats['adsa']."&&".$adsn."==".$this->stats['adsn']."&&".
// $row['regionAds']."==".$this->stats['regionAds']."&&".$row['cityAds']."==".$this->stats['cityAds']."&&".
// $row['categoryAds']."==".$this->stats['categoryAds']."&&".(int)$tagStats[0]."&&".(int)$tagStats[1];die();
    $this->assertTrue((bool)($adsa == $this->stats['adsa'] && $adsn == $this->stats['adsn'] && 
        $row['regionAds'] == $this->stats['regionAds'] && $row['cityAds'] == $this->stats['cityAds'] &&
        $row['categoryAds'] == $this->stats['categoryAds'] && $tagStats[0] && $tagStats[1]
      )
    );
  }


  /**
   * Tests the database after delete of one ad.
   * @return Displayed template.
   */
  public function testDeleteAd()
  {
    $client = static::createClient(array(
      'environment' => 'test',
    ));
    $client->followRedirects(false);
    $entityManager = $client->getContainer()->get('doctrine')->getEntityManager();
    $cacheManager = new \Database\Doctrine\FileCache();
    $cacheManager->setBaseDir(rootDir.'cache/');
    $cacheManager->setCacheStructure(array(
      'users' => 'users/', 'catalogues/' => 'catalogues/', 'tags' => 'tags/', 'ads' => 'ads/', 'categories' => 'categories/',
      'cities' => 'cities/', 'regions' => 'regions/', 'offers' => 'offers/'
    ));
    $entityManager->getConfiguration()->setResultCacheImpl($cacheManager);
    $entityManager->getConfiguration()->addCustomDatetimeFunction('DATE_FORMAT', 'Database\Doctrine\DateFormat');
    $row = $entityManager->getRepository('AdItemsBundle:Ads')->getOneAd($this->adId, false);
    $entityManager->getRepository('AdItemsBundle:Ads')->deleteAd($this->adId, $row, array('title' => "deleted", 'cacheName' => 'test', 'dateFormat' => '%d'));
    // prepare new stats
    $stats = $this->stats;
    unset($stats['adsn']);
    foreach($stats as $s => $stat)
    {
      $stats[$s] = $stat - 1;
    }
    $adsa = $entityManager->getRepository('FrontendFrontBundle:Stats')->getStats('adsa');
    $adTags = $entityManager->getRepository('AdItemsBundle:AdsTags')->getTagsByAd($this->adId);
    $tags = $entityManager->getRepository('FrontendFrontBundle:Tags')->getTagsInId(array((int)$adTags[0]['id_ta'], (int)$adTags[1]['id_ta']));
    $tagStats = array();
    foreach($tags as $t => $tag)
    {
      $tagStats[] = (bool)($tag['tagAds'] == 0);
    }
    // get category, region and city stats separately
    $catStats = $entityManager->getRepository('CategoryCategoriesBundle:Categories')->getById($row['id_ca']);
    $geoStats = $entityManager->getRepository('GeographyCitiesBundle:Cities')->getCityWithRegion($row['id_ci']);
    $this->assertTrue((bool)($adsa == $stats['adsa'] &&
        $geoStats['regionAds'] == $stats['regionAds'] && $geoStats['cityAds'] == $stats['cityAds'] &&
        $catStats['categoryAds'] == $stats['categoryAds'] && $tagStats[0] && $tagStats[1]
      )
    );
  }

  /**
   * Tests accept an offer.
   * @return Displayed template.
   */
  public function testConfirmOfferPropositionAd()
  {
    $client = static::createClient(array(
      'environment' => 'test',
    ));
    $client->followRedirects(false);
    $entityManager = $client->getContainer()->get('doctrine')->getEntityManager();
    $crawler = $client->request('GET', '/mon_compte/offres/propositions/accepter/1_1');
// DEBUG MODE file_put_contents($_SERVER['DOCUMENT_ROOT'].'response_test.txt', $client->getResponse());
    $this->assertContains('accepted_successfully', $client->getResponse()->getContent());
  }


  /**
   * Tests the database after ended ad.
   * @return Displayed template.
   */
  public function testDeleteAdEnded()
  {
    $client = static::createClient(array(
      'environment' => 'test',
    ));
    $client->followRedirects(false);
    $crawler = $client->request('GET', 'mon_compte/annonces/supprimer/14?ticket=X', array());
    $this->assertContains('ad_not_deleted', $client->getResponse()->getContent());
  }

}