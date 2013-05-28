<?php
namespace Catalogue\OffersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Order\OrdersBundle\Entity\Tax;

/**
 * Form used to synchronize catalogue with Prestashop store.
 */
class SynchronizePrestashopFirst extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('syncSite', 'text', array('required' => true, 'trim' => true, 'max_length' => 255));
    $builder->add('syncKey', 'text', array('required' => true, 'trim' => true, 'max_length' => 255));
    $builder->add('syncCountry', 'choice', array('required' => true, 'choices' => $options['data']->getCountriesList()));
    $builder->add('syncCity', 'choice', array('required' => true, 'choices' => $options['data']->getCitiesList()));
    $builder->add('syncDefaultState', 'choice', array('required' => true, 'choices' => $options['data']->getStates()));
    $builder->add('syncTax', 'choice', array('required' => true, 'choices' => Tax::getTaxesToSelect(false)));
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('synchronizePrestashopFirst'));
  }

  public function getName()
  {
    return 'SynchronizePrestashopFirst';
  }
}