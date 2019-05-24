const Component = require('./Component.js')

const Text = Component(config =>
  Object.assign({}, { type: 'msg-plain' }, config))

module.exports = (content = '', params = {}, styles = {}) =>
  Text(content, params, styles)
