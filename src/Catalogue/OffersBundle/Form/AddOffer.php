<?php
namespace Catalogue\OffersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Ad\ItemsBundle\Entity\Ads;
use Order\OrdersBundle\Entity\Tax;
use Others\FormTestListener;

/**
 * Form used to add a new offer.
 */
class AddOffer extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $adsEnt = new Ads;
    $adsEnt->setCategoriesList($options['data']->categoriesList);
    $builder->add('offerName', 'text', array('required' => true, 'trim' => true, 'max_length' => 200));
    $builder->add('offerText', 'textarea', array('required' => true, 'trim' => true));
    $builder->add('offerCategory', 'choice', array('required' => true, 'choices' => $adsEnt->getCategoriesList()));
    $builder->add('offerCatalogue', 'choice', array('required' => true, 'choices' => $options['data']->getCataloguesList(true)));
    $builder->add('offerObjetState', 'choice', array('required' => true, 'choices' => $adsEnt->getObjetStates()));
    $builder->add('offerPrice', 'text', array('required' => true, 'trim' => true, 'max_length' => 10));
    $builder->add('offerCountry', 'choice', array('required' => true, 'choices' => $options['data']->getCountriesList()));
    $builder->add('offerCity', 'choice', array('required' => true, 'choices' => $options['data']->getCitiesList()));
    $builder->add('offerAd', 'hidden', array('required' => true));
    $builder->add('offerTax', 'choice', array('required' => true, 'choices' => Tax::getTaxesToSelect(false)));
    $builder->add('deliveryYN', 'choice', array('required' => true, 'multiple' => false, 'expanded' => true, 'choices' => $options['data']->getDeliveryFeesYNList()));
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
    for($i = 1; $i < 11; $i++)
    {
        $builder->add('tag'.$i, 'text', array('required' => false, 'max_length' => 20));
    }
    foreach($options['data']->getFormFields() as $f => $field)
    {
      $options = (array)unserialize($field['typeOptionsForm']);
      $builder->add($field['codeName'], $field['typeForm'], $options);
    }
    $listener = new FormTestListener($builder->getFormFactory());
    $builder->addEventSubscriber($listener);
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('addOffer'));
  }

  public function getName()
  {
    return 'AddOffer';
  }
}