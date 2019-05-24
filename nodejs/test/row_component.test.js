const { Row, Text } = require('../src/components')
const { Submit } = require('../src/actions')
const expect = require('expect')

describe('Row Component', () => {
  it('Creates a Row that matches the snapshot', () => {
    const row = Row()
    expect(row).toMatchSnapshot()
  })
  it('Creates a row containing the given components', () => {
    const text1 = Text('test text 1')
    const text2 = Text('test text 2')

    const row = Row([ text1, text2 ])

    expect(row.row_content.length).toBe(2)
  })
  it('Creates an empty array of contents if no components are passed', () => {
    const row = Row()
    expect(row.row_content.length).toBe(0)
  })
  it('Creates a row with params and inline styles', () => {
    const text1 = Text('test text 1')
    const text2 = Text('test text 2')

    const row = Row(
      [ text1, text2 ],
      { onclick: Submit('submit') },
      { backgroundColor: '#000000' }
    )

    expect(row.onclick).toBeDefined()
    expect(row.style_content['background-color']).toEqual('#000000')
  })
})
