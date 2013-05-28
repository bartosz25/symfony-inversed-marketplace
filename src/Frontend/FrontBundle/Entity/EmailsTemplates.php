<?php
namespace Frontend\FrontBundle\Entity;


class EmailsTemplates 
{

  protected $fixed = array("header" => "tpl/header.maildoc", "footer" => "tpl/footer.maildoc");
  protected $path;

  public function __construct()
  {
    $this->path = rootDir."mails/";
  }
  /**
   * Getters for e-mails configurations.
   */
  public function getHeaderTemplate()
  {
    return $this->path.$this->fixed["header"];
  }
  public function getFooterTemplate()
  {
    return $this->path.$this->fixed["footer"];
  }
}