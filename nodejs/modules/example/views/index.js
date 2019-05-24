const { View } = require('../../../index')
const Text = require('../../../src/components/Text')
const TextField = require('../../../src/components/TextField')
const SubmitButton = require('../components/SubmitButton')

class Index extends View {
  /**
   * This is the method called by Appzio to prepare the
   * layout of the module.
   */
  render () {
    this.header(
      Text('Login', {}, {
        textAlign: 'center',
        margin: '0 0 10 0'
      })
    )

    this.scroll(
      TextField('', {
        variable: 'firstname',
        hint: 'First Name'
      }, {
        backgroundColor: '#ffffff',
        margin: '10 10 0 10',
        padding: '0 10 0 10',
        borderRadius: 5
      }),
      TextField('', {
        variable: 'lastname',
        hint: 'Last Name'
      }, {
        backgroundColor: '#ffffff',
        margin: '10 10 0 10',
        padding: '0 10 0 10',
        borderRadius: 5
      })
    )

    const isSaved = this.getData('saved')

    if (isSaved) {
      this.scroll(Text('Login successful!', {}, { textAlign: 'center' }))
    }

    this.footer(
      SubmitButton()
    )
  }
}

module.exports = Index
