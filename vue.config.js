const fs = require('fs')

let https = false
try {
	/*
	Generate Cert files via:
	`mkcert wp-vue.local`
	 */
	if (fs.existsSync('./wp-vue.local-key.pem')) {
		https = {
			key  : fs.readFileSync('./wp-vue.local-key.pem'),
			cert : fs.readFileSync('./wp-vue.local.pem'),
			ca   : fs.readFileSync('/Users/benjamin/Library/Application Support/mkcert/rootCA.pem')
		}
	};
} catch (err) {
	console.log(err)
}

module.exports = {
	productionSourceMap : false,
	publicPath          : 'production' === process.env.NODE_ENV
		? '/wp-content/plugins/vue-wordpress-dev-plugin/dist/'
		: 'https://wp-vue.local:8080/',
	outputDir        : 'dist',
	configureWebpack : {
		devServer : {
			contentBase  : '/wp-content/plugins/vue-wordpress-dev-plugin/dist/',
			allowedHosts : [ 'wp-vue.local' ],
			headers      : {
				'Access-Control-Allow-Origin'  : '*',
				'Access-Control-Allow-Methods' : '*',
				'Access-Control-Allow-Headers' : '*'
			},
			disableHostCheck : true,
			host             : 'wp-vue.local',
			port             : '8080',
			https
		},
		output : {
			filename      : 'js/[name].js',
			chunkFilename : 'js/[name].js'
		}
	},
	css : {
		extract : {
			filename      : 'css/[name].css',
			chunkFilename : 'css/[name].css'
		}
	}
}