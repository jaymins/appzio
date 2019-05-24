<?php

namespace packages\actionMproducts\Components;

use Bootstrap\Components\BootstrapComponent;
use packages\actionMearnster\Components\getIntroText;

/* all components need to be define here. Idea is that the
components should be easily movable between the actions,
so they should be fairly self-contained */

class Components extends BootstrapComponent {

    use getPhotoField;
    use getShadowBox;
    use getIconField;
    use getPhoneNumberField;
    use getDivPhoneNumbers;
    use getCategoryListItem;
    use getProductListItem;
    use getProductPage;
    use getCartHeader;
    use getFeaturedProductsSwiper;
    use getSearchBox;
    use getFeaturedProductsSwiperBig;
    use getAddToCartButton;
    use getIntroText;

}
