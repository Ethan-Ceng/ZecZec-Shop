<?php











namespace Composer;

use Composer\Autoload\ClassLoader;
use Composer\Semver\VersionParser;








class InstalledVersions
{
private static $installed = array (
  'root' => 
  array (
    'pretty_version' => '1.0.0+no-version-set',
    'version' => '1.0.0.0',
    'aliases' => 
    array (
    ),
    'reference' => NULL,
    'name' => 'topthink/think',
  ),
  'versions' => 
  array (
    'adbario/php-dot-notation' => 
    array (
      'pretty_version' => '2.5.0',
      'version' => '2.5.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '081e2cca50c84bfeeea2e3ef9b2c8d206d80ccae',
    ),
    'alibabacloud/tea' => 
    array (
      'pretty_version' => '3.2.1',
      'version' => '3.2.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '1619cb96c158384f72b873e1f85de8b299c9c367',
    ),
    'alibabacloud/tea-fileform' => 
    array (
      'pretty_version' => '0.3.4',
      'version' => '0.3.4.0',
      'aliases' => 
      array (
      ),
      'reference' => '4bf0c75a045c8115aa8cb1a394bd08d8bb833181',
    ),
    'alipaysdk/easysdk' => 
    array (
      'pretty_version' => '2.2.3',
      'version' => '2.2.3.0',
      'aliases' => 
      array (
      ),
      'reference' => 'c6008839a22a5fca08e9f8536730f7abfed522d5',
    ),
    'aliyuncs/oss-sdk-php' => 
    array (
      'pretty_version' => 'v2.7.1',
      'version' => '2.7.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'ce5d34dae9868237a32248788ea175c7e9da14b1',
    ),
    'bacon/bacon-qr-code' => 
    array (
      'pretty_version' => '2.0.8',
      'version' => '2.0.8.0',
      'aliases' => 
      array (
      ),
      'reference' => '8674e51bb65af933a5ffaf1c308a660387c35c22',
    ),
    'dasprid/enum' => 
    array (
      'pretty_version' => '1.0.6',
      'version' => '1.0.6.0',
      'aliases' => 
      array (
      ),
      'reference' => '8dfd07c6d2cf31c8da90c53b83c026c7696dda90',
    ),
    'endroid/qr-code' => 
    array (
      'pretty_version' => '4.8.5',
      'version' => '4.8.5.0',
      'aliases' => 
      array (
      ),
      'reference' => '0db25b506a8411a5e1644ebaa67123a6eb7b6a77',
    ),
    'ezyang/htmlpurifier' => 
    array (
      'pretty_version' => 'v4.17.0',
      'version' => '4.17.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'bbc513d79acf6691fa9cf10f192c90dd2957f18c',
    ),
    'firebase/php-jwt' => 
    array (
      'pretty_version' => 'v6.10.1',
      'version' => '6.10.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '500501c2ce893c824c801da135d02661199f60c5',
    ),
    'guzzlehttp/command' => 
    array (
      'pretty_version' => '1.3.1',
      'version' => '1.3.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '0eebc653784f4902b3272e826fe8e88743d14e77',
    ),
    'guzzlehttp/guzzle' => 
    array (
      'pretty_version' => '7.9.2',
      'version' => '7.9.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'd281ed313b989f213357e3be1a179f02196ac99b',
    ),
    'guzzlehttp/guzzle-services' => 
    array (
      'pretty_version' => '1.4.1',
      'version' => '1.4.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'bcab7c0d61672b606510a6fe5af3039d04968c0f',
    ),
    'guzzlehttp/promises' => 
    array (
      'pretty_version' => '2.0.3',
      'version' => '2.0.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '6ea8dd08867a2a42619d65c3deb2c0fcbf81c8f8',
    ),
    'guzzlehttp/psr7' => 
    array (
      'pretty_version' => '2.7.0',
      'version' => '2.7.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'a70f5c95fb43bc83f07c9c948baa0dc1829bf201',
    ),
    'guzzlehttp/uri-template' => 
    array (
      'pretty_version' => 'v1.0.3',
      'version' => '1.0.3.0',
      'aliases' => 
      array (
      ),
      'reference' => 'ecea8feef63bd4fef1f037ecb288386999ecc11c',
    ),
    'kosinix/grafika' => 
    array (
      'pretty_version' => 'dev-master',
      'version' => 'dev-master',
      'aliases' => 
      array (
        0 => '9999999-dev',
      ),
      'reference' => '211f61fc334b8b36616b23e8af7c5727971d96ee',
    ),
    'league/flysystem' => 
    array (
      'pretty_version' => '2.5.0',
      'version' => '2.5.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '8aaffb653c5777781b0f7f69a5d937baf7ab6cdb',
    ),
    'league/mime-type-detection' => 
    array (
      'pretty_version' => '1.15.0',
      'version' => '1.15.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'ce0f4d1e8a6f4eb0ddff33f57c69c50fd09f4301',
    ),
    'lvht/geohash' => 
    array (
      'pretty_version' => 'v1.1.0',
      'version' => '1.1.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'bbba3e1b487f0ec2e5e666c1bc9d1d4277990a29',
    ),
    'maennchen/zipstream-php' => 
    array (
      'pretty_version' => '3.1.0',
      'version' => '3.1.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'b8174494eda667f7d13876b4a7bfef0f62a7c0d1',
    ),
    'markbaker/complex' => 
    array (
      'pretty_version' => '3.0.2',
      'version' => '3.0.2.0',
      'aliases' => 
      array (
      ),
      'reference' => '95c56caa1cf5c766ad6d65b6344b807c1e8405b9',
    ),
    'markbaker/matrix' => 
    array (
      'pretty_version' => '3.0.1',
      'version' => '3.0.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '728434227fe21be27ff6d86621a1b13107a2562c',
    ),
    'monolog/monolog' => 
    array (
      'pretty_version' => '2.9.3',
      'version' => '2.9.3.0',
      'aliases' => 
      array (
      ),
      'reference' => 'a30bfe2e142720dfa990d0a7e573997f5d884215',
    ),
    'myclabs/php-enum' => 
    array (
      'pretty_version' => '1.8.4',
      'version' => '1.8.4.0',
      'aliases' => 
      array (
      ),
      'reference' => 'a867478eae49c9f59ece437ae7f9506bfaa27483',
    ),
    'nyholm/psr7' => 
    array (
      'pretty_version' => '1.8.2',
      'version' => '1.8.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'a71f2b11690f4b24d099d6b16690a90ae14fc6f3',
    ),
    'nyholm/psr7-server' => 
    array (
      'pretty_version' => '1.1.0',
      'version' => '1.1.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '4335801d851f554ca43fa6e7d2602141538854dc',
    ),
    'overtrue/socialite' => 
    array (
      'pretty_version' => '4.11.1',
      'version' => '4.11.1.0',
      'aliases' => 
      array (
      ),
      'reference' => 'de873dab83b8c0d6d94716b0c8c4dc0336d391af',
    ),
    'php-http/async-client-implementation' => 
    array (
      'provided' => 
      array (
        0 => '*',
      ),
    ),
    'php-http/client-implementation' => 
    array (
      'provided' => 
      array (
        0 => '*',
      ),
    ),
    'php-http/message-factory-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'phpoffice/phpspreadsheet' => 
    array (
      'pretty_version' => '1.29.1',
      'version' => '1.29.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '59ee38f7480904cd6487e5cbdea4d80ff2758719',
    ),
    'psr/cache' => 
    array (
      'pretty_version' => '3.0.0',
      'version' => '3.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'aa5030cfa5405eccfdcb1083ce040c2cb8d253bf',
    ),
    'psr/cache-implementation' => 
    array (
      'provided' => 
      array (
        0 => '2.0|3.0',
      ),
    ),
    'psr/container' => 
    array (
      'pretty_version' => '2.0.2',
      'version' => '2.0.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'c71ecc56dfe541dbd90c5360474fbc405f8d5963',
    ),
    'psr/http-client' => 
    array (
      'pretty_version' => '1.0.3',
      'version' => '1.0.3.0',
      'aliases' => 
      array (
      ),
      'reference' => 'bb5906edc1c324c9a05aa0873d40117941e5fa90',
    ),
    'psr/http-client-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'psr/http-factory' => 
    array (
      'pretty_version' => '1.1.0',
      'version' => '1.1.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '2b4765fddfe3b508ac62f829e852b1501d3f6e8a',
    ),
    'psr/http-factory-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'psr/http-message' => 
    array (
      'pretty_version' => '1.1',
      'version' => '1.1.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'cb6ce4845ce34a8ad9e68117c10ee90a29919eba',
    ),
    'psr/http-message-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0',
      ),
    ),
    'psr/log' => 
    array (
      'pretty_version' => '3.0.2',
      'version' => '3.0.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f16e1d5863e37f8d8c2a01719f5b34baa2b714d3',
    ),
    'psr/log-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0.0 || 2.0.0 || 3.0.0',
      ),
    ),
    'psr/simple-cache' => 
    array (
      'pretty_version' => '3.0.0',
      'version' => '3.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '764e0b3939f5ca87cb904f570ef9be2d78a07865',
    ),
    'psr/simple-cache-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.0|2.0|3.0',
      ),
    ),
    'qcloud/cos-sdk-v5' => 
    array (
      'pretty_version' => 'v2.6.14',
      'version' => '2.6.14.0',
      'aliases' => 
      array (
      ),
      'reference' => 'f59af74e7a45de259786e378e6b45cc61039caa3',
    ),
    'qiniu/php-sdk' => 
    array (
      'pretty_version' => 'v7.12.1',
      'version' => '7.12.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '88952a65e97b4c2fae3f954bc4419ba14e42f154',
    ),
    'ralouphie/getallheaders' => 
    array (
      'pretty_version' => '3.0.3',
      'version' => '3.0.3.0',
      'aliases' => 
      array (
      ),
      'reference' => '120b605dfeb996808c31b6477290a714d356e822',
    ),
    'symfony/cache' => 
    array (
      'pretty_version' => 'v6.4.11',
      'version' => '6.4.11.0',
      'aliases' => 
      array (
      ),
      'reference' => '36daef8fce88fe0b9a4f8cf4c342ced5c05616dc',
    ),
    'symfony/cache-contracts' => 
    array (
      'pretty_version' => 'v3.5.0',
      'version' => '3.5.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'df6a1a44c890faded49a5fca33c2d5c5fd3c2197',
    ),
    'symfony/cache-implementation' => 
    array (
      'provided' => 
      array (
        0 => '1.1|2.0|3.0',
      ),
    ),
    'symfony/deprecation-contracts' => 
    array (
      'pretty_version' => 'v3.5.0',
      'version' => '3.5.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '0e0d29ce1f20deffb4ab1b016a7257c4f1e789a1',
    ),
    'symfony/http-client' => 
    array (
      'pretty_version' => 'v6.4.11',
      'version' => '6.4.11.0',
      'aliases' => 
      array (
      ),
      'reference' => '4c92046bb788648ff1098cc66da69aa7eac8cb65',
    ),
    'symfony/http-client-contracts' => 
    array (
      'pretty_version' => 'v3.5.0',
      'version' => '3.5.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '20414d96f391677bf80078aa55baece78b82647d',
    ),
    'symfony/http-client-implementation' => 
    array (
      'provided' => 
      array (
        0 => '3.0',
      ),
    ),
    'symfony/http-foundation' => 
    array (
      'pretty_version' => 'v6.4.10',
      'version' => '6.4.10.0',
      'aliases' => 
      array (
      ),
      'reference' => '117f1f20a7ade7bcea28b861fb79160a21a1e37b',
    ),
    'symfony/mime' => 
    array (
      'pretty_version' => 'v6.4.11',
      'version' => '6.4.11.0',
      'aliases' => 
      array (
      ),
      'reference' => 'dba5d5f6073baf7a3576b580cc4a208b4ca00553',
    ),
    'symfony/polyfill-intl-idn' => 
    array (
      'pretty_version' => 'v1.31.0',
      'version' => '1.31.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'c36586dcf89a12315939e00ec9b4474adcb1d773',
    ),
    'symfony/polyfill-intl-normalizer' => 
    array (
      'pretty_version' => 'v1.31.0',
      'version' => '1.31.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '3833d7255cc303546435cb650316bff708a1c75c',
    ),
    'symfony/polyfill-mbstring' => 
    array (
      'pretty_version' => 'v1.31.0',
      'version' => '1.31.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '85181ba99b2345b0ef10ce42ecac37612d9fd341',
    ),
    'symfony/polyfill-php80' => 
    array (
      'pretty_version' => 'v1.31.0',
      'version' => '1.31.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '60328e362d4c2c802a54fcbf04f9d3fb892b4cf8',
    ),
    'symfony/polyfill-php81' => 
    array (
      'pretty_version' => 'v1.31.0',
      'version' => '1.31.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '4a4cfc2d253c21a5ad0e53071df248ed48c6ce5c',
    ),
    'symfony/polyfill-php83' => 
    array (
      'pretty_version' => 'v1.31.0',
      'version' => '1.31.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '2fb86d65e2d424369ad2905e83b236a8805ba491',
    ),
    'symfony/psr-http-message-bridge' => 
    array (
      'pretty_version' => 'v2.3.1',
      'version' => '2.3.1.0',
      'aliases' => 
      array (
      ),
      'reference' => '581ca6067eb62640de5ff08ee1ba6850a0ee472e',
    ),
    'symfony/service-contracts' => 
    array (
      'pretty_version' => 'v3.5.0',
      'version' => '3.5.0.0',
      'aliases' => 
      array (
      ),
      'reference' => 'bd1d9e59a81d8fa4acdcea3f617c581f7475a80f',
    ),
    'symfony/var-dumper' => 
    array (
      'pretty_version' => 'v7.1.4',
      'version' => '7.1.4.0',
      'aliases' => 
      array (
      ),
      'reference' => 'a5fa7481b199090964d6fd5dab6294d5a870c7aa',
    ),
    'symfony/var-exporter' => 
    array (
      'pretty_version' => 'v7.1.2',
      'version' => '7.1.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'b80a669a2264609f07f1667f891dbfca25eba44c',
    ),
    'topthink/framework' => 
    array (
      'pretty_version' => 'v8.0.4',
      'version' => '8.0.4.0',
      'aliases' => 
      array (
      ),
      'reference' => '846432655a82614a874cdaaac7be6870940b14d2',
    ),
    'topthink/think' => 
    array (
      'pretty_version' => '1.0.0+no-version-set',
      'version' => '1.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => NULL,
    ),
    'topthink/think-cors' => 
    array (
      'pretty_version' => 'v1.0.2',
      'version' => '1.0.2.0',
      'aliases' => 
      array (
      ),
      'reference' => '822d90b357daa5aa5e1d01668615599f428ad132',
    ),
    'topthink/think-filesystem' => 
    array (
      'pretty_version' => 'v2.0.2',
      'version' => '2.0.2.0',
      'aliases' => 
      array (
      ),
      'reference' => 'c08503232fcae0c3c7fefae5e6b5c841ffe09f2f',
    ),
    'topthink/think-helper' => 
    array (
      'pretty_version' => 'v3.1.8',
      'version' => '3.1.8.0',
      'aliases' => 
      array (
      ),
      'reference' => '612eea76eec2a22f41b0e24be27f49454e4fd5f5',
    ),
    'topthink/think-multi-app' => 
    array (
      'pretty_version' => 'v1.0.17',
      'version' => '1.0.17.0',
      'aliases' => 
      array (
      ),
      'reference' => '4055a6187296ac16c0bc7bbab4ed5d92f82f791c',
    ),
    'topthink/think-orm' => 
    array (
      'pretty_version' => 'v3.0.27',
      'version' => '3.0.27.0',
      'aliases' => 
      array (
      ),
      'reference' => '9c691f6bedc8a47103767a9a682810308c30c010',
    ),
    'topthink/think-trace' => 
    array (
      'pretty_version' => 'v1.6',
      'version' => '1.6.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '136cd5d97e8bdb780e4b5c1637c588ed7ca3e142',
    ),
    'topthink/think-worker' => 
    array (
      'pretty_version' => 'v4.0.0',
      'version' => '4.0.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '2bd716582c912075e38036f896a2b45e64bf858d',
    ),
    'w7corp/easywechat' => 
    array (
      'pretty_version' => '6.7.0',
      'version' => '6.7.0.0',
      'aliases' => 
      array (
      ),
      'reference' => '85a49397713662c5b98d661a5a9ad8f5566042c4',
    ),
    'wechatpay/wechatpay' => 
    array (
      'pretty_version' => '1.4.9',
      'version' => '1.4.9.0',
      'aliases' => 
      array (
      ),
      'reference' => '2cabc8a15136050c4ee61083cd24959114756a03',
    ),
    'workerman/gateway-worker' => 
    array (
      'pretty_version' => 'v3.0.22',
      'version' => '3.0.22.0',
      'aliases' => 
      array (
      ),
      'reference' => 'a615036c482d11f68b693998575e804752ef9068',
    ),
    'workerman/mysql' => 
    array (
      'pretty_version' => 'v1.0.8',
      'version' => '1.0.8.0',
      'aliases' => 
      array (
      ),
      'reference' => '5929dcb03693073dca2fc3cde3cc93382b55c366',
    ),
    'workerman/workerman' => 
    array (
      'pretty_version' => 'v3.5.34',
      'version' => '3.5.34.0',
      'aliases' => 
      array (
      ),
      'reference' => 'fe4fc5ecc44d0410a22214d9e8679e2fc3795f5a',
    ),
  ),
);
private static $canGetVendors;
private static $installedByVendor = array();







public static function getInstalledPackages()
{
$packages = array();
foreach (self::getInstalled() as $installed) {
$packages[] = array_keys($installed['versions']);
}

if (1 === \count($packages)) {
return $packages[0];
}

return array_keys(array_flip(\call_user_func_array('array_merge', $packages)));
}









public static function isInstalled($packageName)
{
foreach (self::getInstalled() as $installed) {
if (isset($installed['versions'][$packageName])) {
return true;
}
}

return false;
}














public static function satisfies(VersionParser $parser, $packageName, $constraint)
{
$constraint = $parser->parseConstraints($constraint);
$provided = $parser->parseConstraints(self::getVersionRanges($packageName));

return $provided->matches($constraint);
}










public static function getVersionRanges($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

$ranges = array();
if (isset($installed['versions'][$packageName]['pretty_version'])) {
$ranges[] = $installed['versions'][$packageName]['pretty_version'];
}
if (array_key_exists('aliases', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['aliases']);
}
if (array_key_exists('replaced', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['replaced']);
}
if (array_key_exists('provided', $installed['versions'][$packageName])) {
$ranges = array_merge($ranges, $installed['versions'][$packageName]['provided']);
}

return implode(' || ', $ranges);
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['version'])) {
return null;
}

return $installed['versions'][$packageName]['version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getPrettyVersion($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['pretty_version'])) {
return null;
}

return $installed['versions'][$packageName]['pretty_version'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getReference($packageName)
{
foreach (self::getInstalled() as $installed) {
if (!isset($installed['versions'][$packageName])) {
continue;
}

if (!isset($installed['versions'][$packageName]['reference'])) {
return null;
}

return $installed['versions'][$packageName]['reference'];
}

throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
}





public static function getRootPackage()
{
$installed = self::getInstalled();

return $installed[0]['root'];
}








public static function getRawData()
{
@trigger_error('getRawData only returns the first dataset loaded, which may not be what you expect. Use getAllRawData() instead which returns all datasets for all autoloaders present in the process.', E_USER_DEPRECATED);

return self::$installed;
}







public static function getAllRawData()
{
return self::getInstalled();
}



















public static function reload($data)
{
self::$installed = $data;
self::$installedByVendor = array();
}





private static function getInstalled()
{
if (null === self::$canGetVendors) {
self::$canGetVendors = method_exists('Composer\Autoload\ClassLoader', 'getRegisteredLoaders');
}

$installed = array();

if (self::$canGetVendors) {
foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
if (isset(self::$installedByVendor[$vendorDir])) {
$installed[] = self::$installedByVendor[$vendorDir];
} elseif (is_file($vendorDir.'/composer/installed.php')) {
$installed[] = self::$installedByVendor[$vendorDir] = require $vendorDir.'/composer/installed.php';
}
}
}

$installed[] = self::$installed;

return $installed;
}
}
