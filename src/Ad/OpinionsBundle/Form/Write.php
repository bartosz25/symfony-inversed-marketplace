<?php
namespace Ad\OpinionsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form used to send a new ad opinion.
 */
class Write extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('opinionTitle', 'text', array('required' => true, 'trim' => true, 'max_length' => 150));
    $builder->add('opinionText', 'textarea', array('required' => true, 'trim' => true));
    $builder->add('opinionNote', 'choice', array('required' => true, 'expanded' => true, 'multiple' => false, 'choices' => $options['data']->getNotes()));
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('write'));
  }

  public function getName()
  {
    return 'Write';
  }
}