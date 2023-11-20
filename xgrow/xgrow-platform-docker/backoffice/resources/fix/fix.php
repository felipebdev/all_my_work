<?php
/**
 * Copyright (C) TheOne / ONI - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Tiago Souza <tiagodjf@gmail.com>
 * If you purchased this software, see the license.txt file contained in this source code for more information and possible exceptions.
 */
declare(strict_types=1);

require_once 'vendor/autoload.php';

#Fix elastic search bug

$classPath = './vendor/elasticsearch/elasticsearch/src/Elasticsearch/Endpoints/AbstractEndpoint.php';

$content = file_get_contents($classPath);

$fix = file_put_contents($classPath, str_replace('$this->id = urlencode($docID);', '$this->id = urlencode(is_array($docID) ? array_values($docID)[0] : $docID);', $content));

