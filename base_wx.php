<?php 

/**
 * 获取 access_token
 * @param  [string] $appid [appid]
 * @param  [string] $key   [appsecret]
 * @return [string]        微信 access_token
 * @since   2016/10/29
 */
function getAccessToken($appid, $key)
{
    $get_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$key}";
    $access_token = curlRequest($get_token_url);
    $access_token = json_decode($access_token, true);    var_dump($access_token);
    $access_token = $access_token['access_token'];

    return $access_token;
}
/**
 * 用于保存与微信接口有关的各种类库
 */
class wx_library
{
    private $app_id;
    private $app_secret;
    private $access_token;  
    private $access_token_expires_time; // access_token的过期时间
    /**
     * 初始化参数
     * @param [string] $app_id     微信 app_id
     * @param [string] $app_secret 微信 app_secret
     */
    public function __construct($app_id, $app_secret)
    {
        $this->app_id       = $app_id;
        $this->app_secret   = $app_secret;
        if (!function_exists("curlRequest")) {
            require "../base_function.php";
        }
    }
    /**
     * 对外提供的接口，获取 access_token 
     * @return [string]  微信通用的 access_token
     */
    public function getAccessToken()
    {
        if (time() >= $access_token_expires_time) {
            return $this->getAccessTokenFromWeixin();
        } else {
            return $this->access_token;
        }
    }
    /**
     * 从微信获取 access_token
     * @return [string] 获取的 access_token
     */
    protected function getAccessTokenFromWeixin()
    {
        $url    =   "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->app_id}&secret={$this->app_secret}";
        $result     =   curlRequest($get_token_url);
        $result     =   json_decode($result, true);
        $access_token   =   $result['access_token'];
        $this->access_token = $access_token;
        $this->access_token_expires_time=   time() + $result['expires_in'];
        return $access_token;
    }

    /**************************************************************************************************
     * 微信素材相关
     *************************************************************************************************/
    /**
     * 获取永久素材列表
     * @param  enum    $type   素材类型
     * @param  integer $offset 偏移量
     * @param  integer $count  查询的数量，不能超过20
     * @return array   $result
     */
    public function batchgetMaterial($type, $offset=0,  $count=20)
    {
        /**
         * 对参数进行简单的校验
         */
        //类型
        $allow_types = array('image', 'video', 'voice', 'news');
        if (!in_array($type, $allow_types)) {
            return false;
        }
        //偏移量
        $offset = $offset < 0 ? 0 : intval($offset);
        // 总数
        if ($count <= 0 || $count > 20 ) {
            $count = 20;
        }
        $offset = intval($offset);

        //获取列表
        $access_token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=".$access_token;

        $data = array(
            "type" => $type,
            "offset"    => 0,
            "count"     => 1, 
            );
        $data = json_encode($data);

        $json_result = curlRequest($url, $data, true);
        $result = json_decode($json_result, true);
        return $result;
    }
    /**
     * 删除素材
     * @param  string $id 素材id
     * @return boolean    是否删除成功
     */
    public function delMaterial($id)
    {
        $url="https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=".$this->getAccessToken();
        $data = array('media_id' => $media_id);
        $data = json_encode($data, true);

        $json_result = curlRequest($url, $data, true);
        $result = json_decode($json_result, true);
        if($result['errcode']==0){
            return true;
        } else {
            return false;
        }

    }

    /*****************************************************************************************
     * 通用函数
     ****************************************************************************************/
    /**
     * 错误结果回复
     * @param  boolean  $status 状态码
     * @param  string   $msg    错误回复信息
     * @return array
     */
    private function wrongReply($status=false, $msg)
    {
        return array(
            'status'    =>  $status,
            'msg'       =>  $msg
            );
    }
    /**
     * 正确回复消息
     * @param  boolean $status 状态码
     * @param  string  $msg    提示信息
     * @return array          
     */
    private function rightReply($status=true, $msg=null)
    {
        return array(
            'status'    => $status,
            'msg'       => $msg
            );
    }

}