const mode = ['local', 'dev', 'master']

const modeKey = 0;

// const baseURL = 'http://127.0.0.1/vue_financa/api-laravel-5.8'
// const baseURL = 'https://niinoocode.net/api/nn-crm-dev'
// const baseURL = 'https://niinoocode.net/api/nn-crm-master'

// publicPath: '/nn-crm-dev',
// publicPath: '/nn-crm',

const arrMode = {
  local: {
    baseURL:    'http://127.0.0.1/vue_financa/api-laravel-5.8',
    publicPath: 'nn-crm-desenv',
  },
  dev: {
    baseURL:    'https://niinoocode.net/api/nn-crm-dev',
    publicPath: 'nn-crm-dev',
  },
  master: {
    baseURL:    'https://niinoocode.net/api/nn-crm-master',
    publicPath: 'nn-crm',
  },
}

const keys   = Object.keys(arrMode)
const values = Object.values(arrMode)
const key    = keys.indexOf(mode[modeKey])

export default values[key];