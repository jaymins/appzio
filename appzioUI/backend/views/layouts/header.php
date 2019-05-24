<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

$firstname = Yii::$app->session['firstname'];
$lastname = Yii::$app->session['lastname'];

if ( $firstname AND $lastname ) {
    $name = $firstname . ' ' . $lastname;
} else {
    $name = Yii::$app->session['username'];
}

?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">APP</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="http://www.realcommercial.com.au/building/assets/images/avatar_placeholder.png" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?php echo $name; ?></span>
                    </a>
                </li>
                
            </ul>
        </div>
    </nav>
</header>