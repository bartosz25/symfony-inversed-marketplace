<?php
namespace Others;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface; 

class FormTestListener implements EventSubscriberInterface 
{
private $factory;

    public function __construct(FormFactoryInterface $factory)
    {  
        $this->factory = $factory;
    }  

    public static function getSubscribedEvents()
    {  
        return array(
            FormEvents::BIND_CLIENT_DATA => 'preSetData',
        );  
    }  

  public function preSetData(DataEvent $event)
  {  
    $data = $event->getData();
    $form = $event->getForm(); 
    if(!$data)
    return;

        
            // $data is your setting, do whatever conditionnal form creation

               // you want using it here !

// cela cr�e une variable dans l'entit�, ce qui nous permet d'ex�cuter
// les setters et les getters; donc on peut �galement ex�cuter les validations
// Callback

/// TODO : � faire la manipulation dynamique !
    // gets used Entity
    $newFields = $form->getClientData()->getFormFields();
    foreach($newFields as $field)
    {
      $form->add($this->factory->createNamed($field['typeForm'], $field['codeName']));
    }
// $form->add($this->factory->createNamed('text', 'technology'));
// $form->add($this->factory->createNamed('text', 'siteweb'));
  // var_dump($data); 
 
 
 // die();
  }  

} 
   
?>