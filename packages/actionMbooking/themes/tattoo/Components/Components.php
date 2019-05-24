<?php

namespace packages\actionMbooking\themes\tattoo\Components;
use packages\actionMbooking\Components\Components as BootstrapComponents;

class Components extends BootstrapComponents {

    use uiKitDoubleSelector;
    use uiKitHintedCalendar;
    use uiKitHintedTime;
    use ItemTagTrait;
    use BookingTimePopup;

}
