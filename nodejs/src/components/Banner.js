module.exports = (...args) => {
  const { params, style } = args
  return {
    type: 'phpComponent',
    component: 'getComponentText',
    params,
    style
  }
}
