const { HIDE_ELEMENT } = require('./types')

const HideElement = (elementId, params = {}) =>
  ({
    action: HIDE_ELEMENT,
    view_id: elementId,
    ...params
  })

module.exports = HideElement
