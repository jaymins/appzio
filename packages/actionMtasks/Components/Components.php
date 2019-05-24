<?php

namespace packages\actionMtasks\Components;

use Bootstrap\Components\BootstrapComponent;
use packages\actionMearnster\Components\getIntroText;
use packages\actionMproducts\Components\getCartHeader;
use packages\actionMproducts\Components\getProductListItem;

/* all components need to be define here. Idea is that the
components should be easily movable between the actions,
so they should be fairly self-contained */

class Components extends BootstrapComponent {

    use getPhotoField;
    use getShadowBox;
    use getIconField;
    use getPhoneNumberField;
    use getDivPhoneNumbers;
    use getProgressHeader;
    use getAddAdultButton;
    use getFauxTopbar;
    use getAddAdult;
    use \packages\actionMregister\themes\stepbystep\Components\HintedField;
    use getAdultRow;
    use getTaskSelector;
    use getHintedSelector;
    use getHintedCalendar;
    use getHintedDateSelector;
    use getProductListItem;
    use getCartHeader;
    use getSummaryBox;
    use getTaskSummary;
    use getAdultRowSummary;
    use getTaskListChild;
    use getIntroText;
    use getHintedSelectorComposit;
    use getLargeProgress;
    use getEditAdult;

}
