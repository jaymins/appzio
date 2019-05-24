const Sequelize = require('sequelize')
const Model = require('./Model')

const PlayVariable = Model.define('ae_game_play_variable', {
  play_id: Sequelize.INTEGER,
  variable_id: Sequelize.INTEGER,
  name: Sequelize.STRING,
  value: Sequelize.STRING,
  parameters: Sequelize.STRING
}, {
  freezeTableName: true,
  timestamps: false,
  underscored: true
})

module.exports = PlayVariable
