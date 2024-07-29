<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/npm.php';

set('application', 'ave_api_auth');
set('repository', 'git@github.com:AsaciTech/ave_api_auth.git');

host('prod')
    ->set('remote_user', 'deployer')
    ->set('hostname', '10.10.160.19')
    ->set('deploy_path', '~/ave-auth');

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:migrate',
    // 'npm:install',
    // 'npm:run:prod',
    'deploy:publish'
]);

task('npm:run:prod', function () {
    cd('{{release_or_current_path}}');
    run('npm run prod');
});

after('deploy:failed', 'deploy:unlock');
