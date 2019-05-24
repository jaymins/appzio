const Model = require('./Model')
const validation = require('./validation')

/**
 * Main bootstrap controller containing the basic functionality for controllers
 */

class Controller {
  constructor (props = {}) {
    Object
      .keys(props)
      .map(key => {
        this[key] = props[key]
      })

    this.model = new Model(props)
  }

  getMenuId () {
    return this.menuid
  }

  validate (input, rules) {
    return Object
      .keys(rules)
      .reduce((accumulator, current) => {
        const value = input[current]
        const validationRules = typeof rules[current] === 'string'
          ? [rules[current]] : rules[current]

        const validationErrors = validationRules
          .map(rule =>
            validation[rule](value)
              ? undefined : validation.getErrorMessage(rule)
          )
          .filter(error => error !== undefined)

        if (!validationErrors.length) return accumulator

        accumulator[current] = validationErrors
        return accumulator
      }, {})
  }
}

module.exports = Controller