<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Security\FilterXss;

class AppKernel extends Kernel
{
    /**
     * Aliases of form used in this application. 
     * The aliases help to detect if handle() method has to filter
     * data or not. After, this data is passed to Form->bindRequest($request).
     * @access protected
     * @type array
     */
    protected $_forms = array("AddAd", "Write", "Reply", "AddImage", "AddCatalogue", "AddOffer",
    "SynchronizePrestashopFirst", "EditTag", "OrderDelivery", "OrderFirstStep", "OrderFormState", "OrderNextStep",
    "AddAddress", "FirstStep", "EditCard", "EditEbayLogin", "EditEmail", "EditPassword", "EditUser", "Forgotten", 
    "ForgottenNew", "Register");
//  find . -regex '.*/Form.*' pour voir la liste des formulaires

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\DoctrineBundle\DoctrineBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
        );

        $bundles[] = new Ad\ItemsBundle\AdItemsBundle();
        // $bundles[] = new Ad\TagsBundle\AdTagsBundle();
        $bundles[] = new Ad\QuestionsBundle\AdQuestionsBundle();
        $bundles[] = new Ad\OpinionsBundle\AdOpinionsBundle();
        $bundles[] = new Catalogue\OffersBundle\CatalogueOffersBundle();
        $bundles[] = new Catalogue\ImagesBundle\CatalogueImagesBundle();
        // $bundles[] = new Catalogue\TagsBundle\CatalogueTagsBundle();
        $bundles[] = new Category\CategoriesBundle\CategoryCategoriesBundle();
        $bundles[] = new Cron\TasksBundle\CronTasksBundle();
        $bundles[] = new Geography\RegionsBundle\GeographyRegionsBundle();
        $bundles[] = new Geography\CitiesBundle\GeographyCitiesBundle();
        $bundles[] = new Geography\CountriesBundle\GeographyCountriesBundle();
        $bundles[] = new Geography\ZonesBundle\GeographyZonesBundle();
        $bundles[] = new Message\MessagesBundle\MessageMessagesBundle();
        $bundles[] = new Order\OrdersBundle\OrderOrdersBundle();
        // $bundles[] = new Order\HistoriesBundle\OrderHistoriesBundle();
        $bundles[] = new User\ProfilesBundle\UserProfilesBundle();
        $bundles[] = new User\AlertsBundle\UserAlertsBundle();
        // $bundles[] = new User\OpinionsBundle\UserOpinionsBundle();
        // $bundles[] = new User\CodesBundle\UserCodesBundle();
        $bundles[] = new User\FriendsBundle\UserFriendsBundle();
        $bundles[] = new User\AddressesBundle\UserAddressesBundle();
        $bundles[] = new Frontend\FrontBundle\FrontendFrontBundle();

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }
        if(!defined('rootDir'))
        {
          define('rootDir' , str_replace("web/", "", $_SERVER['DOCUMENT_ROOT']));
        }
        date_default_timezone_set('Europe/Paris');
        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
// TODO : test it
      $filter = new FilterXss(FilterXss::STRICT_MODE, array());
      foreach($this->_forms as $formKey)
      {  
        $formParts = $request->request->all($formKey);
        if(isset($formParts[$formKey]) && count($formParts[$formKey]) > 0)
        {
          $filtered = $filter->doFilterXss($formParts);
          $request->request->set($formKey, $filtered[$formKey]);
          break;
        }
      }
      return parent::handle($request, $type, $catch);
    }
 

}