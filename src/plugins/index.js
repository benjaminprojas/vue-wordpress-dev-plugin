import Vue from 'vue'

import superagent from 'superagent'

const http = superagent.agent()

Vue.prototype.$http = http