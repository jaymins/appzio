const Calendar = require('./Calendar')
const Column = require('./Column')
const Text = require('./Text')
const { ShowElement, HideElement } = require('../actions/')

const closedCalendar = () => {
  return Text('03/03/2018', {
    id: 'calendar-closed',
    onclick: [
      HideElement('calendar-closed'),
      ShowElement('calendar-opened')
    ]
  })
}

const openedCalendar = () => {
  return Column([
    Text('03/03/2018'),
    Calendar({
      update_on_entry: true,
      variable: 'date',
      date_format: 'MM / dd / yyyy',
      date: 1520426189,
      onselect: [
        HideElement('calendar-opened'),
        ShowElement('calendar-closed')
      ]
    }, {
      margin: '0 40 0 40',
      selection_style: {
        color: '#ffffff',
        backgroundColor: '#0000ff'
      }
    })
  ], {
    id: 'calendar-opened',
    visibility: 'hidden'
  })
}

module.exports = () => {
  return Column([
    closedCalendar(),
    openedCalendar()
  ])
}
