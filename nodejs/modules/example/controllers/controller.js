const { Controller } = require('../../../index')
const { isEmptyObject } = require('../../../utils')

class Default extends Controller {
  handleFormSubmission (input) {
    const validationErrors = this.validate(input, {
      firstname: 'required',
      lastname: 'required'
    })

    if (isEmptyObject(validationErrors)) {
      const [ firstname, lastname ] = input

      this.model.saveVariable('firstname', firstname)
      this.model.saveVariable('lastname', lastname)

      this.viewData.saved = true
    }
  }

  /**
   * This is the default method that will be called on the
   * controller if nothing else is specified, or the method does
   * not exist.
   *
   * In this example, when the form is submitted it will execute
   * the same method (if not specified otherwise).
   *
   * We can handle additional cases or execute different logic
   * depending on the menuId that is passed.
   */
  default () {
    this.viewData = {}

    if (this.getMenuId() === 'test') {
      const input = this.model.getAllSubmittedVariablesByName()
      this.handleFormSubmission(input)
    }

    return ['index', this.viewData]
  }
}

module.exports = Default
