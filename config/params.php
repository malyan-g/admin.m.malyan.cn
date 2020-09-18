<?php

return [
    'title' => 'M - 后台管理系统',
    'adminEmail' => 'admin@malyan.cn',
    'hiddenMenu' => ['create', 'update', 'view', 'auth', 'sort', 'error'],
    'allowedRole' => ['超级管理员'],
    'allowedRoute' => ['admin/create-password', 'permission/menu'],
];
