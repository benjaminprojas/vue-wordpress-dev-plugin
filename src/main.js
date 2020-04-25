import Vue from 'vue'
import App from './App.vue'

import '@/plugins'

Vue.config.productionTip = false

document.addEventListener('DOMContentLoaded', () => {
	if (window.wpVue.posts) {
		window.wpVue.posts.forEach(post => {
			new Vue({
				render : h => h(App, {
					props : {
						post
					}
				})
			}).$mount(`#${post.columnName}-${post.id}`)
		})
	}
})