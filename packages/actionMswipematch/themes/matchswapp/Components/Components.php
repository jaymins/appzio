<?php

namespace packages\actionMswipematch\themes\matchswapp\Components;
use packages\actionMswipematch\Components\Components as BootstrapComponents;

class Components extends BootstrapComponents {

    use Hello;
    use getUserListing;
    use getUserListingSwipe;
    use getCheckinFloatingButton;
    use getListSwipeFloatingButton;
}