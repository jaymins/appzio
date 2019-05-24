<?php

namespace packages\actionMprofile\Components;

use Bootstrap\Components\BootstrapComponent;

/* all components need to be define here. Idea is that the
components should be easily movable between the actions,
so they should be fairly self-contained */

class Components extends BootstrapComponent {

    use getComponentField;
    use getUserProfileImage;

}
