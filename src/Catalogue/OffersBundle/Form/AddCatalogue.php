<?php
namespace Catalogue\OffersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form used to add a new catalogue.
 */
class AddCatalogue extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('catalogueName', 'text', array('required' => true, 'trim' => true, 'max_length' => 100));
    $builder->add('catalogueDesc', 'textarea', array('required' => false, 'trim' => true, 'max_length' => 200));
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('addCatalogue'));
  }

  public function getName()
  {
    return 'AddCatalogue';
  }
}