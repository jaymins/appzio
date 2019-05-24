const { Text, Column, HintedCalendar } = require('../../../src/components')
const TextField = require('./TextField')
const SubmitButton = require('./SubmitButton')
const SavedButton = require('./SavedButton')

const successMessage = () => {
  return Text('Profile saved', {}, {
    color: '#ffffff',
    fontWeight: 'bold',
    textAlign: 'center',
    margin: '10 0 0 0'
  })
}

module.exports = isSubmitted => {
  return Column([
    TextField('firstname', 'First name'),
    TextField('lastname', 'Last name'),
    HintedCalendar(),
    isSubmitted
      ? Column([
        successMessage(),
        SavedButton()
      ])
      : SubmitButton()
  ])
}
