<?php
namespace User\ProfilesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form used to personalize user's card.
 */
class EditCard extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('userEbayLogin', 'text', array('required' => true, 'trim' => true, 'max_length' => 64));
    $builder->add('userPrestashopStore', 'text', array('required' => true, 'trim' => true, 'max_length' => 255));
    $builder->add('userProfile', 'textarea', array('required' => true, 'trim' => true, 'max_length' => 200));
    $builder->add('userType', 'choice', array('required' => true, 'multiple' => false, 'expanded' => true, 'choices' => $options['data']->getUserTypesAliases()));
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('editCard'));
  }

  public function getName()
  {
    return 'EditCard';
  }
}