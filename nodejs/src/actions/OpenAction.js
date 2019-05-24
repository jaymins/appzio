const { OPEN_ACTION } = require('./types')

const OpenAction = (actionId, params) =>
  ({
    action: OPEN_ACTION,
    action_config: actionId,
    ...params
  })

module.exports = actionId => OpenAction(actionId)
