<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit69a3ca8f6589e013b8f87ccdd2a32266
{
    public static $prefixesPsr0 = array (
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
            $loader->prefixesPsr0 = ComposerStaticInit69a3ca8f6589e013b8f87ccdd2a32266::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
