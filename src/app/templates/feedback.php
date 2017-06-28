<?php include "inc/head.php" ?>
<?php include "inc/nav.php" ?>
<div class="container">
    <h2>反馈信息列表</h2>
    <table class="table">
        <tr class="row">
            <th>用户ID</th>
            <th>用户昵称</th>
            <th>反馈内容</th>
            <th>时间</th>
        </tr>
        <?php foreach ($words as $feed): ?>
            <tr class="row">
                <td><?= $feed['openId']; ?></td>
                <td><?= $feed['nickname']; ?></td>
                <td><?= $feed['feed']; ?></td>
                <td><?= date('Y-m-d H:i:s', $feed['ts']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include "inc/foot.php" ?>
