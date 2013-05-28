<?php
namespace User\ProfilesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form used to define the user's new e-mail address.
 */
class EditEmail extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('email', 'text', array('required' => true, 'trim' => true, 'max_length' => 200));
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('editEmail'));
  }

  public function getName()
  {
    return 'EditEmail';
  }
}