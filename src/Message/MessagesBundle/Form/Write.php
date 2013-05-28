<?php
namespace Message\MessagesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form used to send a new message or to reply to an old message.
 */
class Write extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('contentTitle', 'text', array('required' => true, 'trim' => true, 'max_length' => 200));
    $builder->add('contentMessage', 'textarea', array('required' => true, 'trim' => true));
    $builder->add('recipersList', 'hidden', array('required' => true));
    $builder->add('recipersLogins', 'hidden', array('required' => true));
    $builder->add('isProfile', 'hidden', array('required' => true));
    $builder->add('message', 'hidden', array());
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