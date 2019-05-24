const { SHOW_DIV } = require('./types')

const ShowDiv = (divId, params = {}, layout = {}) =>
  ({
    action: SHOW_DIV,
    action_config: divId,
    layout,
    ...params
  })

module.exports = ShowDiv
