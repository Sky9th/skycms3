<?php
/**
 * Created by PhpStorm.
 * User: Sky9th
 * Date: 2017/4/29
 * Time: 10:10
 */

return [
    'status' =>  [
        -1 => '删除',
        0  => '<span class="text-warning">禁用</span>',
        1  => '<span class="text-success">正常</span>',
        2  => '待审核'
    ],

    'status_name' => [
        -1 => '删除',
        0  => '禁用',
        1  => '启用',
        2  => '退回'
    ],

    'action_type' => [
        '0' => '普通路由',
        '1' => '资源路由',
        '2' => '行为规则',
     ],

    'adv_type' => [
        '0' => '首页轮播图[1226px*460px]',
        '1' => '首页三连小图[316px*170px]',
        '2' => '移动端轮播图[1080px*504px]',
        '3' => '移动端明星单品广告图[720*440]',
     ],

    'ueditor' => "[
            'fullscreen', 'source', '|', 'undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough',  'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
            'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
            'fontfamily', 'fontsize', '|',
            'directionalityltr', 'directionalityrtl', 'indent', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
            'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'simpleupload', 'insertimage', 'attachment', 'map',  'insertcode',  'pagebreak', 'template', 'background', '|',
            'horizontal', 'date', 'time', 'spechars', 'snapscreen', 'wordimage', '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
            'print', 'searchreplace', 'drafts'
        ]",

    'linkage' => [
        'area',
        'article_category',
        'role',
        'subject'
    ],

    'extension' => [
        'file' => 'doc,docx,xls,xlsx,ppt,pdf,zip,rar,7z,jpg,png,gif,sql,xml,rss,mp4,flv,avi,mp3,amr,txt,cut',
        'image' => 'jpg,png,gif',
        'video' => 'mp4,flv,avi',
        'audio' => 'avi,mp3,amr',
        'document' => 'doc,docx,xls,xlsx,ppt,pdf,zip,rar,7z,sql,xml,rss'
    ]
];

?>