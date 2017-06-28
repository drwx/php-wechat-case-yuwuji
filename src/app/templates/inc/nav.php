<div style="margin-top:10px;padding:0 10px;">
    <ul class="nav nav-tabs">
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="/content/.*">
                内容管理 <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li><a class="" href="/content/add">添加记录</a></li>
                <li><a class="" href="/content/list?state=1">记录列表</a></li>
                <li><a class="" href="/content/dellist?state=0">删除列表</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="/mp/.*">
                公众号管理 <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li><a class="" href="/mp/menu?type=1">自定义菜单管理</a></li>
                <li><a class="" href="/mp/psnmenu?type=2">个性化菜单管理</a></li>
                <li role="separator" class="divider"></li>
                <li><a class="" href="/mp/activeuser">消息推送候选用户</a></li>
                <li><a class="" href="/mp/oppush">消息推送(48小时)</a></li>
                <li role="separator" class="divider"></li>
                <li><a class="" href="/mp/usertag">用户标签管理</a></li>
                <li role="separator" class="divider"></li>
                <li><a class="" href="/mp/staff" target="_blank">客服列表 </a></li>
                <li><a class="" href="/mp/feedbacks">用户反馈</a></li>
                <li role="separator" class="divider"></li>
                <li><a class="" href="/mp/qrcode">渠道二维码</a></li>
            </ul>
        </li>
    </ul>
</div>
<ol class="breadcrumb" style="">
    <li><a href="/">首页</a></li>
</ol>
<script>
    var pathname = location.pathname;
    $('.nav li>a').each(function () {
        var title = '';
        if(new RegExp(this.pathname).test(pathname)){
            title = $(this).text();
            $(this).parent().addClass('active');
            $('.breadcrumb').append('<li>' + title + '</li>')
        }
    });
</script>
