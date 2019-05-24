const Text = require('./Text')

const VerticalSpacer = (width = 10, params, styles) => {
  return Text('', params, { width, ...styles })
}

module.exports = VerticalSpacer
