const Component = require('./Component')

const Row = Component(config =>
  Object.assign({}, config, { type: 'row', row_content: config.content, content: null }))

module.exports = (content = [], params = {}, styles = {}) =>
  Row(content, params, styles)
