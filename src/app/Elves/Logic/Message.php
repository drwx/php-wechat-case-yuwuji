<?php
namespace App\Logic;

use EasyWeChat\Support\Log;
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Video;
use EasyWeChat\Message\ShortVideo;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\Material;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Article;
use EasyWeChat\Message\Raw;
use EasyWeChat\Message\Transfer;

/*
1、永久素材： curl -F media=@3.jpg https://api.weixin.qq.com/cgi-bin/material/add_material\?access_token\=NsgbPqg_O37Ygo3N2ZLNUpsLswYWkwygiYmljcTd1j1WTycftDLGjGXNBrCz8NCswPk9wAxgJ2ZiIsN2gF02dDMD3lOQXCOlZhzXH_93Clz40AO-DEkqG0OEyH8KUfR8YRMaAIAOHC\&type\=image
2、素材列表： curl -X POST https://api.weixin.qq.com/cgi-bin/material/batchget_material\?access_token\=NsgbPqg_O37Ygo3N2ZLNUpsLswYWkwygiYmljcTd1j1WTycftDLGjGXNBrCz8NCswPk9wAxgJ2ZiIsN2gF02dDMD3lOQXCOlZhzXH_93Clz40AO-DEkqG0OEyH8KUfR8YRMaAIAOHC -d '{"type":"image", "offset":0, "count":20}'
 */

class Message
{
    private $wechat;
    private $redis;

    const KF = 'kf2002@yuwuji-dc';
    const TXTLEN = 677; //680
    const KEEPTIME = 300;
    const WELCOME = <<<EOS
        欲无极能帮您快速搜索各种好屌的内容，使用特别简单：

        1. 输入一个词，如“内涵”，欲MM会回复您好玩的东东

        2. 想得到之前输入的搜索词更多乐趣，无需重新输入，直接戳“换一换”就好啦

        3. 客官若找不到想要的内容，请回复“#想要的内容描述”，欲MM会尽快上新货哦

        嘛~ 好用请置顶！好用请分享！谁用谁知道[Shy]
EOS;

    /* {{{ constructor */
    public function __construct($wechatApp)
    {
        $this->wechat = $wechatApp;
        $this->redis = $wechatApp->redis;
    } /* }}} */

    /* {{{ getApp */
    public function getApp()
    {
        return $this->wechat;
    } /* }}} */

    /* {{{ handleTextMsg */
    public function handleTextMsg($message)
    {
        $content = trim($message->Content);

        if ($content == '@帮助' || $content == '@help') {
            return self::WELCOME;
        }
        // 反馈调用
        if (stripos($content, '#') === 0) {
            $feed = mb_substr($content, 1);
            $feed = trim($feed);
            if (mb_strlen($feed) > 1) {
                $nickname = 'nickname';
                $this->redis->rPush(Constant::INF_USER_FEEDBACK_LIST, jsonEncode(['openId' => $message->FromUserName, 'nickname' => $nickname, 'feed' => $feed, 'ts' => time()]));

                return '您的反馈已收到，我们会认真考虑，感谢支持：）';
            }
        }

        if (strpos($content, '收到不支持的消息类型，暂无法显示') !== false) {
            return '收到不支持的消息类型，暂无法显示';
        }
        if (mb_strlen($content) < 2) {
            return '亲，请输入至少两个字哟！比如 『内涵』，更多帮助信息请输入 @帮助 或 @help 获得';
        }

        $this->redis->del(sprintf(Constant::INF_EVENT_MENU_ITEM_STR, $message->FromUserName)); // 删除单独类型
        // 记录最后一次发送的词
        $this->redis->hSet(Constant::INF_EVENT_RANDOM_WORD_POOL, $message->FromUserName, $content);

        // 判断用户标签
        $typeStr = implode('_', Infinity::$aDftTypes[Constant::USER_TYPE_NORMAL]);
        $wxData = $this->getSearchRsp($content, ['message' => $message, 'type' => $typeStr]);
        return $this->retWxMsg($wxData, $message);
    } /* }}} */

    /* {{{ handleEventMsg */
    public function handleEventMsg($message)
    {
        $result = '';
        switch (strtolower($message->Event)) {
            /* 消息事件 */
        case 'subscribe':
            $result = new Text();
            $result->content = self::WELCOME;

            /* $staffMsg = new Image();
            $mediaId = 'SD0_bq_D8o_yvOLP7YKvQ9VotbE23XecdyMOmLj-ADk';
            $staffMsg->media_id = $mediaId;
            $this->wechat->staff->message($staffMsg)->by(self::KF)->to($message->FromUserName)->send(); */
            break;
        case 'scan':
            $result = new Text();
            $result->content = self::WELCOME;
            break;
            /* 菜单事件 */
        case 'click':
            $eventKey = trim($message->EventKey);
            Log::info('click event key:' . $eventKey);
            $aKey = array_filter(array_map(function ($v) {
                    return trim($v);
                }, explode('_', $eventKey)));
            $keyStr = array_shift($aKey);
            if (empty($keyStr)) {
                return '功能正在修复中。。请输入关键词搜索哟！';
            }
            if ($keyStr == 'random') { // 换一换搜索 菜单格式：random_2_3_4
                $text = $this->redis->hGet(Constant::INF_EVENT_RANDOM_WORD_POOL, $message->FromUserName);
                if (empty($text)) {
                    $text = Infinity::$aMenuItemType[Infinity::ITEM_TYPE_NHDZ];
                    $this->redis->hSet(Constant::INF_EVENT_RANDOM_WORD_POOL, $message->FromUserName, $text);
                }
                Log::info('random text:' . $text);

                $typeStr = $this->redis->get(sprintf(Constant::INF_EVENT_MENU_ITEM_STR, $message->FromUserName)); // 设置单独类型
                if (empty($typeStr)) {
                    $typeStr = implode('_', array_values(Infinity::$aDftTypes[Constant::USER_TYPE_NORMAL]));
                    if (!empty($aKey)) {
                        $typeStr = implode('_', $aKey);
                    }
                }
                Log::info('random typestr[rds]:' . $typeStr);

                $wxData = $this->getSearchRsp($text, ['message' => $message, 'type' => $typeStr]);
                return $this->retWxMsg($wxData, $message);
            } else { // 预置词搜索 菜单格式：预置词_2_3_4
                $typeStr = implode('_', array_values(Infinity::$aDftTypes[Constant::USER_TYPE_NORMAL]));
                if (is_numeric($keyStr) && in_array($keyStr, array_keys(Infinity::$aItemType))) { // 选择菜单类型，预置词为预置type转换，2
                    $typeStr = $keyStr; // 选择菜单类型
                    $keyStr = Infinity::$aMenuItemType[$typeStr]; // 转换成搜索词
                    Log::info('preset typestr:' . $keyStr);
                } else {
                    if (!empty($aKey)) {
                        $typeStr = implode('_', $aKey);
                    }
                }
                $this->redis->setEx(sprintf(Constant::INF_EVENT_MENU_ITEM_STR, $message->FromUserName), self::KEEPTIME, $typeStr); // 设置单独类型

                $this->redis->hSet(Constant::INF_EVENT_RANDOM_WORD_POOL, $message->FromUserName, $keyStr);

                $wxData = $this->getSearchRsp($keyStr, ['message' => $message, 'type' => $typeStr]);
                return $this->retWxMsg($wxData, $message);
            }
            break;
        case 'unsubscribe':
        case 'location':
        case 'view':
        case 'scancode_push':
        case 'scancode_waitmsg':
        case 'pic_sysphoto':
        case 'pic_photo_or_album':
        case 'pic_weixin':
        case 'location_select':
        default:
            $result = new Text();
            $result->content = self::WELCOME;
            break;
        }
        return $result;
    } /* }}} */

    /* {{{ retWxMsg */
    public function retWxMsg($wxData, $message)
    {
        if (empty($wxData)) {
            return '对不起，未找到合适的内容';
        }
        if ($wxData['wxtype'] == 'string') {
            $txt = $wxData['wxmsg'];
            $txtLen = mb_strlen($txt, 'utf8');
            if ($txtLen > self::TXTLEN) {
                return mbTrunc($txt, self::TXTLEN);
                /* $segSize = ceil($txtLen / self::TXTLEN);
                for ($i = 0; $i < $segSize; $i++) {
                    $toSent = mb_substr($txt, $i * self::TXTLEN, self::TXTLEN, 'utf-8');
                    $this->wechat->staff->message($toSent)->by(self::KF)->to($message->FromUserName)->send();
                }
                return Constant::WX_SUCCESS; */
            } else {
                return $txt;
            }
        } elseif ($wxData['wxtype'] == 'object') {
            return $wxData['wxmsg'];
        }
    } /* }}} */

    /* {{{ getSearchRsp
     * 这个代码逻辑我自己都看不懂了，不要乱改
     * */
    public function getSearchRsp($content, $opts = [])
    {
        $message = isset($opts['message']) ? $opts['message'] : null;
        if (!$message) {
            return false;
        }

        // handle list result
        $rows = isset($opts['rows']) ? (int)$opts['rows'] : 20;
        $md5TxtKey = md5($message->FromUserName . $content);
        $stKey = sprintf(Constant::INF_USER_WORD_KEY, $md5TxtKey);
        $listKey = sprintf(Constant::INF_USER_WORD_LIST_RST, $md5TxtKey);

        $start = $this->redis->incr($stKey) - 1; // get current cursor position
        $needResetAll = false; // reset list result and search word key
        $needResetList = false; // reset list result
        $needResetAllWithoutRst = false; // reset list result and search word key with different hit
        do {
            // end of the list store user's search word info
            $startTotal = $this->redis->lIndex($listKey, -1);
            Log::info('list rds: [start: ' . $start . ']-[startTotal: ' . $startTotal . ']-[rdskey: ' . $listKey . ']');
            if (!empty($startTotal)) {
                list($startRds, $totalRds, ) = explode(':', $startTotal);
                if (($startRds + $rows - 1) <= $start) { // if cursor upto the total count, need reset the list
                    $needResetList = true;
                }
                if ($totalRds > 0 && ($totalRds <= $start)) {
                    $needResetAll = true;
                    break;
                }

                $dataJson = $this->redis->lIndex($listKey, $start - $startRds);
                $data = jsonDecode($dataJson);
            } else {
                $queryParams = [
                    'kw' => $content,
                    'start' => $start,
                    'rows' => $rows
                ];
                if (isset($opts['type']) && !empty($opts['type'])) {
                    $queryParams['type'] = $opts['type'];
                }

                // switch search engine
                $config = $this->wechat->config;
                $resultSet = $this->xsQuery($queryParams);
                if (!empty($resultSet)) {
                    $__total = $resultSet['__total'];
                    $__start = $resultSet['__start'];
                    if ($__total > 0 && ($__total <= $__start)) {
                        $needResetAll = true;
                        break;
                    }
                    $data = $resultSet['__items'][0];

                    // set cache resultSet
                    $this->redis->expire($listKey, 300); // 结果集缓存300s
                    foreach ($resultSet['__items'] as $item) {
                        $this->redis->rPush($listKey, jsonEncode($item));
                    }
                    $this->redis->rPush($listKey, "$__start:$__total:$rows");
                } else { // 如果查询无结果，重置所有用户状态
                    $needResetAllWithoutRst = true;
                }
                // Log::info('search resultSet: ' . jsonEncode($resultSet));
            }
        } while (0);

        if ($needResetList) {
            $this->redis->del($listKey);
            Log::info('listKey reset: ' . $listKey);
        }
        $wxMsg = '未找到匹配，试试其他关键词哟！';
        $wxType = 'string';
        if ($needResetAll || $needResetAllWithoutRst) {
            $this->redis->del($stKey);
            $this->redis->del($listKey);
            Log::info('[stKey reset: ' . $stKey . ']-[listKey reset: ' . $listKey . ']');

            $wxMsg = $needResetAllWithoutRst ? $wxMsg : '已经看完咯，可以试试其他关键词哟！';
        } elseif (!empty($data) && isset($data['content'])) {
            // Log::info('search data: ' . jsonEncode($data));
            if ($data['type'] == \App\Logic\Infinity::ITEM_TYPE_BQTP) {
                $wxType = 'object';
            } elseif ($data['type'] == \App\Logic\Infinity::ITEM_TYPE_NHDZ) {
                $wxMsg = $data['content'];
            } else {
                $wxMsg = sprintf("%s\n%s", $data['title'], $data['content']);
            }
        }

        return ['wxmsg' => $wxMsg, 'wxtype' => $wxType];
    } /* }}} */

    /* {{{ xsQuery */
    public function xsQuery(array $queryParams)
    {
        $kw = $queryParams['kw'];
        $start   = $queryParams['start'];
        $rows    = $queryParams['rows'];
        $type = isset($queryParams['type']) ? trim($queryParams['type']) : null;
        Log::info('xsParams: ' . jsonEncode($queryParams));

        $queryArr[] = "($kw)";
        $queryOr = ["tags:$kw", "source:$kw"];
        if ($type !== null) { // 过滤类型
            $types = explode('_', $type);
            foreach ($types as $t) {
                $queryOr[] = "type:$t";
            }
        }
        $queryArr[] = '(' . implode(' OR ', $queryOr) . ')';
        $queryStr = implode(' AND ', $queryArr);

        $data = [];
        $xsClient = $this->wechat->xsClient;
        $resultSet = $xsClient->setQuery($queryStr)
            ->setLimit($rows, $start)
            ->setSort('ts', false, true)
            ->search();;
        $total = $xsClient->getLastCount();
        Log::info('xsQuery: ' . $xsClient->getQuery() . '|' . 'queryStr: ' . $queryStr);
        foreach ($resultSet as $doc) {
            $it = [];
            $it['id']      = $doc->_id;
            $it['type']    = $doc->type;
            $it['title']   = $doc->title;
            $it['content'] = $doc->content;
            $it['tags']    = $doc->tags;
            $it['source']  = $doc->source;
            $it['score']   = $doc->percent();

            $data['__items'][] = $it;
        }

        if ($total > 0 && isset($data['__items'])) {
            $data['__total'] = $total;
            $data['__start'] = $start;
        }

        return $data;
    } /* }}} */
}
