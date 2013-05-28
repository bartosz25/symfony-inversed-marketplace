<?php
namespace Order\OrdersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Order\OrdersBundle\Entity\Delivery;

/**
 * Form used to fill up the next step of order creation.
 */
class OrderDelivery extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('orderPackRef', 'text', array('required' => false));
    $builder->add('orderComment', 'textarea', array('required' => false));
    $builder->add('orderState', 'choice', array('required' => true, 'expanded' => false, 'multiple' => false, 'choices' => $options['data']->getOrderStatesToSelect()));
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('orderDelivery'));
  }

  public function getName()
  {
    return 'OrderDelivery';
  }
}