<?php

$options = getopt('', array(
    'log_file:',
));

if (!empty($options['log_file'])) {
    $log_file = log_parser($options['log_file']);
    echo $log_file;
}

function log_parser(string $file) {

	$remote_hosts = [];
    $status_code = [];

    $pars_file = [
        'views'    => 0,
        'urls'     => 0,
        'traffic'  => 0,
        'crawlers' => [
            'Google' => 0,
            'Yandex' => 0,
            'Bing'   => 0,
            'Baidu'  => 0,
        ],
        'status_codes' => [],
    ];

    
    $pattern = '/^([^ ]+) ([^ ]+) ([^ ]+) (\[[^\]]+\]) "(.*) (.*) (.*)" ([0-9\-]+) ([0-9\-]+) "(.*)" "(.*)"$/';

    if ($open_file = fopen($file, 'r')) {
        $i = 1;
        while (!feof($open_file)) {
            if ($line = trim(fgets($open_file))) {
                if (preg_match($pattern, $line, $matches)) {
                    list($line, $remote_host, $pr1, $pr2, $datetime, $method, $request, $protocol, $status, $bytes, $link, $user) = $matches;

                    if (!array_search($remote_host, $remote_hosts)) {
                        $remote_hosts[] = $remote_host;
                    }

                    if (!array_key_exists($status, $status_code)) {
                        $status_code[$status] = 1;
                    } else {
                        $status_code[$status]++;
                    }

                    $pars_file['views'] = $i;
                    $pars_file['urls'] = count($remote_hosts);
                    $pars_file['traffic'] += $bytes;
                    $pars_file['status_codes'] = $status_code;

                    $crawl_pattern = "/bot|google|yandex|bing|baidu/i";
                    preg_match($crawl_pattern, $user, $crawl_result);
                    if (!empty($crawl_result)) {
                        list($crawl_name) = $crawl_result;
                        if (!array_key_exists($crawl_name, $pars_file['crawlers'])) {
                            $pars_file['crawlers'][$crawl_name] = 1;
                        } else {
                            $pars_file['crawlers'][$crawl_name]++;
                        }
                    }
                }
            }
            $i++;
        }
    }

    return json_encode($pars_file, JSON_PRETTY_PRINT);
}