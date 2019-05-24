const defaultController = require('./controllers/controller')
const defaultModel = require('./models/model')
const defaultView = require('./views/index')
const viewIndex2 = require('./views/index2')

const exampleTheme = require('./themes/example')

/**
 * Each module needs to defined which files it exports.
 * The contents of the module will be accessed depending on how they are exported.
 * Therefore you should use the identifiers that the app will be looking for.
 */

module.exports = {
  controllers: {
    Controller: defaultController
  },
  models: {
    model: defaultModel
  },
  themes: {
    uiKit: exampleTheme
  },
  views: {
    index: defaultView,
      index2: viewIndex2
  }
}
