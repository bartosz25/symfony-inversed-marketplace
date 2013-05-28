<?php
namespace Order\OrdersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Order\OrdersBundle\Entity\Delivery;

/**
 * Form used to fill up the next step of order creation.
 */
class OrderNextStep extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('orderDeliveryFalse', 'choice', array('required' => false, 'expanded' => true, 'multiple' => true, 'choices' => array(1 => 'addresse de livraison incomplète')));
    $builder->add('paymentInfos', 'textarea', array('required' => true));
    $builder->add('orderDelivery', 'text', array('required' => true));
    $builder->add('orderComment', 'textarea', array('required' => false));
    $builder->add('orderCarrier', 'choice', array('required' => true, 'choices' => Delivery::getCarriers(false)));
    $builder->add('orderState', 'choice', array('required' => true, 'expanded' => false, 'multiple' => false, 'choices' => $options['data']->getOrderStatesToSelect()));
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('orderNextStep'));
  }

  public function getName()
  {
    return 'OrderNextStep';
  }
}