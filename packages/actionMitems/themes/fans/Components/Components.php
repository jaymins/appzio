<?php

namespace packages\actionMitems\themes\fans\Components;

use packages\actionMbooking\Components\ValidationErrorText;
use packages\actionMitems\Components\Components as BootstrapComponents;
use packages\actionMitems\themes\venues\Components\getVenueList;

class Components extends BootstrapComponents {

    use IntroScreenOverlay;
    use uiKitHintedCalendar;
    use uiKitIntroWithButtons;
    use uiKitThreeColumnImageSwiper;
    use ReminderDiv;
    use NextVisitDiv;
    use uiKitHintedTime;
    use uiKitDoubleSelector;
    use getCalendarPicker;
    use ShowNextVisitDiv;
    use ValidationErrorText;
	use getPicker;
	use getNamePicker;
	use getHourPicker;
	use ItemTag;
	use uiKitPaddedHintedCalendar;
	use VisitCategories;
	use CategoryAccordion;
	use CategoryDescription;
	use ShowReminderDiv;
	use CloseDivButton;
	use uiKitVisitTopbar;
	use getVenueList;

}