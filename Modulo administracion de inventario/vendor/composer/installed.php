<?php return array(
    'root' => array(
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'reference' => NULL,
        'name' => '__root__',
        'dev' => true,
    ),
    'versions' => array(
        '__root__' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'reference' => NULL,
            'dev_requirement' => false,
        ),
        'php-amqplib/php-amqplib' => array(
            'pretty_version' => 'v2.11.0',
            'version' => '2.11.0.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../php-amqplib/php-amqplib',
            'aliases' => array(),
            'reference' => '9ee212baced63442ca1ab029acde38e1144a00b8',
            'dev_requirement' => false,
        ),
        'phpseclib/phpseclib' => array(
            'pretty_version' => '2.0.23',
            'version' => '2.0.23.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../phpseclib/phpseclib',
            'aliases' => array(),
            'reference' => 'c78eb5058d5bb1a183133c36d4ba5b6675dfa099',
            'dev_requirement' => false,
        ),
        'videlalvaro/php-amqplib' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => 'v2.11.0',
            ),
        ),
    ),
);
