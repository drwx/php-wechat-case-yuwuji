<?php
namespace App\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Model\Item;

class WebController extends BaseController
{
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

    /* {{{ getItems */
    public function getItems(Request $req, Response $res, array $args)
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
            $data = [];
            $items = Item::where('state', '1')
                ->orderBy('id', 'desc')
                ->take($result['size'])
                ->skip($result['offset'])
                ->get();
            foreach ($items as $it) {
                $data[] = Item::format($it);
            }
            $result['data'] = $data;
        } while (0);


        $this->container['data'] = [
            'code' => $code,
            'msg' => $msg,
            'result' => $result,
        ];

        return $res;
    } /* }}} */
}
