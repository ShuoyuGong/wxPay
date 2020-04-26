<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Yansongda\Pay\Log;
use Yansongda\Pay\Pay;

class PayController extends Controller
{
  protected $config = [
    'appid' => 'wx0c2c602aa476e404', // APP APPID
    'app_id' => 'wx0c2c602aa476e404', // 公众号 APPID
    'miniapp_id' => 'wxb3fxxxxxxxxxxx', // 小程序 APPID
    'mch_id' => '1570216471',
    'key' => '73f099e9e6824678aab814a35b67f2a5',
    'notify_url' => 'http://yanda.net.cn/notify.php',
    'cert_client' => './cert/apiclient_cert.pem', // optional，退款等情况时用到
    'cert_key' => './cert/apiclient_key.pem', // optional，退款等情况时用到
    'log' => [ // optional
      'file' => './logs/wechat.log',
      'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
      'type' => 'single', // optional, 可选 daily.
      'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
    ],
    'http' => [ // optional
      'timeout' => 5.0,
      'connect_timeout' => 5.0,
      // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    'mode' => 'dev', // optional, dev/hk;当为 `hk` 时，为中国香港 gateway。
  ];

  public function index()
  {
    $order = [
      'out_trade_no' => time(),
      'total_fee' => '101', // **单位：分**
      'body' => 'test body - 测试',
      'openid' => 'onkVf1FjWS5SBIixxxxxxx',
    ];

    $pay = Pay::wechat($this->config)->mp($order);
    return $pay;
    // $pay->appId
    // $pay->timeStamp
    // $pay->nonceStr
    // $pay->package
    // $pay->signType
  }

  public function notify()
  {
    $pay = Pay::wechat($this->config);

    try {
      $data = $pay->verify(); // 是的，验签就这么简单！

      Log::debug('Wechat notify', $data->all());
    } catch (Exception $e) {
      // $e->getMessage();
    }

    return $pay->success(); // laravel 框架中请直接 `return $pay->success()`
  }
}
