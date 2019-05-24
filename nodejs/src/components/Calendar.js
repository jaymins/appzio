const Component = require('./Component.js')

const Calendar = Component(config =>
  Object.assign({}, { type: 'calendar' }, config))

module.exports = (params = {}, styles = {}) =>
  Calendar('', params, styles)
