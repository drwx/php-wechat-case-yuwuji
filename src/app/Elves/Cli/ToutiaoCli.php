<?php
namespace App\Cli;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Model\Item;

// php7 src/public/index.php /cli/toutiao "num=1"
class ToutiaoCli extends Cli
{
    public function run(Request $req, Response $res, array $args)
    {
        $num = $req->getParam('num', 1);
        $i = 0;
        $hasMore = true;
        $msg = 'ok';
        $url = 'http://toutiao.com/api/article/recent/?source=2&count=100&category=essay_joke&max_behot_time=&utm_source=toutiao&offset=0&as=A175C77C8578168&cp=57C5E84156A8BE1';
        $httpClient = $this->container->get('httpClient');
        while ($i < $num && $hasMore) {
            $httpRsp = $httpClient->get($url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36',
                    'Cookie' => 'uuid="w:187b447095a5486f908d15bb91bee10a"; utm_campaign=client_share; utm_medium=toutiao_android; cp=57C39A535F112E1; CNZZDATA1259612802=1145888933-1472198632-%7C1472456436; _ga=GA1.2.991536083.1466997985; tt_webid=20214575019; csrftoken=13363d4875ce92f38b9a75f4fee90262; CNZZDATA1258609184=1006032138-1466995632-%7C1467773945; __utma=24953151.991536083.1466997985.1472460626.1472562721.3; __utmc=24953151; __utmz=24953151.1472562721.3.2.utmcsr=toutiao|utmccn=(not%20set)|utmcmd=(not%20set); utm_source=toutiao',
                    'Host' => 'toutiao.com',
                ],
                'timeout' => 10,
            ]);
            $content = (string)$httpRsp->getBody();

            $jsonArr = jsonDecode($content);
            if (empty($jsonArr)) {
                $msg = 'fetch err';
                break;
            }
            $hasMore = $jsonArr['has_more'];
            $data = $jsonArr['data'];
            foreach ($data as $grp) {
                $it = $grp['group'];
                $itemTmp = [];
                $itemTmp['title']  = mb_substr($it['content'], 0, 20, 'utf-8');
                $itemTmp['brief']  = $it['content'];
                $itemTmp['type']  = \App\Logic\Infinity::ITEM_TYPE_NHDZ;
                $itemTmp['content']  = $it['content'];
                $itemTmp['tags']  = '内涵,搞笑,段子';
                $itemTmp['source']  = $it['category_name'];
                $itemTmp['orig_id']  = $it['id'];
                $itemTmp['state']  = 1;

                try {
                    $item = Item::create($itemTmp);
                    $result['data'] = $item;
                    // handle es
                    $idxRst = \App\Logic\Helper::addDoc($item->toArray());
                } catch (\Exception $e) {
                    $code = $e->getCode();
                    $msg = $e->getMessage();
                    continue;
                }
            }

            $i++;
        }
        return $res->write($msg . '|count: ' . $i);
    }
}
