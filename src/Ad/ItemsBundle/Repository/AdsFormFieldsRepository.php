<?php
namespace Ad\ItemsBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class AdsFormFieldsRepository extends EntityRepository 
{

  /**
   * Gets form fields for ad.
   * @access public
   * @param int $ad Ad's id
   * @return array Form fields list.
   */
  public function getFieldsByAd($ad, $category)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT aff.fieldValue, ff.fullName, ff.codeName, 
    ff.typeForm, ff.typeOptionsForm, ff.constraintsForm, ff.dependenciesPhpForm,
    ff.dependenciesJsForm, ff.entityForm, ca.labelForm
    FROM AdItemsBundle:AdsFormFields aff
    JOIN aff.form_fields_id_ff ff
    JOIN aff.ads_id_ad a 
    JOIN aff.categories_id_ca ca
    WHERE a.id_ad = :ad AND ca.categories_id_ca = :category AND ca.form_fields_id_ff = ff.id_ff")
    ->setParameter('ad', $ad)
    ->setParameter('category', $category);
    return $query->getResult();
  }

}