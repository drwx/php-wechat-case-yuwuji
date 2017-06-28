<?php include "inc/head.php" ?>
<?php include "inc/nav.php" ?>
<div class="container">
    <div style="color:red; font-weight:bold"><?php if (isset($flash) && is_array($flash)) echo array_pop($flash); ?></div>
    <div style="color:green;">自定义菜单最多包括3个一级菜单，每个一级菜单最多包含5个二级菜单。</div>
    <div style="color:green;">一级菜单最多4个汉字，二级菜单最多7个汉字，多出来的部分将会以“...”代替。</div>
    <div style="color:red; font-weight: bold;">菜单名必填，只要填了菜单名，即为有效菜单，如果一级菜单不填，配置的二级菜单无效。</div>
    <form class="form" action='/api/menus' method="POST">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">左边菜单</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    一级菜单：
                    <?php echo \App\Logic\Helper::htmlSelect(\App\Logic\Infinity::$aMenuType, $menus ? $menus['left']['top']['type'] : '', 'class="btn btn-info" style="width: 150px;" name="menus[left][top][type]"'); ?>
                    🐷 菜单名【显示名】：<input type="input" class="form-inline" style="width: 200px;" name="menus[left][top][name]" value="<?=$menus ? $menus['left']['top']['name'] : '';?>">
                    🐯 KEY值【不明勿动】：<input type="input" class="form-inline" style="width: 200px;" name="menus[left][top][value]" value="<?=$menus ? $menus['left']['top']['value'] : '';?>">
                </div>
                <?php for ($i = 0; $i < 5; $i++) { ?>
                <div class="form-group">
                    二级菜单：
                    <?php echo \App\Logic\Helper::htmlSelect(\App\Logic\Infinity::$aMenuType, $menus ? $menus['left']['sub']['type'][$i] : '', 'class="btn btn-info" style="width: 150px;" name="menus[left][sub][type][]"'); ?>
                    🐷 菜单名【显示名】：<input type="input" class="form-inline" style="width: 200px;" name="menus[left][sub][name][]" value="<?=$menus ? $menus['left']['sub']['name'][$i] : '';?>">
                    🐯 KEY值【不明勿动】：<input type="input" class="form-inline" style="width: 200px;" name="menus[left][sub][value][]" value="<?=$menus ? $menus['left']['sub']['value'][$i] : '';?>">
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">中间菜单</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    一级菜单：
                    <?php echo \App\Logic\Helper::htmlSelect(\App\Logic\Infinity::$aMenuType, $menus ? $menus['center']['top']['type'] : '', 'class="btn btn-info" style="width: 150px;" name="menus[center][top][type]"'); ?>
                    🐷 菜单名【显示名】：<input type="input" class="form-inline" style="width: 200px;" name="menus[center][top][name]" value="<?=$menus ? $menus['center']['top']['name'] : '';?>">
                    🐯 KEY值【不明勿动】：<input type="input" class="form-inline" style="width: 200px;" name="menus[center][top][value]" value="<?=$menus ? $menus['center']['top']['value'] : '';?>">
                </div>
                <?php for ($i = 0; $i < 5; $i++) { ?>
                <div class="form-group">
                    二级菜单：
                    <?php echo \App\Logic\Helper::htmlSelect(\App\Logic\Infinity::$aMenuType, $menus ? $menus['center']['sub']['type'][$i] : '', 'class="btn btn-info" style="width: 150px;" name="menus[center][sub][type][]"'); ?>
                    🐷 菜单名【显示名】：<input type="input" class="form-inline" style="width: 200px;" name="menus[center][sub][name][]" value="<?=$menus ? $menus['center']['sub']['name'][$i] : '';?>">
                    🐯 KEY值【不明勿动】：<input type="input" class="form-inline" style="width: 200px;" name="menus[center][sub][value][]" value="<?=$menus ? $menus['center']['sub']['value'][$i] : '';?>">
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">右边菜单</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    一级菜单：
                    <?php echo \App\Logic\Helper::htmlSelect(\App\Logic\Infinity::$aMenuType, $menus ? $menus['right']['top']['type'] : '', 'class="btn btn-info" style="width: 150px;" name="menus[right][top][type]"'); ?>
                    🐷 菜单名【显示名】：<input type="input" class="form-inline" style="width: 200px;" name="menus[right][top][name]" value="<?=$menus ? $menus['right']['top']['name'] : '';?>">
                    🐯 KEY值【不明勿动】：<input type="input" class="form-inline" style="width: 200px;" name="menus[right][top][value]" value="<?=$menus ? $menus['right']['top']['value'] : '';?>">
                </div>
                <?php for ($i = 0; $i < 5; $i++) { ?>
                <div class="form-group">
                    二级菜单：
                    <?php echo \App\Logic\Helper::htmlSelect(\App\Logic\Infinity::$aMenuType, $menus ? $menus['right']['sub']['type'][$i] : '', 'class="btn btn-info" style="width: 150px;" name="menus[right][sub][type][]"'); ?>
                    🐷 菜单名【显示名】：<input type="input" class="form-inline" style="width: 200px;" name="menus[right][sub][name][]" value="<?=$menus ? $menus['right']['sub']['name'][$i] : '';?>">
                    🐯 KEY值【不明勿动】：<input type="input" class="form-inline" style="width: 200px;" name="menus[right][sub][value][]" value="<?=$menus ? $menus['right']['sub']['value'][$i] : '';?>">
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">个性化菜单</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    用户标签：
                    <?php echo \App\Logic\Helper::htmlSelect($tags, isset($menus['matchRule']['tag_id']) ? $menus['matchRule']['tag_id'] : '', 'class="btn btn-info" style="width: 150px;" name="menus[matchRule][tag_id]"'); ?>
                </div>
                <div class="form-group">
                    用户性别：
                    <?php echo \App\Logic\Helper::htmlSelect(['' => '选择性别', 1 => '男', 2 => '女'], isset($menus['matchRule']['sex']) ? $menus['matchRule']['sex'] : '', 'class="btn btn-info" style="width: 150px;" name="menus[matchRule][sex]"'); ?>
                </div>
                <div class="form-group">
                    用户地区：
                    <div id="global_location" style="display: inline-block;">
                        <select data-value="<?=isset($menus['matchRule']['country']) ? $menus['matchRule']['country'] : ''; ?>" name="menus[matchRule][country]" class="btn btn-info country" data-first-title="选择国家"></select>
                        <select data-value="<?=isset($menus['matchRule']['province']) ? $menus['matchRule']['province'] : ''; ?>" name="menus[matchRule][province]" class="btn btn-info province" data-required="true"></select>
                        <select data-value="<?=isset($menus['matchRule']['city']) ? $menus['matchRule']['city'] : ''; ?>" name="menus[matchRule][city]" class="btn btn-info city" data-required="true"></select>
                          <!-- <select data-value="" name="menus[matchRule][region]" class="btn btn-info region" data-required="true"></select> -->
                    </div>
                </div>
                <div class="form-group">
                    客户端版本：
                    <?php echo \App\Logic\Helper::htmlSelect(['' => '选择客户端', 1 => 'IOS', 2 => 'Android', 3 => 'Others'], isset($menus['matchRule']['client_platform_type']) ? $menus['matchRule']['client_platform_type'] : '', 'class="btn btn-info" style="width: 150px;" name="menus[matchRule][client_platform_type]"'); ?>
                </div>
            </div>
        </div>
        <button type="submit" class="form-control btn btn-success">更新</button>
    </form>
</div>
<script src="/static/js/cxSelect/jquery.cxselect.min.js"></script>
<script>
$('#global_location').cxSelect({
  url: '/static/js/cxSelect/globalDataWx.json',
  selects: ['country', 'province', 'city'/* , 'region' */],
  nodata: 'none'
});
</script>

<?php include "inc/foot.php" ?>
