<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitde4315d61ed2bc51f4967f1f0f73ad94
{
    public static $prefixLengthsPsr4 = array (
        'l' => 
        array (
            'lara-crud\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'lara-crud\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitde4315d61ed2bc51f4967f1f0f73ad94::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitde4315d61ed2bc51f4967f1f0f73ad94::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitde4315d61ed2bc51f4967f1f0f73ad94::$classMap;

        }, null, ClassLoader::class);
    }
}
