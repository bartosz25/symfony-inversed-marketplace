<?php
namespace User\ProfilesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form used to register an user.
 */
class Register extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('login', 'text', array('max_length' => 10, 'trim' => true, 'required' => true));
    $builder->add('pass1', 'password', array('required' => true, 'trim' => true, 'always_empty' => true));
    $builder->add('pass2', 'password', array('required' => true, 'trim' => true, 'always_empty' => true));
    $builder->add('email', 'text', array('required' => true, 'trim' => true, 'max_length' => 200));
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('registration'));
  }

  public function getName()
  {
    return 'Register';
  }
}