<?php
namespace User\ProfilesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form used to edit user's eBay login.
 */
class EditEbayLogin extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('userEbayLogin', 'text', array('required' => true, 'trim' => true, 'max_length' => 64));
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('editEbayLogin'));
  }

  public function getName()
  {
    return 'EditEbayLogin';
  }
}