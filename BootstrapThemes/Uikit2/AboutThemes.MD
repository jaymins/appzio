### About themes

Themes are a new way to arrange code inside Appzio. When your application relies on multiple actions / action themes that share same components, styles and images, you can use the theme instead or organizing the code inside actions or their themes.

### About namings

Its recommended to use general namings for your components, so that in case the theme needs to be duplicated, you don't need to replace code inside actions. Components should always start with word theme. So for example Button component becomes themeButton.

### How to include theme

Simply have your Components.php inside the action or action's theme extend the baseclass. So for example, a theme called uikit2 would like the following:

~~~~
namespace packages\actionMlogin\themes\uikit2\Components;

use BootstrapThemes\Uikit2\Components\UikitComponents;

class Components extends UikitComponents {

}
~~~~

