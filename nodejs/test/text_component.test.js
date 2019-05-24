const { Text } = require('../src/components')
const { Submit } = require('../src/actions')
const expect = require('expect')

describe('Text Component', () => {
  it('Creates a Text component that matches the snapshot', () => {
    const text = Text('test text', { onclick: Submit('test') }, { color: '#000', fontSize: 14 })
    expect(text).toMatchSnapshot()
  })
  it('Creates a text with the given text', () => {
    const content = 'Test text'
    const text = Text(content)

    expect(text.type).toEqual('msg-plain')
    expect(text.content).toEqual(content)
  })
  it('Creates a text with the given params', () => {
    const content = 'Test text'
    const params = { id: 'test' }
    const text = Text(content, params)

    expect(text.id).toEqual('test')
  })
  it('Creates a text with inline styles', () => {
    const content = 'Test text'
    const styles = {
      fontSize: 14,
      color: '#000000'
    }
    const text = Text(content, {}, styles)

    expect(text.style_content).toEqual({
      'font-size': 14,
      'color': '#000000'
    })
  })
  it('Creates a text, disregarding inline styles if style param is passed', () => {
    const content = 'Test text'
    const styles = {
      fontSize: 14,
      color: '#000000'
    }
    const text = Text(content, { style: 'test_text_style' }, styles)
    expect(text.style_content).toBeUndefined()
    expect(text.style).toEqual('test_text_style')
  })
})
