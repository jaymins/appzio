<?php

/**
 * Component usage is defined on this file. You can also include componets from other
 * actions by providing the full namespace to them. Components should be independent
 * for maximum re-usability. Even though you can access model inside the component,
 * you should only use model methods that are provided by Bootstrap. Example of where
 * model is is used directly by the component is with validation error messages.
 *
 * Themes extend this file. Make sure to set the name spaces correctly.
 *
 * Normal documentation practise is to document only the functions inside the components
 * themselves which are traits.
 *
 * NOTE: components are expected to return objects, that can be applied directly on
 * the layout by the view.
 */

namespace packages\actionMbooking\Components;
use Bootstrap\Components\AppzioUiKit\Controls\uiKitDoubleSelector;
use Bootstrap\Components\AppzioUiKit\Controls\uiKitHintedCalendar;
use Bootstrap\Components\BootstrapComponent;

class Components extends BootstrapComponent {

    use uiKitHintedCalendar;
    use uiKitDoubleSelector;
    use ValidationErrorText;
    use ConfirmedBooking;
    use PendingBooking;
    use BookingCardImage;
    use BookingCardInformation;
    use BookingNoteDiv;
    use BookingTimeDiv;

}
