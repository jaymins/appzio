const Text = require('./Text')

const HorizontalSpacer = (height = 10, params, styles) => {
  return Text('', params, { height, ...styles })
}

module.exports = HorizontalSpacer
