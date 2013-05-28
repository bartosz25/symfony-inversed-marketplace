<?php
namespace Catalogue\ImagesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * Form used to add a new image.
 */
class AddImage extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('imageOffer', 'choice', array('required' => true, 'choices' => $options['data']->getOffersList()));
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('addImage'));
  }

  public function getName()
  {
    return 'AddImage';
  }
}