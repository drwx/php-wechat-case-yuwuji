<?php include "inc/head.php" ?>
<?php include "inc/nav.php" ?>
<div class="container">
    <form class="form" action='/api/items/<?=$item->id;?>/edit' method="POST">
        <div class="form-group" style="margin: 10px 0px;">
            类型：<?php echo \App\Logic\Helper::htmlSelect(\App\Logic\Infinity::$aItemType, $item->type, 'class="btn btn-info" style="width: 100px;" name="type"'); ?>
        </div>
        <div class="form-group" style="margin: 10px 0px;">
            标题：<input type="input" class="form-control" name="title" placeholder="请填写内容标题" required value="<?=$item->title;?>"/>
        </div>
        <div class="form-group">
            简介：<textarea class="form-control" style="height: 80px;" name="brief" placeholder="请填写内容简介" required><?=$item->brief;?></textarea>
        </div>
        <div class="form-group">
            详情：<textarea class="form-control" style="height: 150px;" name="content" placeholder="请填写内容" required><?=$item->content;?></textarea>
        </div>
        <div class="form-group">
            标签：<input type="input" class="form-control" name="tags" placeholder="请填写标签，用,分隔" value="<?=$item->tags;?>" />
        </div>
        <div class="form-group">
            来源：<input type="input" class="form-control" name="source" placeholder="请填写内容来源，百度云盘，xxx网站等" value="<?=$item->source;?>" />
        </div>
        <button type="submit" class="btn btn-success form-control">提交</button>
    </form>
</div>
<?php include "inc/foot.php"; ?>

