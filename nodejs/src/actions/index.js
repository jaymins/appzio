const Submit = require('./Submit')
const OpenAction = require('./OpenAction')
const OpenTab = require('./OpenTab')
const ShowDiv = require('./ShowDiv')
const HideDiv = require('./HideDiv')
const ShowElement = require('./ShowElement')
const HideElement = require('./HideElement')

/**
 * This is the default entrypoint for all Appzio actions.
 * This file's purpose is to export the actions that should
 * be made available to people using the library. If an action
 * is added but not exported here it won't be available.
 *
 */
const actions = {
  Submit,
  OpenAction,
  OpenTab,
  ShowDiv,
  HideDiv,
  ShowElement,
  HideElement
}

module.exports = actions
