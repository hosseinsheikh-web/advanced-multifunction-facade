<?php

namespace Palpalasi\AdvancedMultifunctionFacade;

use App\Domain\Entities\Models\Product;
use App\FacadeMiddle;
use App\Models\FackeModel;
use App\Models\Quize;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Facade as LaravelFacade;
use mysql_xdevapi\Exception;
use phpDocumentor\Reflection\Types\Static_;
use RuntimeException;

//Advanced multifunction Facade
class Facade extends LaravelFacade
{
    protected static $accessor = null;

    public function handle($request, Closure $next)
    {
        return $next($request);
    }
    public static function shouldBindTo($class)
    {
        static::$app->singleton(self::getFacadeAccessor(), $class);

        return static::class;
    }

    protected static function getFacadeAccessor()
    {
        if ($accessor = static::$accessor) {
            static::$accessor = null;

            return $accessor;
        }

        return static::class;
    }

    public static function passesAuthorization()
    {
        if (config('advanced-multifunction-facade.authorize_enable') && method_exists(static::class, 'authorize')) {
            return static::$app->call([static::class, 'authorize']);
        }

        return true;
    }

    public static function checkCallingAuthorization()
    {
        if (config('advanced-multifunction-facade.authorize_calling_is_enable') && method_exists(static::class, 'authorizeAndCalling')) {
            return static::$app->call([static::class, 'authorizeAndCalling']);
        }

        return true;
    }

    public static function __callStatic($method, $args)
    {
        if (! $instance = static::getFacadeRoot()) {
            throw new RuntimeException('A facade root has not been set.');
        }

        if (! static::passesAuthorization()) {
            throw new AuthorizationException;
        }

        if (! static::checkCallingAuthorization()) {
            // return;
            return \Mockery::mock();
            app()->bind($instance, function (){
                dd(__FILE__);
            });
           return;
        }

        static::listenCalling();
        static::listenCalled();
        Event::dispatch('calling: ' . static::class . '@' . $method, [$method, $args]);
        $result = $instance->$method(...$args);
        Event::dispatch('called: ' . static::class . '@' . $method, [$method, $args, $result]);

        return $result;
    }

    public static function listenCalling()
    {
        if (property_exists(static::class, 'calling_is_enable') && ! static::$calling_is_enable) {
            return;
        }
        if (config('advanced-multifunction-facade.calling_is_enable') && method_exists(static::class, 'calling')) {
            $callings = (new static())->calling();
            foreach ($callings as $methodName => $class) {
                Event::listen('calling: ' . static::class . '@' . $methodName, $class);
            }
        }
    }

    public static function listenCalled()
    {
        if (property_exists(static::class, 'called_is_enable') && ! static::$called_is_enable) {
            return;
        }

        if (config('advanced-multifunction-facade.called_is_enable') && method_exists(static::class, 'called')) {
            $callings = (new static())->called();
            foreach ($callings as $methodName => $class) {
                Event::listen('called: ' . static::class . '@' . $methodName, $class);
            }
        }
    }
}
