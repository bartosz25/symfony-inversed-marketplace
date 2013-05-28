<?php
namespace Category\CategoriesBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class FormFieldsCategoriesRepository extends EntityRepository 
{

  /**
   * Gets all form fields.
   * @access public
   * @param int $category Categorie's id.
   * @return array Categorie's list.
   */
  public function getFields($category)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT ffc.labelForm, f.id_ff, f.codeName, f.typeForm, f.typeOptionsForm,
    f.constraintsForm, f.dependenciesPhpForm, f.dependenciesJsForm, f.entityForm
    FROM CategoryCategoriesBundle:FormFieldsCategories ffc
    JOIN ffc.categories_id_ca c
    JOIN ffc.form_fields_id_ff f
    WHERE ffc.categories_id_ca = :category")
    ->setParameter("category", (int)$category);
    return $query->getResult(); 
  }

  /**
   * Gets form fields composition for each category by the IN's clause.
   * @access public
   * @param array $cats Categories array.
   * @return array Array with the composition.
   */
  public function getFieldsInCategories($cats)
  {
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->add('select', 'c.id_ca, f.id_ff')
    ->add('from', 'CategoryCategoriesBundle:FormFieldsCategories ffc')
    ->innerJoin('ffc.categories_id_ca', 'c')
    ->innerJoin('ffc.form_fields_id_ff', 'f')
    ->add('where', $qb->expr()->in('ffc.categories_id_ca', $cats));
    $query = $qb->getQuery();
    $finalResult = array();
    foreach($query->getResult() as $result)
    {
      $finalResult[$result['id_ca']][] = array('id_ff' => $result['id_ff']);
    }
    return $finalResult;
  }


}