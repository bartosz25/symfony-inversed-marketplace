<?php
namespace User\ProfilesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form used to personalize user's profile.
 */
class EditUser extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('login', 'text', array('required' => true, 'trim' => true, 'max_length' => 10));
    $builder->add('email', 'text', array('required' => true, 'trim' => true,  'max_length' => 200));
    $builder->add('userProfile', 'textarea', array('required' => false, 'trim' => true, 'max_length' => 200));
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('editUser'));
  }

  public function getName()
  {
    return 'EditUser';
  }
}