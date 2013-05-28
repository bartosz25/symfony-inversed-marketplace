<?php
namespace Ad\QuestionsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form used to reply to a question.
 */
class Reply extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('replyText', 'textarea', array('required' => true, 'trim' => true));
    $builder->add('replyType', 'choice', array('required' => true, 'expanded' => true, 'trim' => true, 'choices' => $options['data']->types));
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('reply'));
  }

  public function getName()
  {
    return 'Reply';
  }
}