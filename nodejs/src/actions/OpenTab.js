const { OPEN_TAB } = require('./types')

const OpenTab = (tabNumber, params = {}) =>
  ({
    action: OPEN_TAB,
    action_config: tabNumber,
    ...params
  })

module.exports = OpenTab
