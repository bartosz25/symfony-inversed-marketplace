<?php
namespace User\AddressesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form used to fill up the first step of order creation.
 */
class FirstStep extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('addressCountry', 'choice', array('required' => true, 'choices' => $options['data']->getCountriesList()));
    $builder->add('addressFirstName', 'text', array('required' => true, 'trim' => true, 'max_length' => 30));
    $builder->add('addressLastName', 'text', array('required' => true, 'trim' => true, 'max_length' => 50));
    $builder->add('addressPostalCode', 'text', array('required' => true, 'trim' => true, 'max_length' => 8));
    $builder->add('addressCity', 'text', array('required' => true, 'trim' => true, 'max_length' => 30));
    $builder->add('addressStreet', 'textarea', array('required' => true, 'trim' => true, 'max_length' => 200));
    $builder->add('addressInfos', 'textarea', array('required' => false, 'trim' => true, 'max_length' => 300));
    $builder->add('addressId', 'hidden', array('required' => false, 'trim' => true ));
    $builder->add('addressHash', 'hidden', array('required' => false, 'trim' => true ));
    $builder->add('addressHashOld', 'hidden', array('required' => false, 'trim' => true ));
    $builder->add('addressOldId', 'hidden', array('required' => false, 'trim' => true ));
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('firstStep'));
  }

  public function getName()
  {
    return 'FirstStep';
  }
}