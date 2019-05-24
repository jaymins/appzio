const { Text, Row } = require('../../../src/components')

module.exports = () => {
  return Row([
    Text('Tab 1', {}, {
      width: '50%',
      textAlign: 'center',
      backgroundColor: '#ffffff',
      padding: '20 0 20 0',
      border: 1,
      borderColor: '#f9f9f9'
    }),
    Text('Tab 2', {}, {
      width: '50%',
      textAlign: 'center',
      backgroundColor: '#ffffff',
      padding: '20 0 20 0',
      border: 1,
      borderColor: '#f9f9f9'
    })
  ])
}
