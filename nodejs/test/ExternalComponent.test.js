const ExternalComponent = require('../src/components/ExternalComponent')
const { parseObject } = require('../utils')

describe('ExternalComponent', () => {
  it('Should create an object with phpComponent type', () => {
    const component = ExternalComponent({ component: 'getComponentText' })
    expect(component.type).toEqual('phpComponent')
  })
  it('Should create an object depending on the passed object', () => {
    const params = {
      variable: 'var'
    }

    const component = ExternalComponent({
      component: 'getComponentText',
      content: 'This is some sample text',
      params: params
    })

    expect(component.content).toEqual('This is some sample text')
    expect(component.params).toEqual(params)
  })
  it('Should parse style object to be understandable by the device', () => {
    const style = {
      backgroundColor: '#ffffff',
      color: '#000000',
      fontSize: 14
    }

    const component = ExternalComponent({
      component: 'getComponentText',
      content: 'This is some sample text',
      style
    })

    expect(component.style).toEqual(parseObject(style))
  })
  it('Should match a working snapshot', () => {
    const component = ExternalComponent({
      component: 'getComponentFormFieldText',
      content: '',
      params: {
        hint: 'First Name'
      },
      style: {
        backgroundColor: '#ffffff'
      }
    })

    expect(component).toMatchSnapshot()
  })
})
