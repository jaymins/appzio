const { SHOW_ELEMENT } = require('./types')

const ShowElement = (elementId, params = {}) =>
  ({
    action: SHOW_ELEMENT,
    view_id: elementId,
    ...params
  })

module.exports = ShowElement
