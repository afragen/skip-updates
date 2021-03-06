<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit83a858263d26671008e693807d4deb50
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Fragen\\Skip_Updates\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Fragen\\Skip_Updates\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Skip_Updates',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit83a858263d26671008e693807d4deb50::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit83a858263d26671008e693807d4deb50::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit83a858263d26671008e693807d4deb50::$classMap;

        }, null, ClassLoader::class);
    }
}
