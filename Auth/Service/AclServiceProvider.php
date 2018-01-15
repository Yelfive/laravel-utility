<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2018-01-15
 */

namespace fk\utility\Auth\Service;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class AclServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../../helpers/src/privilege/menu.php' => config_path('/menu.php'),
            // migrate
            __DIR__ . '/InitAclMigration.php' => database_path('migrations') . '/2017_11_24_023935_InitAclMigration.php'
        ]);
        $this->shutdown();
    }

    protected function shutdown()
    {
        register_shutdown_function(function () {
            $this->info('Running migrate');
            try {
                Artisan::call('migrate');
            } catch (\Exception $e) {
                echo $e->getMessage();
                echo "\n";
            }
            $this->generateModel();
            $this->mergeKernelMiddleware();
        });
    }

    protected function generateModel()
    {
        // Check Model exists
        $dir = app_path('/Models');
        $models = [\App\Models\Admin::class, \App\Models\AdminRole::class];
        foreach ($models as $model) {
            if (class_exists($model)) {
                $this->log("Skipped: $model exists");
                continue;
            }

            $basename = class_basename($model);

            Artisan::call('reference:model', [
                '--without-writing' => true,
                'tables' => [Str::snake(class_basename($model))],
            ]);
            $content = Artisan::output();
            if ($model === \App\Models\Admin::class) {
                $pattern = "/(?<=class $basename extends Model)/";
                $content = preg_replace($pattern, ' implements \Illuminate\Contracts\Auth\Authenticatable', $content);
            }
            file_put_contents("$dir/$basename.php", $content);
            $this->info("Model generated: $model");
        }
    }

    protected function mergeKernelMiddleware()
    {
        $this->info('Merging Kernel Middleware');

        $kernel_file = app_path('/Http/Kernel.php');
        $kernel = file_get_contents($kernel_file);
        $rc = new \ReflectionClass(\App\Http\Kernel::class);
        $property = $rc->getProperty('routeMiddleware');
        $property->setAccessible(true);
        $middleware = $property->getValue($rc->newInstanceWithoutConstructor());
        if (isset($middleware['auth.acl'])) return $this->log('Skipped: auth.acl already loaded.');

        $key = 'auth.acl';
        $middleware[$key] = \fk\utility\Auth\Service\AclServiceProvider::class;

        $pattern = '/(protected\s*\$routeMiddleware\s*=\s*\[)([^];]*)(\];)/';
        $space = str_repeat(' ', 8);
        $merged = "\n";
        foreach ($middleware as $alias => $class) {
            $merged .= "$space'$alias' => \\$class::class,\n";
        }
        $kernel = preg_replace($pattern, '\1' . $merged . str_repeat(' ', 4) . '\3', $kernel);
        file_put_contents($kernel_file, $kernel);

        $this->info("Kernel merged as `$key`");
    }

    /**
     * @param string $message
     * @return null
     */
    protected function info($message)
    {
        echo "\033[32m$message\033[0m\n";
    }

    /**
     * @param string $message
     * @return null
     */
    protected function log($message)
    {
        echo $message, "\n";
    }

}