<?php
namespace Catalogue\OffersBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class OffersFormFieldsRepository extends EntityRepository 
{

  /**
   * Gets form fields for offer.
   * @access public
   * @param int $offer Offer's id
   * @param int $category Categorie's id
   * @return array Form fields list.
   */
  public function getFieldsByOffer($offer, $category)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT off.fieldValue, ff.fullName, ff.codeName, ff.id_ff,
    ff.typeForm, ff.typeOptionsForm, ff.constraintsForm, ff.dependenciesPhpForm,
    ff.dependenciesJsForm, ff.entityForm, ca.labelForm
    FROM CatalogueOffersBundle:OffersFormFields off
    JOIN off.form_fields_id_ff ff
    JOIN off.offers_id_of o 
    JOIN off.categories_id_ca ca
    WHERE o.id_of = :offer AND ca.categories_id_ca = :category AND ca.form_fields_id_ff = ff.id_ff")
    ->setParameter('offer', $offer)
    ->setParameter('category', $category);
    return $query->getResult();
  }

}