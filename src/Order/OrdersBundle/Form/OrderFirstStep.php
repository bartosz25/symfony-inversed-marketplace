<?php
namespace Order\OrdersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form used to fill up the first step of order creation.
 */
class OrderFirstStep extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('orderPayment', 'choice', array('required' => true, 'choices' => $options['data']->getPaymentTypes(true)));
    $builder->add('orderPreferedDelivery', 'choice', array('required' => true, 'choices' => $options['data']->getDeliveryTypes(), 'expanded' => true, 'multiple' => true));
    $builder->add('orderState', 'choice', array('required' => true, 'expanded' => false, 'multiple' => false, 'choices' => $options['data']->getOrderStatesToSelect()));  
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
    $builder->add('orderComment', 'textarea', array('required' => false));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('orderFirstStep'));
  }

  public function getName()
  {
    return 'OrderFirstStep';
  }
}