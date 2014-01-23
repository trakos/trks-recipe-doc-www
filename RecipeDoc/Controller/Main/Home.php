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
            /* @var ViewModel $model */
            $model = $this->notFoundAction();
            $model->setVariable('message', 'Item not found!');
            return $model;
        }

        return array(
            'item' => $item
        );
    }

    public function recipeHandlerAction()
    {
        $handler = RecipeDocData::getInstance()->getRecipeHandler($this->params()->fromQuery('action', 'empty'));
        if (!$handler)
        {
            /* @var ViewModel $model */
            $model = $this->notFoundAction();
            $model->setVariable('message', 'Recipe type not found!');
            return $model;
        }

        return array(
            'recipeHandler' => $handler
        );
    }
}
