<?php

use backend\components\Helper;

$nav_items = Helper::getMenusConfig();

$firstname = Yii::$app->session['firstname'];
$lastname = Yii::$app->session['lastname'];

if ($firstname AND $lastname) {
    $name = $firstname . ' ' . $lastname;
} else {
    $name = Yii::$app->session['username'];
}

$fieldsets = Helper::getThemeListing();
$app_id = Yii::$app->session['app_id'];
$current_config = Yii::$app->session->get('custom_config_'.$app_id);

?>

<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="http://www.realcommercial.com.au/building/assets/images/avatar_placeholder.png"
                     class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?php echo $name; ?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                <br>

            </div>
        </div>

        <div style="margin:15px;margin-left:25px;color:#ffffff">
            Admin modules:
            <br><br>

            <form action="/appzioUI/backend/web/users/usergroups-user" method="post" id="configchanger">

                <select style="color:#000000;max-width:190px;" onchange="this.form.submit();" name="fieldset">
                    <option value="default">Default</option>

                    <?php
                    foreach ($fieldsets as $field) {
                        $name = ucfirst(str_replace('.json', '', $field));

                        if ($current_config == $field) {
                            echo(' <option value="' . $field . '" SELECTED>' . $name . '</option>');
                        } else {
                            echo(' <option value="' . $field . '">' . $name . '</option>');
                        }
                    }

                    ?>
                </select>
                <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>"/>

            </form>
        </div>


        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => array_values($nav_items),
            ]
        /*
        [
            'options' => ['class' => 'sidebar-menu'],
            'items' => [
                ['label' => 'Menu Yii2', 'options' => ['class' => 'header']],
                ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
                ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug']],
                ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                [
                    'label' => 'Same tools',
                    'icon' => 'share',
                    'url' => '#',
                    'items' => [
                        ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii'],],
                        ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug'],],
                        [
                            'label' => 'Level One',
                            'icon' => 'circle-o',
                            'url' => '#',
                            'items' => [
                                ['label' => 'Level Two', 'icon' => 'circle-o', 'url' => '#',],
                                [
                                    'label' => 'Level Two',
                                    'icon' => 'circle-o',
                                    'url' => '#',
                                    'items' => [
                                        ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                                        ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]
        */
        ) ?>

    </section>

</aside>