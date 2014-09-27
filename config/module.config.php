<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Analytics.Controller.Index' => 'Analytics\Controller\IndexController'
        ),
    ),

    'service_manager' => array(
        'invokables' => array(
            'Analytics.Controller.Index' => 'Analytics\Controller\IndexController'
        ),
    ),

    'router' => array(
        'routes' => array(
            'analytics_oauthaccessback' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/analytics/accessback',
                    'defaults' => array(
                        'controller' => 'Analytics.Controller.Index',
                        'action'     => 'oauthbackcode',
                    ),
                ),
            ),
        ),
    ),

    'yima-settings' => array(
        'analytics' => array(
            'label' => 'Google Analytics Settings',
            'properties' => array(
                // Visit https://console.developers.google.com/ to generate your
                // client id, client secret, and to register your redirect uri.
                'client_id'   => array(
                    'label' => 'Client ID',
                ),
                'client_secret'   => array(
                    'label' => 'Client Secret',
                ),
                'developer_key'   => array(
                    'label' => 'Developer Key',
                ),
                'redirect_uri'   => array(
                    'label' => 'Redirect URL',
                    # form element
                    'element' => array(
                        'type' => 'Zend\Form\Element\Url',
                        'attributes' => array(
                            #'required' => 'required',
                            #'disabled' => 'disabled',
                        ),
                        'options' => array(
                            # these options was replaced by values from top
                            # 'label' => 'label not set here',
                            # 'value' => 'value not set from here because of hydrator',
                        ),
                    ),
                ),
                'refresh_token'   => array(
                    # used as default value
                    'label' => 'Refresh Token',
                ),
            ),
            'options' => array(
                // default values
                'default_values' => array(

                ),
            ),
        ),
    ),

    # Add menu item into adminor navigation
    'navigation' => array(
        'admin' => array(
            array(
                'label' 	 => 'Analytics',
                'route'		 => \yimaAdminor\Module::ADMIN_DEFAULT_ROUTE_NAME,
                'module'     =>'Analytics',
                'controller' => 'Index',
                //'action'     => 'dashboard', // by default
                'pages' 	 => array(
                    array(
                        'label' 	 => 'Dashboard',
                        'route'		 => \yimaAdminor\Module::ADMIN_DEFAULT_ROUTE_NAME,
                        'module'     => 'Analytics',
                        //'action'   => 'dashboard', // by default
                        'order' 	 => 10000,
                    ),
                    array(
                        'label' 	 => 'Grant Access',
                        'route'		 => \yimaAdminor\Module::ADMIN_DEFAULT_ROUTE_NAME,
                        'module'     => 'Analytics',
                        'controller' => 'Index',
                        'action'     => 'access',
                    ),
                    array(
                        'label' 	 => 'Settings',
                        'route'		 => \yimaAdminor\Module::ADMIN_DEFAULT_ROUTE_NAME,
                        'module'     =>'yimaSettings',
                        'params'     => array(
                            'setting'    => 'analytics',
                        ),
                        'order' 	 => -10000,
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
