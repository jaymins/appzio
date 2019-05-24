const parseObject = require('../../utils').parseObject

/**
 * Components are created using partial application.
 * Partial application is a function composition pattern
 * in which we create a function in multiple steps.
 *
 * The base component function takes a function as a
 * parameter and returns a function containing the logic to
 * create a component. The function that is takes as a parameter
 * is passed from the "child" components to specify their
 * specific behavior.
 *
 * @param {*} fn
 */
const Component = fn => {
  return (content, params, styles) => {
    let config = parseObject(params)
    config.content = content

    if (!config.style) {
      config.style_content = parseObject(styles)
    }

    return fn(config)
  }
}

module.exports = Component
