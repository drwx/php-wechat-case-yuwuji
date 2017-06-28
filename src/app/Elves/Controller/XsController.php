<?php
namespace App\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class XsController extends BaseController
{
    /* {{{ doc add */
    public function addDoc(Request $req, Response $res, array $args)
    {
        $code = 0;
        $msg = '';
        $result = null;
        do {
            $_id     = (int)$req->getParam('id', 0);
            $title   = $req->getParam('title', '');
            $content = $req->getParam('content', '');
            $type    = $req->getParam('type', 0);

            if ($_id < 1 || empty($title) || empty($content)) {
                $code = -1;
                $msg = 'id || title || content is empty!';
                break;
            }
            $brief = $req->getParam('brief', '');
            $tags = (string)$req->getParam('tags', '');
            $tags = str_replace(['，', ' '], [',', ''], $tags);
            $source = (string)$req->getParam('source', '');
            $source = str_replace(['，', ' '], [',', ''], $source);

            $params               = [];
            $params['_id']         = $_id;
            $params['title']      = trim($title);
            $params['brief']      = trim($brief);
            $params['content']    = trim($content);
            $params['tags']       = trim($tags);
            $params['source']     = trim($source);
            $params['type']       = $type;
            $params['ts']         = $req->getParam('ts', time());

            $xs = $this->container->get('xs');
            try {
                $doc = new \XSDocument($params);
                $rspXs = $xs->index->add($doc);
                var_dump($rspXs);die;
                $result['data'] = jsonEncode($rspXs);
            } catch (\Exception $e) {
                $code = $e->getCode();
                $msg = $e->getMessage();
                break;
            }
        } while (false);

        $this->container['data'] = [
            'code' => $code,
            'msg' => $msg,
            'result' => $result,
        ];
        return $res;
    } /* }}} */
    /* {{{ doc update */
    public function updDoc(Request $req, Response $res, array $args)
    {
        $code = 0;
        $msg = '';
        $result = null;
        do {
            $_id     = (int)$req->getParam('id', 0);
            $title   = $req->getParam('title', '');
            $content = $req->getParam('content', '');
            $type    = $req->getParam('type', 0);

            if ($_id < 1 || empty($title) || empty($content)) {
                $code = -1;
                $msg = 'id || title || content is empty!';
                break;
            }
            $brief = $req->getParam('brief', '');
            $tags = (string)$req->getParam('tags', '');
            $tags = str_replace(['，', ' '], [',', ''], $tags);
            $source = (string)$req->getParam('source', '');
            $source = str_replace(['，', ' '], [',', ''], $source);

            $params               = [];
            $params['id']         = $_id;
            $params['title']      = trim($title);
            $params['brief']      = trim($brief);
            $params['content']    = trim($content);
            $params['tags']       = trim($tags);
            $params['source']     = trim($source);
            $params['type']       = $type;
            $params['ts']         = $req->getParam('ts', time());

            $xs = $this->container->get('xs');
            try {
                $doc = new \XSDocument($params);
                $rspXs = $xs->index->update($doc);
                $result['data'] = jsonEncode($rspXs);
            } catch (\Exception $e) {
                $code = $e->getCode();
                $msg = $e->getMessage();
                break;
            }
        } while (false);

        $this->container['data'] = [
            'code' => $code,
            'msg' => $msg,
            'result' => $result,
        ];
        return $res;
    } /* }}} */
    /* {{{ doc delete */
    public function delDoc(Request $req, Response $res, array $args)
    {
        $code = 0;
        $msg = '';
        $result = null;
        do {
            $_id = (int)$req->getParam('id', 0);
            if ($_id < 1) {
                $code = -1;
                $msg = 'id is empty!';
                break;
            }

            $xs = $this->container->get('xs');
            try {
                $rspXs = $xs->index->del($_id);
                $result['data'] = jsonEncode($rspXs);
            } catch (\Exception $e) {
                $code = $e->getCode();
                $msg = $e->getMessage();
                break;
            }
        } while (false);

        $this->container['data'] = [
            'code' => $code,
            'msg' => $msg,
            'result' => $result,
        ];
        return $res;
    } /* }}} */

    /* {{{ index delete */
    public function delIdx(Request $req, Response $res, array $args)
    {
        $code = 0;
        $msg = '';
        $result = null;
        do {
            $xs = $this->container->get('xs');
            try {
                $rspXs = $xs->index->clean();
                $result['data'] = jsonEncode($rspXs);
            } catch (\Exception $e) {
                $code = $e->getCode();
                $msg = $e->getMessage();
                break;
            }
        } while (false);

        $this->container['data'] = [
            'code' => $code,
            'msg' => $msg,
            'result' => $result,
        ];
        return $res;
    } /* }}} */
    /* {{{ index rebuild */
    public function rebuildIdx(Request $req, Response $res, array $args)
    {
        $code = 0;
        $msg = '';
        $result = null;
        do {
            $xs = $this->container->get('xs');
            try {
                $xs->index->beginRebuild();
                $xs->index->endRebuild();
                $result['data'] = jsonEncode($rspXs);
            } catch (\Exception $e) {
                $code = $e->getCode();
                $msg = $e->getMessage();
                break;
            }
        } while (false);

        $this->container['data'] = [
            'code' => $code,
            'msg' => $msg,
            'result' => $result,
        ];
        return $res;
    } /* }}} */
}
