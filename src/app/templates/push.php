<?php include "inc/head.php" ?>
<?php include "inc/nav.php" ?>
        <div style="color:red; font-weight:bold"><?php if (isset($flash) && is_array($flash)) echo array_pop($flash); ?></div>
        <div class="container">
            <h2>用户推送设置</h2>
            <div class="form-group">
                推送内容：<textarea id="msg" style="width:500px; height: 100px;"></textarea>
            </div>
            <div class="form-group">
                测试用户：<textarea id="users" placeholder="用,分隔openid" style="width:500px; height: 100px;"></textarea>
            </div>
            <div class="form-group" style="width:500px;">
                <input type="button" id="subTest" value="推送测试用户" class="btn-warning" />
                <input type="button" id="subAll" value="推送所有用户" class="btn-success" />
                <a class="" href="/mp/activeuser" target="_blank">查看推送活跃候选用户 »</a>
            </div>
            <div style="background-color:grey;" id="msgInfo"></div>
    <h2>历史信息</h2>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>时间</th>
          <th>信息</th>
          <th>用户列表</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $v): ?>
        <?php $da = jsonDecode($v); ?>
        <tr>
            <th scope="row"><?=date('Y-m-d H:i:s', $da['ts']);?></th>
            <td><?=$da['msg'];?></td>
            <td style="word-break: break-all;"><?=jsonEncode($da['sent']);?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
        </div>
        <script>
            $('#subAll').click(function() {
                if (confirm('确定推送么？')) {
                    $.post('/mp/push2user', {
                        'msg': $('#msg').val(),
                        'test': 0
                    }, function(res) {
                        $('#msgInfo').html(res.msg + ' - [INFO]' + JSON.stringify(res.data));
                        console.log(res);
                    }, 'json');
                }
            });
            $('#subTest').click(function() {
                if (confirm('确定推送么？')) {
                    $.post('/mp/push2user', {
                        'msg': $('#msg').val(),
                        'users': $('#users').val(),
                        'test': 1
                    }, function(res) {
                        $('#msgInfo').html(res.msg);
                        console.log(res);
                    }, 'json');
                }
            });
        </script>
<?php include "inc/foot.php" ?>
