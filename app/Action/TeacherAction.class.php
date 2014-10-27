<?php

/**
 *
 */
class TeacherAction extends Action {

    public function detail(){
        $data = array(
            "profile"=>array(
                "name" => "周佳",
                "sex" => "0",
                "number" => "371042818",
                "avatar_url" => "http://test.img.genshuixue.com/1349_jgvwnob7.jpeg",
                "user_id" => "495",
                "space_url" => "http://127.0.0.1:8081/t/zhoujiah",
                "short_introduce" => "天道酬勤",
                "institution" => "教育局",
                "other_info" => "哈哈哈那些年想你呢想你呢那些年你想呢那你呢想你呢你。。年休假电脑教学呢年休假的教学呢山口山没什么我看.基督教嗯哪携家带口。基督教室内设计可喜欢吃点吧你即小见大你觉得快上课",
                "praise_times" => "0",
                "view_times" => 347,
                "favourite_times" => "0",
                "shared_times" => "0",
                "rating_total" => "17",
                "private_domain" => "zhoujiah",
                "address" => "北京-房山区-周口店",
                "student_count" => "9",
                "school_age" => "5",
                "rate" => "1.00",//评分率
                "teach_time" => 0, //教学时间
                "stars" => "0.0",//星级
                "max_money" => "500",//最大金额
                "min_money" => "1",//最小金额
            )

        );

        $this->assign("tpl_data",$data);
        $this->display("detail.html");
    }

}
