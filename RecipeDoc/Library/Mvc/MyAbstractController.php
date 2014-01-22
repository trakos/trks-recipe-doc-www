<?php

namespace RecipeDoc\Library\Mvc;


use Trks\Mvc\Controller\TrksAbstractController;
use Trks\Mvc\Controller\TrksForwardException;

class MyAbstractController extends TrksAbstractController
{
    /**
     * @param $module
     * @param $controller
     * @param $action
     *
     * @throws \Trks\Mvc\Controller\TrksForwardException
     *
     */
    protected function isAllowed($module, $controller, $action)
    {
        return;
    }

    /**
     * Called after action dispatch.
     *
     */
    protected function createViewData()
    {
    }

    /**
     * @throws \Exception
     * @return void|\Zend\Http\PhpEnvironment\Request
     */
    public function getRequest()
    {
        $request = parent::getRequest();
        if (!$request || $request instanceof \Zend\Http\PhpEnvironment\Request) {
            return $request;
        } else {
            throw new \Exception("Wrong request type!");
        }
    }
}