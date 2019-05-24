const ExternalComponent = require('./ExternalComponent')

module.exports = (imgfilename, params = [], style = []) => {
  return ExternalComponent('getComponentImage', imgfilename, params, style)
}
