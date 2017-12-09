<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita16caad90865e5134ad17e7719ba5398
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\Providers\\' => 14,
            'App\\Models\\' => 11,
            'App\\Core\\' => 9,
            'App\\Controllers\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\Providers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/providers',
        ),
        'App\\Models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/models',
        ),
        'App\\Core\\' => 
        array (
            0 => __DIR__ . '/../..' . '/main/core',
        ),
        'App\\Controllers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/controllers',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita16caad90865e5134ad17e7719ba5398::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita16caad90865e5134ad17e7719ba5398::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
