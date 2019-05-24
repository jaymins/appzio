const REQUIRED = 'required'
const EMAIL = 'email'

/**
 * Returns a string containing a descriptive error
 * message depending on the type of error.
 *
 * @param {*} rule
 */
const getErrorMessage = rule => {
  switch (rule) {
    case REQUIRED:
      return `This field is required.`
    case EMAIL:
      return `This is not a valid email`
    default:
      return `This field is invalid.`
  }
}

module.exports = getErrorMessage
