const { View } = require('../../../index')
const Text = require('../../../src/components/Text')
const TextField = require('../../../src/components/TextField')
const SubmitButton = require('../components/SubmitButton')
const Image = require('../../../src/components/Image')

class Index2 extends View {
  render () {
    this.header(
      Text('Login', {}, {
        textAlign: 'center',
        margin: '0 0 10 0'
      })
    )

    this.scroll(
        Image('appzio.png',{},{
            'margin' : '10 40 10 40'
        }),

        Image('appzio.png',{},{
            'margin' : '10 40 10 40'
        }),

        TextField('', {
        variable: 'firstname',
        hint: 'First Name 2'
      }, {
        backgroundColor: '#ffffff',
        margin: '10 10 0 10',
        padding: '0 10 0 10',
        borderRadius: 5
      }),
      TextField('', {
        variable: 'lastname',
        hint: 'Last Name 2'
      }, {
        backgroundColor: '#ffffff',
        margin: '10 10 0 10',
        padding: '0 10 0 10',
        borderRadius: 5
      })
    )

    const isSaved = this.getData('saved')

    //if (isSaved) {
      this.scroll(Text('Login successful!', {}, { textAlign: 'center' }))
    //}

    this.footer(
      SubmitButton()
    )
  }
}

module.exports = Index2
