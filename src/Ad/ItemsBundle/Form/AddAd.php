<?php
namespace Ad\ItemsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use User\ProfilesBundle\Entity\Users;
use Order\OrdersBundle\Entity\Tax;
use Others\FormTestListener;

/**
 * Form used to add a new ad.
 */
class AddAd extends AbstractType
{
  public function buildForm(FormBuilder $builder, array $options)
  {
    $userEnt = new Users;
    $builder->add('adName', 'text', array('required' => true, 'trim' => true, 'max_length' => 200));
    $builder->add('adText', 'textarea', array('required' => true, 'trim' => true));
    $builder->add('adCategory', 'choice', array('required' => true, 'choices' => $options['data']->getCategoriesList()));
    $builder->add('adCountry', 'choice', array('required' => true, 'choices' => $options['data']->getCountriesList()));
    $builder->add('adCity', 'choice', array('required' => true, 'choices' => $options['data']->getCitiesList()));
    $builder->add('adLength', 'text', array('required' => true, 'trim' => true, 'max_length' => 200));
    $builder->add('adMinOpinion', 'choice', array('choices' => $userEnt->getAveragesAliases()));
    $builder->add('adObjetState', 'choice', array('required' => true, 'choices' => $options['data']->getObjetStates()));
    $builder->add('adSellerType', 'choice', array('required' => true, 'choices' => $userEnt->getUserTypesAliases()));
    $builder->add('adSellerGeo', 'choice', array('required' => true, 'choices' => $options['data']->getSellerGeo()));
    $builder->add('adAtHomePage', 'choice', array('required' => true, 'expanded' => true, 'multiple' => false, 'choices' => array(1 => 'Oui', 2 => 'Non')));
    // $builder->add('adBuyFrom', 'text', array('required' => true, 'trim' => true, 'max_length' => 10));
    $builder->add('adBuyTo', 'text', array('required' => true, 'trim' => true, 'max_length' => 10));
    $builder->add('adValidity', 'choice', array('required' => true, 'choices' => $options['data']->getValidityTime()));
    $builder->add('adPayments', 'choice', array('expanded' => true, 'multiple' => true, 'required' => true, 'trim' => true,
    'choices' => $options['data']->getPayments(), 'data' => $options['data']->getAdPayments()));
    $builder->add('adTax', 'choice', array('required' => true, 'choices' => Tax::getTaxesToSelect(false)));
    for($i = 1; $i < 11; $i++)
    {
        $builder->add('tag'.$i, 'text', array('required' => false, 'max_length' => 20));
    }
    foreach($options['data']->getFormFields() as $f => $field)
    {
      $options = (array)unserialize($field['typeOptionsForm']);
      $builder->add($field['codeName'], $field['typeForm'], $options);
    }
    $builder->add('ticket', 'hidden', array('required' => true, 'trim' => true));
// echo serialize(array('required' => true, 'trim' => true, 'max_length' => 100));
// echo serialize(array(array('constraint' => 'NotBlank', 'options' => array('message' => "Indiquez le site")), 
// array('constraint' => 'MaxLength', 'options' => array('limit' => 4, 'message' => "Indiquez le site avec moins que 100 caractères")), ));
// $arr = unserialize('a:2:{i:0;a:2:{s:10:"constraint";s:49:"\\Symfony\\Component\\Validator\\Constraints\\NotBlank";s:7:"options";a:1:{s:7:"message";s:16:"Indiquez le site";}}i:1;a:2:{s:10:"constraint";s:50:"\\Symfony\\Component\\Validator\\Constraints\\MaxLength";s:7:"options";a:2:{s:5:"limit";i:4;s:7:"message";s:47:"Indiquez le site avec moins que 100 caractères";}}}');

// $arr[0]['options']['message'] = "Indiquez la technologie";
// $arr[1]['options']['limit'] = 100;
// $arr[1]['options']['message'] = "Indiquez la technologie avec moins que 100 caractères";
// echo serialize($arr);

// print_r($arr);
// die();
// $builder->add('technology', 'text', array('required' => true, 'trim' => true, 'max_length' => 200));
    $listener = new FormTestListener($builder->getFormFactory());
    $builder->addEventSubscriber($listener);
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array('addAd'));
  }

  public function getName()
  {
    return 'AddAd';
  }
}