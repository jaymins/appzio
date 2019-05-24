#!/usr/bin/env node
const program = require('commander')
const fs = require('fs')

program
  .arguments('<module>')
  .action(moduleName => {
    const dirPath = `./modules/${moduleName}`

    if (!fs.existsSync(dirPath)) {
      fs.mkdirSync(dirPath)
      fs.mkdirSync(`${dirPath}/controllers`)
      fs.mkdirSync(`${dirPath}/models`)
      fs.mkdirSync(`${dirPath}/views`)
      fs.mkdirSync(`${dirPath}/components`)

      fs.writeFileSync(`${dirPath}/controllers/controller.js`, 'console.log()')
      fs.writeFileSync(`${dirPath}/views/view.js`, 'console.log()')
      fs.writeFileSync(`${dirPath}/models/model.js`, 'console.log()')

      console.log('\x1b[33m%s\x1b[0m', `Module ${moduleName} created successfully!`)
    } else {
      console.log('\x1b[31m%s\x1b[0m', `Module "${moduleName}" already exists!`)
    }
  })
  .parse(process.argv)
