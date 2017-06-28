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
    <h2>信息列表（共<?=$count?>人）<a href="/excel" target="_blank">导出Excel</a></h2>
    <table class="table">
        <tr class="row">
            <th>用户</th>
            <th>邮箱</th>
            <th>是否常住户口在农村</th>
            <th>是否建档立卡贫困家庭</th>
        </tr>
        <?php foreach ($users as $feed): ?>
            <tr class="row">
                <td><?= $feed['name']; ?></td>
                <td><?= $feed['email']; ?></td>
                <td><?= $feed['hukou'] == 0 ? '否' : '是'; ?></td>
                <td><?= $feed['pingkun'] == 0 ? '否' : '是'; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include "inc/foot.php" ?>
