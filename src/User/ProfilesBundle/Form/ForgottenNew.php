<?php
namespace User\ProfilesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form used to resend a forgotten credentials to user.
 */
class ForgottenNew extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('pass1', 'password', array('required' => true, 'trim' => true, 'always_empty' => true));
    $builder->add('pass2', 'password', array('required' => true, 'trim' => true, 'always_empty' => true));
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('forgottenNew'));
  }

  public function getName()
  {
    return 'ForgottenNew';
  }
}