module.exports = {
	productionSourceMap : false,
	publicPath          : 'production' === process.env.NODE_ENV
		? '/wp-content/plugins/vue-wordpress-dev-plugin/dist/'
		: 'http://localhost:8080/',
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
			disableHostCheck : true
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