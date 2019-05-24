/**
 * Validate whether the passed value is numeric.
 *
 * @param {*} value
 * @returns {bool}
 */
const isNumeric = value =>
  !isNaN(parseFloat(value)) && isFinite(value)

/**
 * Accepts a function as argument and returns it with
 * a catch handler attached. This provides the ability to skip
 * adding catch to every async function by wrapping it into handleErrors.
 *
 * @param {*} fn
 */
const handleErrors = fn => () =>
  fn.catch(err => console.error(`An error occured: ${err}`))

/**
 * Parses a string from pascal to kebap case. This is required
 * from the backend so it can understand certain properties.
 *
 * @param {*} string
 */
const pascalToKebapCase = string =>
  string
    .replace(/(?:^|\.?)([A-Z])/g, (x, y) => '-' + y.toLowerCase())
    .replace(/^-/, '')

/**
 * Used in components to Map the passed object
 * to use kebap-cased indices.
 *
 * @param {*} obj
 */
const parseObject = obj => {
  let parsed = {}

  Object.keys(obj)
    .map(index => {
      parsed[pascalToKebapCase(index)] = obj[index]
    })

  return parsed
}

/**
 * Capitalizes the first letter of a string.
 *
 * @param {*} str
 */
const capitalizeFirstLetter = str =>
  str.charAt(0).toUpperCase() + str.slice(1)

/**
 * Check whethe given object is empty.
 *
 * @param {*} obj
 */
const isEmptyObject = obj =>
  Object.keys(obj).length === 0 && obj.constructor === Object

/**
 * Check whether a property exists on the given object.
 * Returns null if the property is not found.
 *
 * If only the object is specified the function returns a
 * partially applied function that expects the property name to be given.
 * This is useful in different cases in which we want to check an object
 * multiple times or have a more verbose syntax in our code.
 *
 * @param {*} obj
 * @param {*} property
 */
const propertyExists = (obj, property) => {
  if (!property) {
    return function (property) {
      return obj.hasOwnProperty(property)
        ? property
        : null
    }
  }

  return obj.hasOwnProperty(property)
    ? property
    : null
}

/**
 * Split the passed route as an array of controller name, method and menu id.
 * They should always be passed in this order in order to be able to utilize
 * pattern matching.
 *
 * If order changes the functionality won't produce the expected result.
 *
 * @param {*} route
 */
const splitRoute = route => {
  route = route || ''

    let [ controller, method, menuId ] = route.toString().split('/')

  if (controller && !method && !menuId) {
    menuId = controller
    return [ undefined, undefined, menuId ]
  }

  return [ controller, method || 'default', menuId || null ]
}

const getRouteFromPayload = payload => {
    const session_storage = payload.session_storage
    const actionId = payload.action.id
    const menuId = payload.menuid

    const persistedRoute = session_storage[`persist_route_${actionId}`]

    return menuId || persistedRoute
  //return menuId || persistedRoute
}

const applyMode = ([ controller, ...args ]) => {
  return mode => {
    return [ mode || controller, ...args ]
  }
}

module.exports = {
  isNumeric,
  handleErrors,
  pascalToKebapCase,
  parseObject,
  capitalizeFirstLetter,
  isEmptyObject,
  propertyExists,
  splitRoute,
  getRouteFromPayload,
  applyMode
}
