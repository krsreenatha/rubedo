<?php
return array(
    'routes' => array(
        // route for different frontoffice controllers
        'frontoffice' => array(
            'type' => 'Literal',
            'options' => array(
                'route' => '/',
                'defaults' => array(
                    '__NAMESPACE__' => 'Rubedo\Frontoffice\Controller',
                    'controller' => 'Index',
                    'action' => 'index'
                )
            ),
            'may_terminate' => true,
            'child_routes' => array(
                'default' => array(
                    'type' => 'Segment',
                    'options' => array(
                        'route' => '[:controller[/:action]]',
                        '__NAMESPACE__' => 'Rubedo\Frontoffice\Controller',
                        'constraints' => array(
                            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                        ),
                        'defaults' => array()
                    )
                )
            )
        ),
        // resolve to URL to pageId
        'rewrite' => array(
            'type' => 'Rubedo\Router\FrontofficeRoute',
            'options' => array(
                'route' => '/',
                'defaults' => array(
                    'controller' => 'Rubedo\Frontoffice\Controller\Index',
                    'action' => 'index'
                )
            )
        ),
        // install route : prefix by install
        'install' => array(
            'type' => 'Literal',
            'options' => array(
                'route' => '/install',
                'defaults' => array(
                    '__NAMESPACE__' => 'Rubedo\Install\Controller',
                    'controller' => 'Index',
                    'action' => 'index'
                )
            ),
            'may_terminate' => true,
            'child_routes' => array(
                'default' => array(
                    'type' => 'Segment',
                    'options' => array(
                        'route' => '/[:controller[/:action]]',
                        '__NAMESPACE__' => 'Rubedo\Install\Controller',
                        'constraints' => array(
                            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                        ),
                        'defaults' => array()
                    )
                )
            )
        ),
        // Blocks controller (for Ajax Access)
        'blocks' => array(
            'type' => 'Literal',
            'options' => array(
                'route' => '/blocks',
                'defaults' => array(
                    '__NAMESPACE__' => 'Rubedo\Blocks\Controller',
                    'controller' => 'Index',
                    'action' => 'index'
                )
            ),
            'may_terminate' => true,
            'child_routes' => array(
                'default' => array(
                    'type' => 'Segment',
                    'options' => array(
                        'route' => '/[:controller[/:action]]',
                        '__NAMESPACE__' => 'Rubedo\Blocks\Controller',
                        'constraints' => array(
                            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                        ),
                        'defaults' => array()
                    )
                )
            )
        ),
//         Backoffice route : prefix by backoffice
        'backoffice' => array(
            'type' => 'Literal',
            'options' => array(
                'route' => '/backoffice',
                'defaults' => array(
                    '__NAMESPACE__' => 'Rubedo\Backoffice\Controller',
                    'controller' => 'Index',
                    'action' => 'index'
                )
            ),
            'may_terminate' => true,
            'child_routes' => array(
                'default' => array(
                    'type' => 'Segment',
                    'options' => array(
                        'route' => '/[:controller[/:action]]',
                        '__NAMESPACE__' => 'Rubedo\Backoffice\Controller',
                        'constraints' => array(
                            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                        ),
                        'defaults' => array()
                    )
                )
            )
        ),
        // AppExtension route : prefix by backoffice/app
        'AppExtension' => array(
            'type' => 'Literal',
            'options' => array(
                'route' => '/backoffice/app',
                'defaults' => array(
                    '__NAMESPACE__' => 'Rubedo\Backoffice\Controller',
                    'controller' => 'AppExtension',
                    'action' => 'get-file'
                )
            ),
            'may_terminate' => true,
            'child_routes' => array(
                'default' => array(
                    'type' => 'Segment',
                    'options' => array(
                        'route' => '/:app-name/:filepath{-}',
                        '__NAMESPACE__' => 'Rubedo\Blocks\Controller',
                        'constraints' => array(
                        ),
                        'defaults' => array()
                    )
                )
            )
        ),
    )
);