const fs = require('fs')

const createStylesObject = stylesPath => {
  const readFileContents = path => {
    const contents = fs.readFileSync(path, 'utf8')
    return JSON.parse(contents)
  }

  const combineStyles = (accumulator, file) => {
    const contents = readFileContents(stylesPath + file)
    return { ...accumulator, ...contents }
  }

  return fs
    .readdirSync(stylesPath)
    .reduce(combineStyles, {})
}

module.exports = createStylesObject
