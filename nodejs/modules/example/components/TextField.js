const { TextField } = require('../../../src/components')

module.exports = (variable, hint) => {
  return TextField('', {
    variable,
    hint,
    input_type: variable === 'email' ? 'email' : 'text'
  }, {
    backgroundColor: '#ffffff',
    color: '#000000',
    padding: '10 10 10 10',
    margin: '10 20 0 20',
    borderRadius: 5,
    width: '100%'
  })
}
