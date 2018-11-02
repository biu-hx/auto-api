/**
 * Created by chenlp on 2017/3/22.
 */
// nodejs 中的path模块
var path = require('path');
var autoprefixer = require('autoprefixer');
module.exports = {
    // 入口文件，path.resolve()方法，可以结合我们给定的两个参数最后生成绝对路径，最终指向的就是我们的index.js文件
    entry: { index: "./main.js" },
    // 输出配置
    output: {
        path: './dist/js/',  //路径
        publicPath: '/',  //网站运行时访问的路径
        filename: '[name].js' // 生成文件名
    },
    module: {

        loaders: [
            // 使用vue-loader 加载 .vue 结尾的文件
            {
                test: /\.vue$/,
                loader: 'vue'
            },
            {
                test: /iview\/.*?js$/,
                loader: 'babel-loader'
            },
            {
                test: /\.js$/,
                loader: 'babel?presets=es2015',
                exclude: /node_modules/
            },
            {
                test: /\.(gif|jpg|png|woff|svg|eot|ttf)\??.*$/,
                loader: 'url-loader'
            },
            {
                test: /\.css$/,
                loader: "style-loader!css-loader!postcss-loader"
            }

        ]
    },
    postcss: [autoprefixer({ browsers: ['last 2 versions'] })],
    externals: [
        { "./node_modules/vue/dist/vue.min.js": "Vue" }
    ],
    resolve: {
        extensions: ['', '.js', '.vue'],
        alias: {
            'vue$': 'vue/dist/vue.common.js'
        }
    }
}