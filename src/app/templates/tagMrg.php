<?php include "inc/head.php" ?>
<?php include "inc/nav.php" ?>
<div style="color:red; font-weight:bold"><?php if (isset($flash) && is_array($flash)) echo array_pop($flash); ?></div>
<div class="container">
<form action="/api/usertag" method="post">
<input type="submit" class="btn btn-success" value="同步用户标签">
</form>

<div style="margin-top: 10px;">
<div>高级用户列表：</div>
<?php
print('<pre>');
print(jsonEncode($userList));
?>
</div>
</div>
<?php include "inc/foot.php" ?>
