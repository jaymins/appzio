<?php
return [
    'bootstrap' => ['gii'],
    'modules' => [
        'gii' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1'],
        'generators' => [
            // generator name
            'giiant-crud' => [
                //generator class
                'class'     => 'schmunk42\giiant\generators\model\Generator',
                //setting for out templates
                'templates' => [
                    // template name => path to template
                    'default' => '@backend/giiTemplates/crud/default',
                ]
            ]
        ],

    ],
];
