const Component = require('./Component')

const TextField = Component(config => {
  return Object.assign({}, { type: 'field-text' }, config)
})

module.exports = (content = '', params = {}, styles = {}) =>
  TextField(content, params, styles)
