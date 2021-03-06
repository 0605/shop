<?php
class MsgCustomizedcast{
    public function exec_android($data){
        require_once(APP_ROOT_PATH. 'system/umeng/notification/android/AndroidCustomizedcast.php');
        try {
            $appMasterSecret = $GLOBALS['db']->getOne("select val from ".DB_PREFIX."m_config where code = 'android_master_secret'");
            $appkey = $GLOBALS['db']->getOne("select val from ".DB_PREFIX."m_config where code = 'android_app_key'");


            $title = app_conf("SHOP_TITLE");


            $customizedcast = new AndroidCustomizedcast();
            $customizedcast->setAppMasterSecret($appMasterSecret);
            $customizedcast->setPredefinedKeyValue("appkey",           $appkey);
            $customizedcast->setPredefinedKeyValue("timestamp",        strval(time()));// 必填 时间戳，10位或者13位均可，时间戳有效期为10分钟 NOW_TIME
            // Set your device tokens here
            $customizedcast->setPredefinedKeyValue("alias",$data['alias']);
            $customizedcast->setPredefinedKeyValue("alias_type",$data["alias_type"]);
            $customizedcast->setPredefinedKeyValue("ticker",           $data['content']);//必填 通知栏提示文字
            $customizedcast->setPredefinedKeyValue("title",            $title);// 必填 通知标题
            $customizedcast->setPredefinedKeyValue("text",             $data['content']);// 必填 通知文字描述
            $customizedcast->setPredefinedKeyValue("after_open",       "go_app");//"go_app": 打开应用;"go_url": 跳转到URL;"go_activity": 打开特定的activity;"go_custom": 用户自定义内容。
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $customizedcast->setPredefinedKeyValue("production_mode", "true");//可选 正式/测试模式。测试模式下，只会将消息发给测试设备。
            // Set extra fields
            //$unicast->setExtraField("test", "helloworld");
            //print("Sending unicast notification, please wait...\r\n");
            //json_decode($data) {"ret":"SUCCESS","data":{"msg_id":"uu05362143574400482600"}}
            $result = $customizedcast->send();
            //print_r($result);
            $res = json_decode($result,1);
            //print("Sent SUCCESS\r\n");
            if ($res['ret'] == 'SUCCESS'){
                $is_success = 1;
            }else{
                $is_success = 0;
                $message = addslashes(print_r($result,true));
            }

        } catch (Exception $e) {
            $is_success = 0;
            $message = addslashes($e->getMessage());

        }

        $result = array();
        $result['status'] = $is_success;
        $result['attemp'] = 0;
        $result['info'] = $message;
        return $result;
    }
    public function exec_ios($data){


        require_once(APP_ROOT_PATH. 'system/umeng/notification/ios/IOSCustomizedcast.php');

        try {
            $appMasterSecret = $GLOBALS['db']->getOne("select val from ".DB_PREFIX."m_config where code = 'ios_master_secret'");
            $appkey = $GLOBALS['db']->getOne("select val from ".DB_PREFIX."m_config where code = 'ios_app_key'");


            $customizedcast = new IOSCustomizedcast();
            $customizedcast->setAppMasterSecret($appMasterSecret);
            $customizedcast->setPredefinedKeyValue("appkey",           $appkey);
            $customizedcast->setPredefinedKeyValue("timestamp",        strval(time()));
            // Set your device tokens here
            $customizedcast->setPredefinedKeyValue("alias",$data['alias']);
            $customizedcast->setPredefinedKeyValue("alias_type",$data["alias_type"]);
            $customizedcast->setPredefinedKeyValue("alert", $data['content']);
            $customizedcast->setPredefinedKeyValue("badge", 1);
            $customizedcast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $customizedcast->setPredefinedKeyValue("production_mode", "true");
            $result = $customizedcast->send();

            $res = json_decode($result,1);
            //print("Sent SUCCESS\r\n");
            if ($res['ret'] == 'SUCCESS'){
                $is_success = 1;
            }else{
                $is_success = 0;
                $message = addslashes(print_r($result,true));
            }

        } catch (Exception $e) {
            $is_success = 0;
            $message = strim($e->getMessage());
//            return false;
        }

        $result = array();
        $result['status'] = $is_success;
        $result['attemp'] = 0;
        $result['info'] = $message;
        return $result;
    }
}