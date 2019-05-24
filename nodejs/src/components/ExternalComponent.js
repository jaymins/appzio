const { parseObject } = require('../../utils')

module.exports = (component, content, params, style) => {
  return {
    type: 'phpComponent',
    component: component,
    content: content || '',
    params: params || [],
    style: parseObject(style || [])
  }
}
