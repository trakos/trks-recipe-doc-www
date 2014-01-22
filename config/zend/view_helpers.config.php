<?php


return array(
    'service_manager' => array(
        'factories' => array(
            'url' => 'Trks\View\Helper\Url',
        )
    ),
    'view_helpers' => array(
        'invokables' => array(
            'form_row'            => 'Trks\Form\View\Helper\FormRow',
            'form_multi_checkbox' => 'Trks\Form\View\Helper\FormMultiCheckbox',
            'form_radio'          => 'Trks\Form\View\Helper\FormRadio',
            'form_element_errors' => 'Trks\Form\View\Helper\FormElementErrors',
            'form_button_row'     => 'Trks\Form\View\Helper\FormButtonRow',
            'messages'            => 'Trks\View\Helper\Messages',
        ),
        'factories'  => array(
            'url' => function ($sm) {
                    $helper = new Trks\View\Helper\Url();
                    $router = Zend\Console\Console::isConsole() ? 'HttpRouter' : 'Router';
                    $helper->setRouter(RecipeDoc::getApplication()->getServiceManager()->get($router));

                    $match = RecipeDoc::getApplication()
                        ->getMvcEvent()
                        ->getRouteMatch();

                    if ($match instanceof Zend\Mvc\Router\RouteMatch) {
                        $helper->setRouteMatch($match);
                    }

                    return $helper;
                }
        )
    ),
);