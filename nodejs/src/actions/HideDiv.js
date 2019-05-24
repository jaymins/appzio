const { HIDE_DIV } = require('./types')

const HideDiv = (divId, params = {}) =>
  ({
    action: HIDE_DIV,
    action_config: divId,
    ...params
  })

module.exports = HideDiv
