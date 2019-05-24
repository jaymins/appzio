# Appzio JavaScript Library
This library brings Appzio's UI development technology to JavaScript developers that want to use the platform.

Developed with Node 9, it takes advantage of all the latest features of the JS ecosystem to provide a modern development workflow.

## Contents

* [How does it work](#how)
* [Architecture](#architecture)
  * [Module Structure](#modules)
    * [Controllers](#controllers)
    * [Models](#models)
    * [Views](#views)
  * [Components](#components)
* [Development and Conventions](#development)
  * [Themes](#themes)
* [Building Components](#building-components)
* [External Components](#external-components)

## <a name="how"></a> How does it work?
Even though this library enales a JS developer to create their own UIs and business logic it is not independent. Appzio's Node port works tightly coupled with the PHP core in order to provide access to the already established Appzio ecosystem, functionality and components.

Whenever a request is fired from the client it is always received by the PHP core. It then starts a series of operations to process the action. If the action you are currently developing is marked as a Note action, the Core will hand off the UI creation responsibility to JavaScript.

The connection itself is done using Redis. Our testing benchmarks proved that using a pub/sub to transmit data is the best solution for the development time that we had available. PHP will publish a configuration message to a channel on which the JavaScript library is subscribed and listening.

Here JS does its magic (we'll get to this later in the docs) and saves the response in a key in Redis. PHP then decodes the response saved in Redis and uses it to finalize the response.

Additional steps that are done on PHP's side is to traverse the whole layout and parse any External Components (will be explained later in the docs), handle images and other expensive operations that shouldn't be done by JavaScript or there is no need for reimplementation.

## <a name="architecture"></a> Architecture
The startup process of the JS library is a lot like what you will find in the PHP core. To summarize - it parses the configuration object that is passed to it and executes code based on that.

We will not go intro further details here for ideally you will never have to deal with this level of abstraction.

In order to create your first JS Appzio module/action you will need to use the `init` function which is exported by default.

This `init` function takes a single parameter which is an object containing references to the modules that you want to load. The value that is passed for each key - value pair is the actual module while the key is the identifier which will be used to fetch it.

### <a name="modules"></a> Module Structure
There is no actual convention on the structure that your mode should have by generally you want it to export references to:
* Controllers
* Models
* Views
* Themes (Nested modules)

#### <a name="controllers"></a> Controllers
Controllers are where you should handle your business logic handling. Like any traditional MVC, controllers will receive some data from the request and return a view by passing some data to it.
#### <a name="models"></a> Models
Models should ideally be only concerned with data and database management. Each model that extends the base model provided by the library will have access to a number of useful methods for managing variables and session.
#### <a name="views"></a> Views
The views are responsible for the generation of your layout. Probably most of your time will be spent in those files. Each view will have access to methods called `header`, `scroll`, `footer`, `onload` which allow the addition of components to the corresponding areas on the client screen. Each of those functions can take one or more components as arguments so you have complete freedom of how you structure your layout.

### <a name="components"></a> Components
The components are in the core of the platform and your goal as a developer using Appzio will be to compose a snappy and intuitive UI using components as building blocks.

In order to keep this core piece of functionality as simple os possible - most of the built in components are implemented using pure JavaScript functions.

You probably won't be implementing any core components but it's useful to know how it all works under the hood.

Each component uses the `Component` function. It is a higher order function that takes another function as an argument. Since all comp's are created more or less in the same way, `Component` takes care of the parsing and preparation logic and uses the function passed to it to apply the component specific logic.

For ease of use the core components are implemented using anonymous functions but feel free to use named ones if it would make your development and debugging easier.

Each component returns an object that has a `type` property (a string identifier), `style_contents` if any are specified and `content` depending on its type. Some components use arrays as `content`, some use strings.

```
const Text = Component(config =>
  Object.assign({}, { type: 'msg-plain' }, config))
```
The Text component implements a single change in its custom function - it specifies the `type` to signal the client that this is a simple text component.

The result of calling the `Component` function is then stored in the `Text` variable. It can now be called by using the arguments of the returned function by `Component`

```
const Component = fn => {
  return (content, params, styles) => {
    // ... parsing is done here

    return fn(config)
  }
}
```

## <a name="development"></a> Development and Conventions

Appzio's JS library was built with ease of development in mind. We want developers to focus most on creating mobile applications and not invest too much of their time into learning conventions. This is why our library is really unopinionated and does not depend on folder or module structure.

With that in mind there are some guidelines that you can follow to make your development easier.

As you can see the built in modules have their folders structured by responsibility. In reality you are not required to use the same structure. You can utilize splitting them depending on business logic or alphabetically if you feel like it (I advice against this particular one).

The one thing that you are required to do is the format in which you export your module to the appzio library. You should be exporting a simple object which has keys named `controllers`, `models` and `views`. Each of those keys should map to another object which will contain references to your controllers, models or views in key value pairs.

This can be achieved the easiest with an `index.js` file in the root of the module that uses `module.exports = {}` for the data. 

### <a name="themes"></a> Themes
Themes are in the core of Appzio's development process and are considered the main source of the platform's power.

You can find more information in the documentation but to summarize - each module can have multiple submodules called themes.

To understand why themes are so important we need to look at an example. Let's imagine that you create a `loginModule` which will have a basic login form with some images and social login buttons.

Chances are that you will want to use such logic and functionality on multiple apps. What you usually do with other platforms is that you copy and paste your code in the new project's workspace and go from there.

Appzio's themes allow you to create the basic functionality in the main module and rewrite or add specific logic in each app's theme.

Themes as modules have no particular structure but the exported object must follow the same structure as the module.

## <a name="building-components"></a> Building Components

In the example modules we have a separate folder called `components` where we store the components. This is the structure that we recommend but if you have decided on a different convention and separation feel free to store them any way you want.

A component represents a small piece of the client's UI. It can be a header, a button or some input field. You should try to split each screen in different components and implement it using small composable blocks.

There is no convention on what to use or how to structure the component itself. We reached the conclusion that using functions is the most useful approach but using classes is also an option. Let's look at an example component:

```
const SubmitButton = () => {
  // SubmitButton.js
  const { Button, Submit } = require('appzio-node')

  return Button('Submit', {
    onclick: Submit('submit-form'),
    style: 'btn-default;
  })

  module.exports = Button

  // view.js
  this.scroll(
    SubmitButton()
  )
}
```
Ideally you want to use the most descriptive syntax possible and hide the implementation of the components. Appzio's syntax tends to get more verbose than necessary so you want to abstract away as much as possible and improve the readability.

```
class LoginView extends View {
  render () {
    this.header(
      Text('Login', {
        style: 'header-text'
      })
    )

    this.scroll(
      // those are custom components
      TextField('email'),
      TextField('password')
    )

    this.footer(
      SubmitButton()
    )
  }
}
```

This is the most basic implementation of the Login screen UI. Something to notice is that the `header`, `scroll` and `footer` functions can take any amount of arguments to minimize visual clutter. So if you have multiple components that will be added to the same screen section it's better to add them all at once.

```
this.scroll(
  Text('Login'),
  Row([
    TextField('email'),
    TextField('password')
  ]),
  SubmitButton()
)
```
This is another way to implement what we already did in the previous example with the difference that everything will be scrollable

Writing out this is completely fine but if you have a more complicated screen at hand you will see that your structure gets out of hand fast, especially when we have to add additional parameters.
```
this.scroll(
  Text('Login', {}, {
    textAlign: 'center',
    fontWeight: 'bold'
  }),
  Row([
    TextField('email', {
      hint: 'Your email...'
    }),
    TextField('password', {
      hint: 'Your password'
    })
  ]),
  SubmitButton('Login', {
    id: 'submit-login'
  })
)
```
Trust me when I say that this quickly goes out of hand and starts looking like a Christmass tree.

The convention that we have found useful in this situation is to use components for the different parts of the UI and separate the sections in other methods on the controller.
```
getHeader () {
  return Text('Login', {}, {
    textAlign: 'center',
    fontWeight: 'bold'
  })
}
getLoginForm () {
  return Row([
    TextField('email', {
      hint: 'Your email...'
    }),
    TextField('password', {
      hint: 'Your password'
    })
  ])
}
getFooter () {
  return SubmitButton('Login', {
    id: 'submit-login'
  })
}
render () {
  this.header(this.getHeader())

  this.scroll(this.getLoginForm())

  this.footer(this.getFooter())
}
```
Of course this can be achieved by wrapping up the components in another big component so feel free to experiment and share any useful paradigms with us!

## <a name="external-components"></a> External Components
While the JS library supports a big amount of basic components that can be used out of the box it also provides access to the vast ecosystem of Appzio components developed by other developers.

In order to provide access for developers to components built by others or the pre-built Appzio UI Kit we have developed the so called External Component.

This component acts as a wrapper that provides access to any component developed in the Appzio ecosystem. Even though the component you need may not be available in the JS library, you can add it to your UI by using the External Component. It will signal to the core that this component needs additional parsing and will be constructed there.

Of course you need to pass the properties and parameters that this component expects but the arguments are pretty much standardized across the platform.

```
const { ExternalComponent } = require('appzio-node')

...

this.scroll(
  ExternalComponent({
    component: 'getComponentFormFieldText', // the method name used by the component
    params: {
      hint: 'Externally added',
      variable: 'test'
    },
    styles: {
      backgroundColor: '#000000'
    }
  })
)

...
```
The example above will create an object with a `type` key which has a value of `phpComponent`.

As we mentioned earlier in the docs, the whole layout is passed and additionally parsed by the core. During this parsing, the platform will check for any objects of type `phpComponent` and will replace them with their actual implementation.

You can find the names of the actual components and their parameters in the documentation.

This approach allows you to additionaly cut development time by reusing code, even if not implemented in the JavaScript library.