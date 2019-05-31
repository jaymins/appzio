<?php

namespace packages\actionDitems\themes\classifieds\Components;
use Bootstrap\Components\AppzioUiKit\Listing\uiKitItemListInfinite;
use Bootstrap\Components\BootstrapComponent as BootstrapComponents;
use packages\actionDitems\Components\HintedIconField;
use packages\actionDitems\Components\ItemCard;
use packages\actionDitems\Components\ItemTag;
use packages\actionDitems\Components\RadioButtons;
use packages\actionMregister\themes\stepbystep\Components\HintedField;
use packages\actionMtasks\Components\getIconField;

class Components extends BootstrapComponents {

    use ItemCard;
    use ItemTag;
    use RadioButtons;
    use getIconField;
    use HintedIconField;
    use uiKitItemListInfinite;
    use AddButton;
    use HintedField;
    use IntroScreen;
    use IntroScreenOverlay;

}
