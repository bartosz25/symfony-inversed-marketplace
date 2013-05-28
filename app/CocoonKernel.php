<?php
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;

class CocoonKernel extends Kernel
{

    private $mode = '';

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

        if ($this->mode == 'dev') {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }
	
        // $bundles[] = new Ad\ItemsBundle\AdItemsBundle();
        // $bundles[] = new Ad\TagsBundle\AdTagsBundle();
        // $bundles[] = new Ad\QuestionsBundle\AdQuestionsBundle();
        // $bundles[] = new Ad\OpinionsBundle\AdOpinionsBundle();
        // $bundles[] = new Catalogue\OffersBundle\CatalogueOffersBundle();
        // $bundles[] = new Catalogue\ImagesBundle\CatalogueImagesBundle();
        // $bundles[] = new Catalogue\ImagesBundle\CatalogueImagesBundle();
        // $bundles[] = new Catalogue\TagsBundle\CatalogueTagsBundle();
        $bundles[] = new Category\CategoriesBundle\CategoryCategoriesBundle();
        // $bundles[] = new Geography\RegionsBundle\GeographyRegionsBundle();
        // $bundles[] = new Geography\CitiesBundle\GeographyCitiesBundle();
        // $bundles[] = new Geography\CountriesBundle\GeographyCountriesBundle();
        // $bundles[] = new Geography\ZonesBundle\GeographyZonesBundle();
        $bundles[] = new Message\MessagesBundle\MessageMessagesBundle();
        // $bundles[] = new Order\HistoriesBundle\OrderHistoriesBundle();
        $bundles[] = new User\ProfilesBundle\UserProfilesBundle();
        $bundles[] = new User\AlertsBundle\UserAlertsBundle();
        // $bundles[] = new User\OpinionsBundle\UserOpinionsBundle();
        // $bundles[] = new User\CodesBundle\UserCodesBundle();
        // $bundles[] = new User\FriendsBundle\UserFriendsBundle();
        // $bundles[] = new User\AddressesBundle\UserAddressesBundle();


        $bundles[] = new User\GodsonsBundle\UserGodsonsBundle();
        $bundles[] = new Order\OrdersBundle\OrderOrdersBundle();
        $bundles[] = new Bid\BidsBundle\BidBidsBundle();
        $bundles[] = new Bid\ItemsBundle\BidItemsBundle();
        $bundles[] = new Frontend\FrontBundle\FrontendFrontBundle();
        $bundles[] = new Cocoon\BackendBundle\CocoonBackendBundle();
        $bundles[] = new Content\PagesBundle\ContentPagesBundle();
        $bundles[] = new Cron\TasksBundle\CronTasksBundle();

          // define('rootDir' , str_replace("web/", "", $_SERVER['DOCUMENT_ROOT']));
          define('rootDir' , str_replace(array("www", "web/"), array("", ""), $_SERVER['DOCUMENT_ROOT'])."/"); 
        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
    }

}