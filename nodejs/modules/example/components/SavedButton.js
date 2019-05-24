const { Text, Row } = require('../../../src/components')

module.exports = () => {
  return Row([
    Text('Saved!', {
      id: 'saved-btn'
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
