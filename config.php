<?php
//pro-生产环境
//dev-开发环境
$env='pro';
if ($env == 'dev') {
    // 示例的全局数据库配置文件
    return array(
         'DEFAULT_DB'=>array(
            'DB_HOST'=>'weishi.db',
            'DB_NAME'=>'ws_api',
            'DB_USER'=>'wsdb',
            'DB_PWD'=>'weishi4us!',
            'DB_PORT'=>'3306',
            'DB_PREFIX'=>"",
        ),
        'URLTYPE'=>"rewrite",
        'OPEN_SQL_LOG'=>true,//是否记录sql语句
        'LOG_DEBUG'=>TRUE,//显示调试日志,

    );
}else if($env=="pro"){
    // 示例的全局数据库配置文件
     return array(
         'DEFAULT_DB'=>array(
            'DB_HOST'=>'weishi.db',
            'DB_NAME'=>'ws_api',
            'DB_USER'=>'wsdb',
            'DB_PWD'=>'weishi4us!',
            'DB_PORT'=>'3306',
            'DB_PREFIX'=>"",
        ),
        'URLTYPE'=>"rewrite",
         'APPID'=>"wx33c7e7805359dfb1",
        'APPSECRET'=>"2f85cb22ec4dd6beb8100dcbd0ae0f45",
        'OPEN_SQL_LOG'=>true,//是否记录sql语句
        'LOG_DEBUG'=>TRUE,//显示调试日志

    );
}
