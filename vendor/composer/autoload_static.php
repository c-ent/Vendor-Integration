<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4dd7ac013e61a88e313ffb1482f92f3c
{
    public static $files = array (
        'decc78cc4436b1292c6c0d151b19445c' => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'p' => 
        array (
            'phpseclib3\\' => 11,
        ),
        'P' => 
        array (
            'ParagonIE\\ConstantTime\\' => 23,
        ),
        'I' => 
        array (
            'Includes\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'phpseclib3\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib',
        ),
        'ParagonIE\\ConstantTime\\' => 
        array (
            0 => __DIR__ . '/..' . '/paragonie/constant_time_encoding/src',
        ),
        'Includes\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Includes\\Base\\Activate' => __DIR__ . '/../..' . '/includes/Base/Activate.php',
        'Includes\\Base\\BaseController' => __DIR__ . '/../..' . '/includes/Base/BaseController.php',
        'Includes\\Base\\FetchCSV' => __DIR__ . '/../..' . '/includes/Base/FetchCSV.php',
        'Includes\\Base\\Deactivate' => __DIR__ . '/../..' . '/includes/Base/Deactivate.php',
        'Includes\\Base\\FTPFetcher' => __DIR__ . '/../..' . '/includes/Base/FTPFetcher.php',
        'Includes\\Init' => __DIR__ . '/../..' . '/includes/init.php',
        'Includes\\Pages\\Admin' => __DIR__ . '/../..' . '/includes/Pages/admin.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4dd7ac013e61a88e313ffb1482f92f3c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4dd7ac013e61a88e313ffb1482f92f3c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit4dd7ac013e61a88e313ffb1482f92f3c::$classMap;

        }, null, ClassLoader::class);
    }
}
