<?php
namespace App\Logic;

class Helper
{
    public static function htmlSelect($kvData, $key, $attr = '')
    {
        $str = "<select $attr>";
        foreach ($kvData as $k => $v) {
            if ($key == $k) {
                $str .= sprintf('<option selected value="%s">%s</option>', $k, $v);
            } else {
                $str .= sprintf('<option value="%s">%s</option>', $k, $v);
            }
        }
        $str .= '</select>';

        return $str;
    }

    public static function getMatchRule(array $matchRules)
    {
        $ruleKeys = ['tag_id', 'sex', 'country', 'province', 'city', 'client_platform_type'];
        $matchRule = [];
        foreach ($ruleKeys as $rk) {
            if (isset($matchRules[$rk]) && !empty($matchRules[$rk])) {
                $matchRule[$rk] = $matchRules[$rk];
            }
        }

        return $matchRule;
    }

    public static function getMenuBtns(array $btnData)
    {
        // 一级菜单为空
        $topName = trim($btnData['top']['name']);
        if (empty($topName)) {
            return [];
        }
        // 二级菜单为空
        $btnNames = array_filter($btnData['sub']['name'], function ($v) {
            $v = trim($v);
            return !empty($v);
        });
        $btnAttrs = \App\Logic\Infinity::$aMenuBtnAttr;

        // top button
        $topType = $btnData['top']['type'];
        $topVal = trim($btnData['top']['value']);
        $topAttr = $btnAttrs[$topType];

        // if no sub buttons
        if (empty($btnNames)) {
            return [
                $topAttr[0] => $topType,
                $topAttr[1] => $topName,
                $topAttr[2] => $topVal ? $topVal : 'river_rdm_' . mt_rand(10000,99999),
            ];
        }

        // sub buttons
        $subBtns = [];
        $subCnt = count($btnData['sub']['type']);
        for ($i = 0; $i < $subCnt; $i++) {
            $subName = trim($btnData['sub']['name'][$i]);
            if (empty($subName)) {
                continue;
            }
            $subType = $btnData['sub']['type'][$i];
            $subVal = trim($btnData['sub']['value'][$i]);
            $attrs = $btnAttrs[$subType];
            $subBtns[] = [
                $attrs[0] => $subType,
                $attrs[1] => $subName,
                $attrs[2] => $subVal ? $subVal : 'river_rdm_' . mt_rand(10000,99999)
            ];
        }

        $btns[$topAttr[1]] = $topName;
        if (!empty($subBtns)) {
            $btns['sub_button'] = $subBtns;
        } else {
            $btns[$topAttr[0]] = $topType;
            $btns[$topAttr[2]] = $topVal ? $topVal : 'river_rdm_' . mt_rand(10000,99999);
        }

        return $btns;
    }

    public static function createLogger($loggerName = 'NaiCha', $logConf = null)
    {
        $logger = new \Monolog\Logger($loggerName);

        $logFile = isset($logConf['file']) ? $logConf['file'] : '/tmp/nc.log';
        $logLevel = isset($logConf['level']) ? $logConf['level'] : \Monolog\Logger::INFO;
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($logFile, $logLevel));

        if (isset($logConf['processors']) && is_array($logConf['processors'])) {
            foreach ($logConf['processors'] as $processor) {
                if (is_array($processor)) { // ['\Monolog\Processor\UidProcessor', [10]]
                    $clazz = new \ReflectionClass($processor[0]);
                    $logger->pushProcessor($clazz->newInstanceArgs($processor[1]));
                } elseif (is_callable($processor)) {
                    $logger->pushProcessor($processor);
                }
            }
        }
        // $logger->pushHandler(new \Monolog\Handler\NullHandler());
        // $logger->pushHandler(new \Monolog\Handler\ErrorLogHandler());

        // add logger to Registry pool
        \Monolog\Registry::addLogger($logger);

        return $logger;
    }

    public static function addDoc(array $item)
    {
        $httpClient = \App\GApp::GG('httpClient');
        $config = \App\GApp::GG('config');
        $xs = \App\GApp::GG('xs');
        $cnt = $xs->search->count('_id:' . $item['id']);
        if ($cnt > 0) {
            return self::updDocXs($item, $config, $httpClient);
        } else {
            return self::addDocXs($item, $config, $httpClient);
        }

    }
    public static function updDoc(array $item)
    {
        $httpClient = \App\GApp::GG('httpClient');
        $config = \App\GApp::GG('config');
        $xs = \App\GApp::GG('xs');
        $cnt = $xs->search->count('_id:' . $item['id']);
        if ($cnt > 0) {
            return self::updDocXs($item, $config, $httpClient);
        } else {
            return self::addDocXs($item, $config, $httpClient);
        }
    }
    public static function delDoc(array $item)
    {
        $httpClient = \App\GApp::GG('httpClient');
        $config = \App\GApp::GG('config');

        return self::delDocXs($item, $config, $httpClient);
    }

    /* {{{ xs function */
    public static function addDocXs(array $item, $config, $httpClient)
    {
        $api = $config->get('wechat.xs.api');
        $item['ts'] = strtotime($item['updated_at']);
        $docRsp = $httpClient->post(rtrim($api, '/') . '/xs/adddoc', ['auth' => ['drwcwh', 'drwcwh76'], 'timeout' => 10, 'connect_timeout' => 10, 'form_params' => $item]);
        $docRst = (string)$docRsp->getBody();

        return jsonDecode($docRst);
    }
    public static function updDocXs(array $item, $config, $httpClient)
    {
        $api = $config->get('wechat.xs.api');
        $item['ts'] = strtotime($item['updated_at']);
        $docRsp = $httpClient->post(rtrim($api, '/') . '/xs/upddoc', ['auth' => ['drwcwh', 'drwcwh76'], 'timeout' => 10, 'connect_timeout' => 10, 'form_params' => $item]);
        $docRst = (string)$docRsp->getBody();

        return jsonDecode($docRst);
    }
    public static function delDocXs(array $item, $config, $httpClient)
    {
        $api = $config->get('wechat.xs.api');
        $docRsp = $httpClient->post(rtrim($api, '/') . '/xs/deldoc', ['auth' => ['drwcwh', 'drwcwh76'], 'timeout' => 10, 'connect_timeout' => 10, 'form_params' => $item]);
        $docRst = (string)$docRsp->getBody();

        return jsonDecode($docRst);
    } /* }}} */
}
