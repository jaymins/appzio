const redis = require('redis')
const subscriber = redis.createClient()
const { propertyExists, splitRoute, getRouteFromPayload, applyMode } = require('./utils')

const extractFieldsFromController = controllerInstance => {
  const { model: {
      rewriteactionfield,
      rewriteconfigs,
      session_storage,
      vars,
      varcontent,
      submitvariables
    }} = controllerInstance

  return { rewriteactionfield, rewriteconfigs, session_storage, vars, varcontent, submitvariables }
}

const start = (modules = {}) => {
  const logSubscribed = () => console.log('\x1b[32m%s\x1b[0m', `Appzio Node is running!`)

  const getModule = moduleName => {
    console.log('\x1b[33m%s\x1b[0m', `Module name: ${moduleName}`)

    const currentModule = modules[moduleName]

    if (!currentModule) {
      const errorMessage = `Module "${moduleName}" not found! \n`
      console.log('\x1b[31m%s\x1b[0m', errorMessage)
      throw Error(errorMessage)
    }

    return currentModule
  }

  const storeResponseInRedis = (response, identifier) => {
    redis
    .createClient()
    .set(
      `Yii.redis.${identifier}`,
      JSON.stringify(response),
      redis.print
    )
  }

  const handleMessage = body => {
    console.log('\x1b[32m%s\x1b[0m', `Message received!`)

    // Prepare data -> get what we need out of the payload body
    const payload = JSON.parse(body)
    const { theme, action: { permaname }, mode } = payload

    // Split the route and get the current module and active theme module
    const [ controller, method, menuId ] = applyMode(splitRoute(getRouteFromPayload(payload)))(mode)

      const shortName = payload.action.shortname.replace('node', '', payload.action.shortname)
      const currentModule = getModule(shortName)
    const themeModule = currentModule.themes[theme] || { controllers: {}, views: {} }

    // Validate that the called controller exists and is being exported
    const controllerExists = propertyExists(currentModule.controllers)
    const themeControllerExists = propertyExists(themeModule.controllers)

    const getControllerName = name =>
      themeControllerExists(controller) ||
      controllerExists(controller) ||
      'Controller'

    const controllerName = getControllerName(controller)

    // Instantiate the controller and view depending on controller output
    const ControllerClass = themeModule.controllers[controllerName] ||
    currentModule.controllers[controllerName]

      const controllerInstance = new ControllerClass({ ...payload, menuid: menuId })
      const [ viewName, viewData ] = controllerInstance[method]()
/*
      console.log('viewName'+viewName)
      console.log('got here'+menuId)
*/


      const ViewClass = themeModule.views[viewName] ||
      currentModule.views[viewName]
      //console.log('4')

    const viewInstance = new ViewClass(viewData)
    const { layout } = viewInstance
/*
      console.log('5')
      console.log('view'+ViewClass)
      console.log('layout')
      console.log(layout)
*/


      // Prepare response data and store it in Redis
    const controllerFields = extractFieldsFromController(controllerInstance)

    const response = {
      layout,
      ...controllerFields
    }

    storeResponseInRedis(response, permaname)
  }

  subscriber.on('subscribe', logSubscribed)

  subscriber.on('message', (connection, body) => {
    try {
      handleMessage(body)
    } catch (error) {
      const { permaname } = JSON.parse(body)
      storeResponseInRedis({ error }, permaname)
    }
  })

  subscriber.subscribe('Yii.redis.Channel:js')
}

module.exports = start
