class View {
  constructor (data) {
    this.data = data
    this.layout = {
      onload: [],
      header: [],
      scroll: [],
      footer: [],
      divs: []
    }
    this.render()
  }
  render () {
    // To be implemented in child classes
  }
  header (...components) {
    this.addComponents(components, 'header')
  }
  scroll (...components) {
    this.addComponents(components, 'scroll')
  }
  footer (...components) {
    this.addComponents(components, 'footer')
  }
  div (...components) {
    this.addComponents(components, 'divs')
  }
  onload (...components) {
    this.addComponents(components, 'onload')
  }
  addComponents (components, section) {
    components.map(x => this.layout[section].push(x))
  }
  getData (key) {
    return this.data[key]
  }
}

module.exports = View
