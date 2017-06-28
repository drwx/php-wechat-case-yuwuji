<!DOCTYPE html>
<html lang="en">
<head>
    <title>INFINITY导航</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/css/jquery.datetimepicker.css">
    <script src="/static/js/jquery.min.js"></script>
    <script src="/static/js/bootstrap.min.js"></script>
    <script src="/static/js/jquery.datetimepicker.full.min.js"></script>
    <script src="/static/js/vue.min.js"></script>
    <script src="/static/js/vue-bootstrap-table.js"></script>
    <script src="/static/js/jqPaginator.min.js"></script>
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
    <script>
        jQuery.extend({
            getParam: function(n,u) {
                if(!u) var u = window.location.search;
                var match = RegExp('[?&]' + n + '=([^&]*)').exec(u);
                return match && decodeURIComponent(match[1].replace(/\+/g, ' '));
            }
        });
    </script>
</head>
<body>
