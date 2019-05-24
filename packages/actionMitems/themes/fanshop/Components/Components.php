<?php

namespace packages\actionMitems\themes\fanshop\Components;
use Bootstrap\Components\AppzioUiKit\Listing\uiKitItemListInfinite;
use Bootstrap\Components\BootstrapComponent as BootstrapComponents;
use packages\actionMitems\Components\HintedIconField;
use packages\actionMitems\Components\ItemCard;
use packages\actionMitems\Components\ItemTag;
use packages\actionMitems\Components\RadioButtons;
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
