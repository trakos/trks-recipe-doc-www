<?php

namespace RecipeDoc\Controller\Main;

use RecipeDoc\Library\Mvc\MyAbstractController;
use RecipeDoc\Model\RecipeDocData;
use Trks\Mvc\Controller\TrksForwardException;
use Zend\View\Model\ViewModel;

class Home extends MyAbstractController
{
    public function indexAction()
    {
        return new ViewModel();
    }


    public function itemAction()
    {
        $item = RecipeDocData::getInstance()->getItem($this->params()->fromRoute('id', 0), $this->params()->fromRoute('id2', 0));
        if (!$item)
        {
            $this->flashMessenger()->addErrorMessage('No item with given id found!');
            throw new TrksForwardException("main");
        }

        return array(
            'item' => $item
        );
    }
}
