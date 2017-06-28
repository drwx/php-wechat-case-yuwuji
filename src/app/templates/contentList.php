<?php include "inc/head.php" ?>
<?php include "inc/nav.php" ?>
<div style="color:red; font-weight:bold"><?php if (isset($flash) && is_array($flash)) echo array_pop($flash); ?></div>
<div class="container">
    <div style="float: left;"><input id="kw" class="form-control" style="width: 300px; display: inline-block; margin-right: 5px;" type="text" placeholder="输入搜索关键词" value="<?=$kw;?>" /><input id="btnSrch" class="btn btn-success" style="display: inline-block;" type="button" value="搜索"/>&nbsp;<a href="/content/add" class="btn btn-info">添加记录</a></div>
    <div style="margin: 0px 0px 10px; float: right;" class="pagination"></div><div style="float: right; height: 34px; line-height: 34px;">共 <?=$result['total']; ?> 条</div>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>操作</th>
          <th>ID</th>
          <th>类型</th>
          <th>标题</th>
          <th>简介</th>
          <th>内容</th>
          <th>标签</th>
          <th>来源</th>
          <th>更新时间</th>
          <th>创建时间</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($result['data'] as $item): ?>
        <tr>
            <td style="width:70px; word-break: break-all;"><a href="/content/<?=$item->id;?>/edit" class="btn btn-info">编辑</a><a class="delOp btn btn-danger" href="javascript:void(0);" state="<?php if ($item->state == 1) {echo 0;} else {echo 1;}?>" purl="/api/items/<?=$item->id;?>/edit" style=""><?php if ($item->state == 1) { echo '删除'; } else { echo '恢复'; } ?></a></td>
            <th scope="row"><?=$item->id;?></th>
            <td style="width:80px; word-break: break-all;">
                <?=\App\Logic\Infinity::$aItemType[$item->type]; ?>
            </td>
            <td style="width:150px; word-break: break-all;"><?=$item->title;?></td>
            <td style="width:250px; word-break: break-all;"><?=mbTrunc($item->brief, 50);?></td>
            <td style="width:250px; word-break: break-all;"><?=mbTrunc($item->content, 50);?></td>
            <td style="width:150px; word-break: break-all;"><?=$item->tags;?></td>
            <td style="word-break: break-all;"><?=$item->source;?></td>
            <td style="word-break: break-all;"><?=$item->updated_at;?></td>
            <td style="word-break: break-all;"><?=$item->created_at;?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div style="margin: 0px 0px 10px; float: right;" class="pagination"></div><div style="float: right; height: 34px; line-height: 34px;">共 <?=$result['total']; ?> 条</div>
</div>
<script>
$('.pagination').jqPaginator({
    totalCounts: <?=($result['total'] == 0)?1:$result['total']; ?>,
    pageSize: <?=$result['size']; ?>,
    visiblePages: 10,
    currentPage: <?=$result['offset']/$result['size'] + 1; ?>,
    onPageChange: function (num, type) {
        if (type == 'change') {
            var state = $.getParam('state') ? $.getParam('state') : 1;
            var kw = $.getParam('kw') ? $.getParam('kw') : '';
            location.href='?state=' + state + '&offset=' + (num - 1) * <?=$result['size'];?> + '&kw=' + kw;
        }
    }
});

$('#btnSrch').on('click', function() {
    var kw = $('#kw').val();
//    var offset = $.getParam('offset') ? $.getParam('offset') : 0;
    var state = $.getParam('state') ? $.getParam('state') : 1;
    location.href='?state=' + state + '&offset=0&kw=' + kw;
});
$('#kw').on('keydown', function(e) {
    if (e.which == 13) {
        e.preventDefault();
        $('#btnSrch').click();
    }
});

$('.delOp').on('click', function () {
    ele = $(this);
    var url = ele.attr('purl');
    var state = ele.attr('state');
    $.post(url, {'state' : state}, function(data) {
        ele.closest('tr').hide();
    });
});
</script>

<?php include "inc/foot.php" ?>
