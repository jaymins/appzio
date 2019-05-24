const { Text, Row } = require('../../../src/components')
const { Submit } = require('../../../src/actions')

module.exports = () => {
  return Row([
    Text('Submit', {
      id: 'submit-btn',
      onclick: Submit('default/default/test')
    }, {
      color: '#ffffff',
      backgroundColor: '#ffcc00',
      width: '100%',
      textAlign: 'center',
      padding: '10 20 10 20',
      margin: '10 20 0 20',
      borderRadius: 5
    })
  ], {}, {
    textAlign: 'center'
  })
}
