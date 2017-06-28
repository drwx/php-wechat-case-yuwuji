<?php include "inc/head.php" ?>
<?php include "inc/nav.php" ?>
<div style="color:red; font-weight:bold"><?php if (isset($flash) && is_array($flash)) echo array_pop($flash); ?></div>
<div class="container">
  <h2>生成二维码</h2>
  <form class="form" action='/mp/qrcode' method="POST">
    <div class="form-group">
      渠道名：<input type="input" class="form-control" name="name" required value="">
    </div>
    <div class="form-group">
      渠道标识：<input type="input" class="form-control" name="scene" required value="">
    </div>
    <button type="submit" class="btn btn-default">提交</button>
  </form>
    <h2>现有渠道列表</h2>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>缓存key</th>
          <th>渠道名</th>
          <th>渠道标识</th>
          <th>URL</th>
          <th>ticket</th>
          <th>二维码链接</th>
          <th>查看数据</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($qrInfo as $key => $v): ?>
        <?php $da = jsonDecode($v); ?>
        <tr>
            <th scope="row"><?=$key;?></th>
            <td><?=$da['name'];?></td>
            <td style="word-break: break-all;"><?=$da['scene'];?></td>
            <td style="word-break: break-all;"><?=$da['url'];?></td>
            <td style="word-break: break-all;"><?=$da['ticket'];?></td>
            <td style="word-break: break-all;"><a href="<?=$da['qrurl'];?>" target="_blank">查看二维码</a></td>
            <td><a href="javscript::" data-scene="<?=$da['scene']?>" class="see-data">查看数据</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
</div>

<script>
  $(document).on('click','.see-data',function(e){
    e.preventDefault();
    var scene = $(this).data('scene');
    var url = "http://10.58.124.52:8080/?#/discover?_g=(refreshInterval:(display:Off,pause:!f,section:0,value:0),time:(from:now%2Fd,mode:quick,to:now%2Fd))&_a=(columns:!(_source),index:%5Blogstash-naicha-php-%5DYYYY.MM.DD,interval:auto,query:(query_string:(analyze_wildcard:!t,query:'"
    + encodeURIComponent('"EventKey  qrscene_' + scene + '"') +
    "')),sort:!('@timestamp',desc))";
    window.open(url,'_blank');
  })
</script>

<?php include "inc/foot.php" ?>
