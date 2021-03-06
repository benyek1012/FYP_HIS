<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7d2dd633a57080805375f8b878c976f5
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'GpsLab\\Component\\Base64UID\\' => 27,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'GpsLab\\Component\\Base64UID\\' => 
        array (
            0 => __DIR__ . '/..' . '/gpslab/base64uid/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7d2dd633a57080805375f8b878c976f5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7d2dd633a57080805375f8b878c976f5::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit7d2dd633a57080805375f8b878c976f5::$classMap;

        }, null, ClassLoader::class);
    }
}
