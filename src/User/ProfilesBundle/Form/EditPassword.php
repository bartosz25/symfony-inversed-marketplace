<?php
namespace User\ProfilesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form used to define the user's new password.
 */
class EditPassword extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('password', 'password', array('required' => true, 'trim' => true, 'always_empty' => true));
    $builder->add('pass1', 'password', array('required' => true, 'trim' => true, 'always_empty' => true));
    $builder->add('pass2', 'password', array('required' => true, 'trim' => true, 'always_empty' => true));
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('editPassword'));
  }

  public function getName()
  {
    return 'EditPassword';
  }
}