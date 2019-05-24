const Component = require('./Component')

const Div = Component(config =>
  Object.assign({}, config, { type: 'div', div_id: config.content, content: null }))

module.exports = (divId = '', params = {}, styles = {}) =>
  Div(divId, params, styles)
