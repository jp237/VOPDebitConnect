<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7c0d2629dd8e6f44d2ea7e939ae8246a
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
    );

    public static $prefixesPsr0 = array (
        'F' => 
        array (
            'Fhp' => 
            array (
                0 => __DIR__ . '/..' . '/mschindler83/fints-hbci-php/lib',
            ),
        ),
        'D' => 
        array (
            'Digitick\\Sepa' => 
            array (
                0 => __DIR__ . '/..' . '/digitick/sepa-xml/lib',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7c0d2629dd8e6f44d2ea7e939ae8246a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7c0d2629dd8e6f44d2ea7e939ae8246a::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit7c0d2629dd8e6f44d2ea7e939ae8246a::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}