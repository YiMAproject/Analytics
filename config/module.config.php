<?php
return array(
    'yima-settings' => array(
        'analytics' => array(
            'label' => 'Analytics / Google Settings',
            'properties' => array(
                // Visit https://console.developers.google.com/ to generate your
                // client id, client secret, and to register your redirect uri.
                'client_id'   => array(
                    # used as default value
                    'value' => '662442608195-c471t6g3lkpbbc7gcn36l5gjpimpk6dd.apps.googleusercontent.com',
                    'label' => 'Client ID',
                ),
                'client_secret'   => array(
                    # used as default value
                    'value' => 'IUPSwDqehwlTMZThks_-05Up',
                    'label' => 'Client Secret',
                ),
                'developer_key'   => array(
                    # used as default value
                    'value' => '662442608195-c471t6g3lkpbbc7gcn36l5gjpimpk6dd@developer.gserviceaccount.com',
                    'label' => 'Developer Key',
                ),
                'redirect_uri'   => array(
                    # used as default value
                    'value' => 'http://dev.crm.zuoo.nl/analytics',
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
                    'value' => '1/hkSAziKfXhHdmkXWOHJjYJSCtg16f4HMKUsx5yQi7Ek',
                    'label' => 'Refresh Token',
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
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
