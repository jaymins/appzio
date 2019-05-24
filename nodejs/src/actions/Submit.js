const { SUBMIT } = require('./types')

const Submit = id => ({ id, action: SUBMIT })

module.exports = id => Submit(id)
