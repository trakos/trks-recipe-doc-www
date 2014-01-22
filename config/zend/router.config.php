<?php

return array(
    'router' => array(
        'routes' => array(
            'regular' => array(
                'type' => 'Trks\\Mvc\\Router\\TrksRouter',
                'options' => array(
                    'route' => '/[:module][/:controller[/:action]][/:id1][/:id2]',
                    'constraints' => array(
                        'module' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'id2' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'module' => RecipeDoc::getDefaultModule(),
                        'controller' => 'home',
                        'action' => 'index',
                    ),
                ),
            ),
            'main' => array(
                'type' => 'Trks\\Mvc\\Router\\TrksRouter',
                'options' => array(
                    'route' => '/[:controller[/:action]][/:id][/:id2]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'id2' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'module' => RecipeDoc::getDefaultModule(),
                        'controller' => 'home',
                        'action' => 'index',
                    ),
                ),
            ),
            'main-index' => array(
                'type' => 'Trks\\Mvc\\Router\\TrksRouter',
                'options' => array(
                    'route' => '/[:action][/:id][/:id2]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                        'id2' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'module' => RecipeDoc::getDefaultModule(),
                        'controller' => 'home',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
);