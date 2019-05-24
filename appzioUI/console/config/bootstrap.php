<?php

\Yii::$container->set(
    'schmunk42\giiant\generators\crud\providers\extensions\DateTimeProvider',
    [
        'columnNames' => ['creation_date']
    ]
);