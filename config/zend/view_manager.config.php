<?php

return array(
    'view_manager' => array(
        'base_path'                => '/',
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map'             => array(
            'layout/layout'   => T_PATH_APPLICATION . '/view/_layout/layout.phtml',
            'error/404'       => T_PATH_APPLICATION . '/view/_error/404.phtml',
            'error/index'     => T_PATH_APPLICATION . '/view/_error/500.phtml',
        ),
        'template_path_stack'      => array(
            T_PATH_APPLICATION . '/view/',
        ),
        'strategies'               => array(
            'ViewJsonStrategy',
        ),
    ),
);