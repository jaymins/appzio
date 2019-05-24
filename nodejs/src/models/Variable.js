const Sequelize = require('sequelize')
const Model = require('./Model')

const Variable = Model.define('ae_game_variable', {
  game_id: Sequelize.INTEGER,
  name: Sequelize.STRING,
  used_by_actions: Sequelize.INTEGER,
  set_on_players: Sequelize.STRING,
  value_type: Sequelize.STRING
}, {
  freezeTableName: true,
  timestamps: false,
  underscored: true
})

module.exports = Variable
