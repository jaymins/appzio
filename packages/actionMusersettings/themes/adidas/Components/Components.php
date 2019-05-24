<?php

namespace packages\actionMusersettings\themes\adidas\Components;
use packages\actionMusersettings\Components\Components as BootstrapComponents;
use packages\actionMregister\themes\tatjack2\Components\getPhotoField;
use packages\actionMregister\themes\tatjack2\Components\getIconField;
use packages\actionMregister\Components\getPhoneNumberField;

class Components extends BootstrapComponents {

    use Hello;
    use getPhotoField;
    use getIconField;
    use HintedField;
    use getSummaryBox;
    use getPhoneNumberField;
}
