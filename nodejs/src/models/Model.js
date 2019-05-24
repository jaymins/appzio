const Sequelize = require('sequelize')

const model = new Sequelize('aecore', 'root', 'root', {
  host: 'localhost',
  dialect: 'mysql',
  pool: {
    max: 5,
    min: 0,
    idle: 10000
  }
})

module.exports = model
