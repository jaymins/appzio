const SubmitButton = require('../components/SubmitButton')

describe('SubmitButton', () => {
  it('Matches its working state', () => {
    const button = SubmitButton()
    expect(button).toMatchSnapshot()
  })
})
