<?php
/**
 * 微信消息回调控制器
 */

declare(strict_types=1);

namespace app\api\controller\wechat;

use think\Request;
use think\Response;

/**
 * 微信消息和事件接收
 * Class CallbackController
 * @package app\api\controller\wechat
 */
class CallbackController
{
    /**
     * 微信服务器验证（GET请求）
     */
    public function index(Request $request): Response
    {
        $signature = $request->get('signature', '');
        $timestamp = $request->get('timestamp', '');
        $nonce = $request->get('nonce', '');
        $echostr = $request->get('echostr', '');

        // 获取账号ID
        $accountId = (int) $request->route('account_id', 0);
        if ($accountId <= 0) {
            return response('error');
        }

        // 获取账号配置
        $account = \app\common\model\wechat\WechatAccount::find($accountId);
        if (!$account) {
            return response('error');
        }

        // 验证签名
        $token = $account->token;
        $tmpArr = [$token, $timestamp, $nonce];
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $hashStr = sha1($tmpStr);

        if ($hashStr === $signature) {
            return response($echostr);
        }

        return response('error');
    }

    /**
     * 接收微信消息（POST请求）
     */
    public function receive(Request $request): Response
    {
        $accountId = (int) $request->route('account_id', 0);
        if ($accountId <= 0) {
            return response('')->code(200);
        }

        // 获取POST数据
        $postData = $request->getInput();
        $postObj = simplexml_load_string($postData, 'SimpleXMLElement', LIBXML_NOCDATA);

        if (empty($postObj)) {
            return response('')->code(200);
        }

        // 处理消息类型
        $msgType = strval($postObj->MsgType ?? '');
        $openid = strval($postObj->FromUserName ?? '');
        $createTime = intval($postObj->CreateTime ?? 0);

        // 记录日志
        $this->log($accountId, $msgType, $openid, $postData);

        // 更新粉丝信息
        $this->updateFans($accountId, $openid, $postObj);

        // 根据消息类型处理
        switch ($msgType) {
            case 'text':
                $content = strval($postObj->Content ?? '');
                return $this->handleTextMessage($accountId, $openid, $content);
            case 'image':
                $mediaId = strval($postObj->MediaId ?? '');
                return $this->handleImageMessage($accountId, $openid, $mediaId);
            case 'event':
                return $this->handleEvent($accountId, $openid, $postObj);
            default:
                return response('')->code(200);
        }
    }

    /**
     * 处理文本消息
     */
    private function handleTextMessage(int $accountId, string $openid, string $content): Response
    {
        // 查询关键词回复
        $reply = \app\common\model\wechat\WechatReply::where('account_id', $accountId)
            ->where('type', 'keyword')
            ->where('status', 1)
            ->where(function ($query) use ($content) {
                $query->where('keyword', $content)
                    ->where('match_mode', 'full');
            })
            ->find();

        if (!$reply) {
            // 模糊匹配
            $reply = \app\common\model\wechat\WechatReply::where('account_id', $accountId)
                ->where('type', 'keyword')
                ->where('status', 1)
                ->where('match_mode', 'like')
                ->where('keyword', 'like', "%{$content}%")
                ->find();
        }

        // 如果没有关键词回复，使用默认回复
        if (!$reply) {
            $reply = \app\common\model\wechat\WechatReply::where('account_id', $accountId)
                ->where('type', 'default')
                ->where('status', 1)
                ->find();
        }

        if ($reply) {
            return $this->sendReply($accountId, $openid, $reply);
        }

        return response('')->code(200);
    }

    /**
     * 处理图片消息
     */
    private function handleImageMessage(int $accountId, string $openid, string $mediaId): Response
    {
        // 可以设置收到图片的自动回复
        return response('')->code(200);
    }

    /**
     * 处理事件
     */
    private function handleEvent(int $accountId, string $openid, $postObj): Response
    {
        $event = strval($postObj->Event ?? '');

        switch ($event) {
            case 'subscribe': // 关注
                return $this->handleSubscribe($accountId, $openid);
            case 'unsubscribe': // 取消关注
                return $this->handleUnsubscribe($accountId, $openid);
            case 'CLICK': // 点击菜单
                $eventKey = strval($postObj->EventKey ?? '');
                return $this->handleMenuClick($accountId, $openid, $eventKey);
            case 'VIEW': // 点击链接
                return response('')->code(200);
            default:
                return response('')->code(200);
        }
    }

    /**
     * 处理关注事件
     */
    private function handleSubscribe(int $accountId, string $openid): Response
    {
        // 更新粉丝状态
        \app\common\model\wechat\WechatFans::where('openid', $openid)
            ->where('account_id', $accountId)
            ->update([
                'status' => 1,
                'subscribe_time' => date('Y-m-d H:i:s'),
                'unsubscribe_time' => null,
                'update_time' => date('Y-m-d H:i:s'),
            ]);

        // 发送关注回复
        $reply = \app\common\model\wechat\WechatReply::where('account_id', $accountId)
            ->where('type', 'subscribe')
            ->where('status', 1)
            ->find();

        if ($reply) {
            return $this->sendReply($accountId, $openid, $reply);
        }

        return response('')->code(200);
    }

    /**
     * 处理取消关注事件
     */
    private function handleUnsubscribe(int $accountId, string $openid): Response
    {
        \app\common\model\wechat\WechatFans::where('openid', $openid)
            ->where('account_id', $accountId)
            ->update([
                'status' => 0,
                'unsubscribe_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s'),
            ]);

        return response('')->code(200);
    }

    /**
     * 处理菜单点击事件
     */
    private function handleMenuClick(int $accountId, string $openid, string $eventKey): Response
    {
        // 根据EventKey查找对应的回复
        $reply = \app\common\model\wechat\WechatReply::where('account_id', $accountId)
            ->where('type', 'keyword')
            ->where('keyword', $eventKey)
            ->where('status', 1)
            ->find();

        if ($reply) {
            return $this->sendReply($accountId, $openid, $reply);
        }

        return response('')->code(200);
    }

    /**
     * 发送回复
     */
    private function sendReply(int $accountId, string $openid, $reply): Response
    {
        $account = \app\common\model\wechat\WechatAccount::find($accountId);
        if (!$account) {
            return response('')->code(200);
        }

        $replyType = $reply->reply_type;
        $content = $reply->content;

        $text = '<xml>';
        $text .= '<ToUserName><![CDATA[' . $openid . ']]></ToUserName>';
        $text .= '<FromUserName><![CDATA[' . $account->appid . ']]></FromUserName>';
        $text .= '<CreateTime>' . time() . '</CreateTime>';
        $text .= '<MsgType><![CDATA[' . $replyType . ']]></MsgType>';

        switch ($replyType) {
            case 'text':
                $text .= '<Content><![CDATA[' . $content . ']]></Content>';
                break;
            case 'image':
                $text .= '<Image><MediaId><![CDATA[' . $reply->media_id . ']]></MediaId></Image>';
                break;
            case 'voice':
                $text .= '<Voice><MediaId><![CDATA[' . $reply->media_id . ']]></MediaId></Voice>';
                break;
            case 'video':
                $text .= '<Video><MediaId><![CDATA[' . $reply->media_id . ']]></MediaId></Video>';
                break;
            case 'news':
                // 图文消息
                $text .= '<ArticleCount>1</ArticleCount>';
                $text .= '<Articles>';
                $text .= '<item>';
                $text .= '<Title><![CDATA[' . ($content['title'] ?? '') . ']]></Title>';
                $text .= '<Description><![CDATA[' . ($content['description'] ?? '') . ']]></Description>';
                $text .= '<PicUrl><![CDATA[' . ($content['picurl'] ?? '') . ']]></PicUrl>';
                $text .= '<Url><![CDATA[' . ($content['url'] ?? '') . ']]></Url>';
                $text .= '</item>';
                $text .= '</Articles>';
                break;
        }

        $text .= '</xml>';

        return response($text, 200, ['Content-Type' => 'text/xml'])->code(200);
    }

    /**
     * 更新粉丝信息
     */
    private function updateFans(int $accountId, string $openid, $postObj): void
    {
        $fans = \app\common\model\wechat\WechatFans::where('openid', $openid)
            ->where('account_id', $accountId)
            ->find();

        $data = [
            'account_id' => $accountId,
            'openid' => $openid,
            'update_time' => date('Y-m-d H:i:s'),
        ];

        if (isset($postObj->NickName)) {
            $data['nickname'] = strval($postObj->NickName);
        }
        if (isset($postObj->Sex)) {
            $data['gender'] = intval($postObj->Sex);
        }
        if (isset($postObj->Country)) {
            $data['country'] = strval($postObj->Country);
        }
        if (isset($postObj->Province)) {
            $data['province'] = strval($postObj->Province);
        }
        if (isset($postObj->City)) {
            $data['city'] = strval($postObj->City);
        }
        if (isset($postObj->Language)) {
            $data['language'] = strval($postObj->Language);
        }
        if (isset($postObj->HeadImgUrl)) {
            $data['avatar'] = strval($postObj->HeadImgUrl);
        }
        if (isset($postObj->SubscribeTime)) {
            $data['subscribe_time'] = date('Y-m-d H:i:s', intval($postObj->SubscribeTime));
        }
        if (isset($postObj->UnionID)) {
            $data['unionid'] = strval($postObj->UnionID);
        }
        if (isset($postObj->TagIdList)) {
            $data['tagid_list'] = strval($postObj->TagIdList);
        }

        if ($fans) {
            $fans->save($data);
        } else {
            $data['create_time'] = date('Y-m-d H:i:s');
            $model = new \app\common\model\wechat\WechatFans();
            $model->save($data);
        }
    }

    /**
     * 记录日志
     */
    private function log(int $accountId, string $msgType, string $openid, string $content): void
    {
        try {
            $logDir = root_path() . 'runtime/logs/wechat/';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            $logFile = $logDir . date('Y-m-d') . '.log';
            $log = sprintf('[%s] Account:%d Type:%s OpenID:%s Content:%s',
                date('Y-m-d H:i:s'),
                $accountId,
                $msgType,
                $openid,
                $content
            );
            file_put_contents($logFile, $log . PHP_EOL, FILE_APPEND);
        } catch (\Exception $e) {
            // 忽略日志错误
        }
    }
}
