<?php
namespace App\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Model\Item;

// http://cms.ywj.river.phper.website/buildemo?size=10&offset=20
class ItemController extends BaseController
{
    /* {{{ addItem */
    public function addItem(Request $req, Response $res, array $args)
    {
        $code = 0;
        $msg = '';
        $result = null;
        do {
            $title = $req->getParam('title');
            $brief = $req->getParam('brief', '');
            $content = $req->getParam('content');
            $tags = $req->getParam('tags', '');
            $source = $req->getParam('source', '');
            $type = $req->getParam('type', 0);

            if (empty($title) && empty($content)) {
                $code = -1;
                $msg = 'title || content is empty!';
                break;
            }

            $itemTmp = [];
            $itemTmp['title']  = $title;
            $itemTmp['brief']  = $brief;
            $itemTmp['type']  = $type;
            $itemTmp['content']  = $content;
            $tags = str_replace(['，', ' '], [',', ''], $tags);
            $itemTmp['tags']  = $tags;
            $itemTmp['source']  = $source;
            $itemTmp['state']  = 1;
            $itemTmp['orig_id']  = 'man_' . md5($content);

            try {
                $item = Item::create($itemTmp);
                $result['data'] = $item;
                // handle es
                $idxRst = \App\Logic\Helper::addDoc($item->toArray());
            } catch (\Exception $e) {
                $code = $e->getCode();
                $msg = $e->getMessage();
                $this->container['data'] = [
                    'code' => $code,
                    'msg' => $msg,
                    'result' => $result,
                ];
                return $res;
            }
        } while (false);

        $this->container['data'] = [
            'code' => $code,
            'msg' => $msg,
            'result' => $result,
        ];

        $item && $this->container->get('flash')->addMessage('itemMsg', sprintf('%s:%s 创建成功', $item->id, $item->title));

        return $res->withRedirect('/content/list?state=1');
    } /* }}} */

    /* {{{ edit item */
    public function editItem(Request $req, Response $res, array $args)
    {
        $code = 0;
        $msg = '';
        $result = null;
        do {
            $id = $args['id'];
            $item = Item::find($id);
            if (empty($item)) {
                $code = -1;
                $msg = 'id not found!';
                break;
            }
            $params = $req->getParams();
            $isNeedUpdate = false;
            if (isset($params['state']) && is_numeric($params['state']) && $item->state != $params['state']) {
                $item->state = $params['state'];
                $isNeedUpdate = true;
            }
            if (isset($params['type']) && is_numeric($params['type']) && $item->type != $params['type']) {
                $item->type = $params['type'];
                $isNeedUpdate = true;
            }
            if (isset($params['title']) && !empty($params['title']) && $item->title != $params['title']) {
                $item->title = $params['title'];
                $isNeedUpdate = true;
            }
            if (isset($params['brief']) && !empty($params['brief']) && $item->brief != $params['brief']) {
                $item->brief = $params['brief'];
                $isNeedUpdate = true;
            }
            if (isset($params['content']) && !empty($params['content']) && $item->content != $params['content']) {
                $item->content = $params['content'];
                $isNeedUpdate = true;
            }
            if (isset($params['tags']) && !empty($params['tags']) && $item->tags != $params['tags']) {
                $item->tags = str_replace('，', ',', $params['tags']);
                $isNeedUpdate = true;
            }
            if (isset($params['source']) && !empty($params['source']) && $item->source != $params['source']) {
                $item->source = $params['source'];
                $isNeedUpdate = true;
            }

            $rst = true;
            if ($isNeedUpdate) {
                $rst = $item->save();
            }
            if (!$rst) {
                $code = -2;
                $msg = 'update failed!';
                break;
            }
            // handle es
            if ($item->state == 0) {
                $idxRst = \App\Logic\Helper::delDoc($item->toArray());
            } else {
                $idxRst = \App\Logic\Helper::addDoc($item->toArray());
            }

            $result['data'] = $item->toArray();
        } while (0);

        if ($req->isXhr()) {
            $this->container['data'] = [
                'code' => $code,
                'msg' => $msg,
                'result' => $result,
            ];
            return $res;
        }

        return $res->withRedirect('/content/list?state=' . $item->state);
    } /* }}} */

    /* {{{ get item */
    public function getItem(Request $req, Response $res, array $args)
    {
        $code = 0;
        $msg = '';
        $result = null;
        do {
            $id = $args['id'];
            $item = Item::find($id);
            if (empty($item)) {
                $code = -1;
                $msg = 'id not found!';
                break;
            }
            $result['data'] = $item->toArray();
        } while (0);

        $this->container['data'] = [
            'code' => $code,
            'msg' => $msg,
            'result' => $result,
        ];
    } /* }}} */

    /* {{{ edit item page */
    public function editItemPage(Request $req, Response $res, array $args)
    {
        $code = 0;
        $msg = '';
        $result = null;
        do {
            $id = $args['id'];
            $item = Item::find($id);
            if (empty($item)) {
                $code = -1;
                $msg = 'id not found!';
                break;
            }
        } while (0);

        if ($code != 0) {
            $this->container->get('flash')->addMessage('itemMsg', sprintf('ID%s不存在', $id));
            return $res->withRedirect('/content/list?state=1');
        }
        $renderer = $this->container->get('renderer');
        $renderer->addAttribute('item', $item);

        return $renderer->render($res, '/contentEdit.php', $args);
    } /* }}} */

    /* {{{ list item */
    public function listItem(Request $req, Response $res, array $args)
    {
        $code = 0;
        $msg = '';
        $params = $req->getParams();
        $result = [
            'total' => 0,
            'offset' => isset($params['offset']) ? (int)$params['offset'] : 0,
            'size' => isset($params['size']) ? (int)$params['size'] : 20,
        ];
        do {
            $items = Item::getItems($params);
            $result['total'] = $items['total'];
            $data = $items['result'];
            $result['data'] = $data;
        } while (0);


        if ($req->isXhr()) {
            $this->container['data'] = [
                'code' => $code,
                'msg' => $msg,
                'result' => $result,
            ];
            return $res;
        }

        $renderer = $this->container->get('renderer');

        $renderer->addAttribute('result', $result);
        $renderer->addAttribute('kw', $req->getParam('kw', ''));
        $flash = $this->container->get('flash')->getMessage('itemMsg');
        !empty($flash) && $renderer->addAttribute('flash', $flash);

        return $renderer->render($res, '/contentList.php', $args);
    } /* }}} */

    /* {{{ rebuild index */
    public function rebuildIndex(Request $req, Response $res, array $args)
    {
        set_time_limit(0);
        ini_set('memory_limit', '128M');

        $code = 0;
        $msg = '';
        $params = $req->getParams();
        $result = [
            'total' => 0,
            'offset' => isset($params['offset']) ? (int)$params['offset'] : 0,
            'size' => isset($params['size']) ? (int)$params['size'] : 20,
        ];
        do {
            $items = Item::orderBy('id', 'asc')->take($result['size'])->skip($result['offset'])->get();
            if (empty($items)) {
                $code = -1;
                $msg = 'items not found!';
                break;
            }
            foreach ($items as $item) {
                if ($item->state == 0) {
                    $idxRst = \App\Logic\Helper::delDoc($item->id);
                } else {
                    $idxRst = \App\Logic\Helper::addDoc($item->toArray());
                }
                $result['data'][] = $item->toArray();
            }
            $result['total'] = Item::count();
        } while (0);

        $this->container['data'] = [
            'code' => $code,
            'msg' => $msg,
            'result' => $result,
        ];
    } /* }}} */

    /* {{{ index emotions */
    public function buildEmotions(Request $req, Response $res, array $args)
    {
        set_time_limit(0);
        ini_set('memory_limit', '128M');

        $code = 0;
        $msg = '';
        $params = $req->getParams();
        $result = [
            'total' => 0,
            'offset' => isset($params['offset']) ? (int)$params['offset'] : 0,
            'size' => isset($params['size']) ? (int)$params['size'] : 20,
        ];
        do {
            $emotions = \App\Model\Emotion::orderBy('size', 'asc')->take($result['size'])->skip($result['offset'])->get();
            if (empty($emotions)) {
                $code = -1;
                $msg = 'emotions not found!';
                break;
            }
            $httpClient = $this->container->get('httpClient');
            foreach ($emotions as $emo) {
                if (stripos($emo['cdn_url'], 'http') === false
                    || !in_array($emo['type'], ['png', 'jpeg', 'jpg', 'gif'])
                ) {
                    continue;
                }
                $itemTmp = [];
                if ($emo['type'] == 'gif') {
                    $itemTmp['type'] = \App\Logic\Infinity::ITEM_TYPE_YLDT;
                    $itemTmp['tags'] = '斗图动图';
                    $itemTmp['source'] = '表情动图';
                } else {
                    $itemTmp['type'] = \App\Logic\Infinity::ITEM_TYPE_BQTP;
                    $itemTmp['tags'] = '神表情,斗图';
                    $itemTmp['source'] = '神表情';
                }
                $itemTmp['state'] = 1;
                $itemTmp['title'] = $emo['title'];
                $itemTmp['brief'] = $emo['title'];
                // 生成短网址
                $tmpRsp = $httpClient->post('http://dwz.cn/create.php', [
                    'form_params' => ['url' => $emo['cdn_url']],
                ]);
                $tmpArr = jsonDecode((string)$tmpRsp->getBody());
                $itemTmp['content']  = (!empty($tmpArr) && $tmpArr['status'] == 0) ? $tmpArr['tinyurl'] : $emo['cdn_url'];
                $itemTmp['extra'] = jsonEncode($emo);
                $itemTmp['orig_id'] = $emo['md5'];

                try {
                    $item = Item::create($itemTmp);
                } catch (\Illuminate\Database\QueryException $e) {
                    $item = $e->getMessage();
                }
                $result['data'][] = $item;
            }
            $result['total'] = Item::count();
        } while (0);

        $this->container['data'] = [
            'code' => $code,
            'msg' => $msg,
            'result' => $result,
        ];
    } /* }}} */
}
