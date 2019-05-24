const init = require('./init')
const Components = require('./src/components')
const Actions = require('./src/actions')
const Controller = require('./src/controller')
const Model = require('./src/model')
const View = require('./src/view')

module.exports = {
  init,
  Controller,
  Model,
  View,
  ...Components,
  ...Actions
}
