const Component = require('./Component')

const Column = Component(config =>
  Object.assign({}, config, { type: 'column', column_content: config.content, content: null }))

module.exports = (content = [], params = {}, styles = {}) =>
  Column(content, params, styles)
