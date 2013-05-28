<?php
namespace Order\OrdersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form used to switch the order states.
 */
class OrderFormState extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('orderState', 'choice', array('required' => true, 'expanded' => false, 'multiple' => false, 'choices' => $options['data']->getOrderStatesToSelect()));  
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
    $builder->add('orderComment', 'textarea', array('required' => false));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('orderFormState'));
  }

  public function getName()
  {
    return 'OrderFormState';
  }
}