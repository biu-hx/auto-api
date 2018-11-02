const webpack = require('webpack');
const path = require('path');
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');//拆分css样式为独立文件
const CleanWebpackPlugin = require('clean-webpack-plugin');
var HtmlWebpackPlugin = require('html-webpack-plugin');

module.exports = {
    entry: {
        "app": './main.js',
        "fastclick": ["./src/static/lib/fastclick.js"]
    },
    output: {
        path: path.resolve(__dirname,'dist'),
        filename: '[name].js',
        chunkFilename: "[name].[hash].js"
    },
    module: {
        loaders: [
            {
                test: /\.(css|sass|scss|less)?$/,
                loader: ExtractTextPlugin.extract("style-loader", "css-loader!autoprefixer-loader!sass-loader!less-loader"),
                exclude: /node_modules/
            },
            {
                test: /\.(png|jpg|gif|svg|woff2?|eot|ttf)(\?.*)?$/,
                use: 'url-loader',
                exclude: /node_modules/
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                exclude: /node_modules/
            },
            {
                test: /\.js$/,
                loader: 'babel-loader?presets=es2015',
                exclude: /node_modules/,
                query:{
                    presets: ['es2015', 'stage-0'],  
                    plugins: ['transform-runtime']                      
                }
            },
        ]
    },
    plugins: [
        new HtmlWebpackPlugin({                               //根据模板插入css/js等生成最终HTML
            filename:'./index.html',                          //生成的html存放路径，相对于 path
            template:'./index.html',                          //html模板路径
            inject:true,                                      //允许插件修改哪些内容，包括head与body
            hash:true,                                        //为静态资源生成hash值
            minify:{                                          //压缩HTML文件
                removeComments:true,                          //移除HTML中的注释
                collapseWhitespace:false                       //删除空白符与换行符
            }
        }),
        new webpack.optimize.CommonsChunkPlugin({
            names: ["fastclick"],
            minChunks: Infinity
        }),
        new CleanWebpackPlugin(
            ['dist/ordering.*.js',],　                      //匹配删除的文件
            {
                root: __dirname,       　　　　　　　　　　//根目录
                verbose:  true,        　　　　　　　　　　//开启在控制台输出信息
                dry:      false        　　　　　　　　　　//启用删除文件
            }
        )
    ]
}
