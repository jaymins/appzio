const {
  isNumeric
} = require('../utils')
const PlayVariable = require('./models/PlayVariable')
const Variable = require('./models/Variable')
const Sequelize = require('sequelize')
const Op = Sequelize.Op

class Model {
  constructor (props) {
    Object
      .keys(props)
      .map(key => {
        this[key] = props[key]
      })
  }

  /**
   * Get variable by id or name
   * @param {*} variableId
   */
  getVariable (variableId) {
    const variables = Object.assign({},
      this.submitvariables,
      this.varcontent,
      this.varconent_byid
    )

    return variables[variableId]
  }

  /**
   * Save variable
   * @param {*} variable
   * @param {*} value
   */
  async saveVariable (variable, value) {
    await this.loadVariables()
    let variableId = isNumeric(variable) ? variable : this.getVariableId(variable)

    if (!variableId && value) {
      const createdVariable = await Variable.create({
        game_id: this.appid,
        name: variable
      })
      variableId = createdVariable.dataValues.id
    }

    PlayVariable
      .findOrCreate({
        where: {
          play_id: this.playid,
          variable_id: variableId
        }
      })
      .spread((variable, created) => {
        variable.updateAttributes({
          value
        })
      })
  }

  /**
   * Delete a variable by name
   * @param {*} name
   */
  async deleteVariable (name) {
    const variable = await Variable.findOne({
      where: {
        [Op.and]: [{
          name
        },
        {
          play_id: this.playid
        }]
      }
    })
    PlayVariable
      .destroy({
        where: {
          variable_id: variable.id,
          play_id: this.playid
        }
      })
  }

  /**
   * Get all submitted variables
   */
  getAllSubmittedVariables () {
    return this.submitvariables
  }

  getAllSubmittedVariablesByName () {
    let variables = []

    Object
      .keys(this.submitvariables)
      .map((variableId) => {
        variables[this.getVariableName(variableId)] = this.submitvariables[variableId]
      })

    return variables
  }

  /**
   * Get a submitted variable by it's name
   * @param {*} name
   */
  getSubmittedVariableByName (name) {
    const variableId = this.getVariableId(name)
    return this.submitvariables[variableId]
      ? this.submitvariables[variableId] : this.submitvariables[name]
  }

  /**
   * Get variable id by name
   * @param {*} name
   */
  getVariableId (name) {
    const variable = Object.values(this.vars).find(variable => variable.name === name)
    return variable !== undefined
      ? variable.id : false
  }

  getVariableName (id) {
    return Object
      .keys(this.vars)
      .find(key => this.vars[key] === id)
  }

  /**
   * Load all variables for current game
   */
  async loadVariables () {
    const variables = await Variable.findAll({
      where: {
        game_id: this.appid
      }
    })
    Object
      .keys(variables)
      .map(index => (this.vars[index] = variables[index].dataValues))
  }

  /**
   * Rewrite a configuration field for the action.
   *
   * @param {*} field
   * @param {*} value
   */
  rewriteConfigField (field, value) {
    this.rewriteconfigs[field] = value
  }

  /**
   * Rewrite a specific action field.
   *
   * @param {*} field
   * @param {*} value
   */
  rewriteActionField (field, value) {
    this.rewriteactionfield[field] = value
  }

  /**
   * Get an action's id by its permaname
   *
   * @param {*} name
   */
  getActionIdByPermaname (name) {
    return this.permanames[name]
  }

  /**
   * Set a value in session storage.
   *
   * @param {*} key
   * @param {*} value
   */
  sessionSet (key, value) {
    this.session_storage[key] = value
  }

  /**
   * Get a value from session storage.
   *
   * @param {*} key
   * @param {*} defaultValue
   */
  sessionGet (key, defaultValue = null) {
    return this.session_storage[key]
      ? this.session_storage[key] : defaultValue
  }
}

module.exports = Model
