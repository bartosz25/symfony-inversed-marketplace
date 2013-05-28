<?php
namespace Frontend\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form used to edit one tag.
 */
class EditTag extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('tagName', 'text', array('required' => true, 'trim' => true, 'max_length' => 20));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('editTag'));
  }

  public function getName()
  {
    return 'EditTag';
  }
}