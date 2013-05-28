<?php
namespace Frontend\FrontBundle\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\Form\Util\FormUtil;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\FormHelper as ParentHelper;

class FormHelper extends ParentHelper
{
    protected $engine;

    protected $varStack;

    protected $context;

    protected $resources;

    protected $themes;

    protected $templates;

    protected function renderSection(FormView $view, $section, array $variables = array())
    {
        // if new theme is set (for exemple for override form templates)
        if(isset($variables['attr']['theme'])) $this->setTheme($view,  $variables['attr']['theme']);
        return parent::renderSection($view, $section, $variables);
    }

}
