// config.js

const fs            = require('fs')
const path          = require('path')
const yaml          = require('js-yaml')

// -------------------------------------------------------------------

const DEFAULT_CONFIG = {
    title: 'Site',

    sourceDir: 'site',
    outputDir: 'build',

    customVariables: {},

    tocPath: 'docs/_toc.yml',
    pageTemplatePath: './theme/doc-page-template.html',

    assetsDir: './theme/_assets',
    assetsSourceDir: './theme/assets-src'
}

// -------------------------------------------------------------------

function getConfig() {
    let configPath = path.resolve('./config.yml')

    let config = {}

    try {
        // Get the config file
        config = fs.readFileSync(configPath, { encoding: 'utf8' })

        // Convert the yaml to json
        config = yaml.load(config)
    } catch (e) {
        throw `Error loading config.yml: \n ${e}`
    }

    // Replace any missing config options with the defaults
    return { ...DEFAULT_CONFIG, ...config }
}

// -------------------------------------------------------------------

module.exports = getConfig()