<!DOCTYPE html>
<html lang="en">
<head>
    <title>信息填写</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <style>
        .breadcrumb { margin-bottom: 10px; }
        .show-grid { margin-bottom: 15px; }
        .form-panel{
            padding:20px;
        }
        .mt10{
            margin-top:10px;
        }
    </style>
</head>
<body>
<div class="container">
    <div style="color:red; font-weight:bold"><?php if (isset($flash) && is_array($flash)) echo array_pop($flash); ?></div>
    <form class="form" action='/cwhinfo' method="POST">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">认真填写</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    姓名：<input required type="input"  style="width: 200px;" name="name" value=""><br/><br/>
                    邮箱：<input required type="input"  style="width: 200px;" name="email" value=""><br/><br/>
                    <input type="checkbox" name="hukou" value="1" id="b"><label for="b">是否常住户口在农村</label><span style="color:red;font-weight:bold;">(是就在前面框框点上)</span><br/><br/>
                    <input type="checkbox" name="pingkun" value="1" id="a"><label for="a">是否建档立卡贫困家庭</label><span style="color:red;font-weight:bold;">(是就在前面框框点上)</span><br/><br/>
                </div>
            </div>
        </div>
        <button type="submit" class="form-control btn btn-success">提交</button>
    </form>
</div>

<?php include "inc/foot.php" ?>
