const required = require('./required')
const email = require('./email')
const messages = require('./messages')

/**
 * This is the default entry point for Appzio's built in validation.
 * For ease of access, all validation options should be exported here
 * in order for the built in function to work as expected.
 *
 * This also provides access to the getErrorMessage function which
 * returns a string containing a descriptive error depending on the type.
 * If you are creating your own validation method you should add a key and
 * a message inside that function. If you don't have such it will default
 * to a generic one.
 */
module.exports = {
  required,
  email,
  getErrorMessage: messages
}
