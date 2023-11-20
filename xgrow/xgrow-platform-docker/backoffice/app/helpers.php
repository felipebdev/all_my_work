<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use App\Services\Objects\PeriodFilter;

if (PHP_OS === 'WINNT') {
    define("BASE_DIR", "C:\\teste-folder\\");
    define("SEPARATOR", "\\");
    define("COPY", "xcopy "); //copia só arquivo
    define("COPY_FILES", "xcopy /E "); //Copia pasta e supastas
    define("REMOVE", "del ");
    define("DELETE_FILES_FROM_FOLDER", "del /q ");
    define("TEMPLATES_BASE_PATH", "C:\\teste-folder\\templates-base");
    define("TEMPLATES_BASE_PATH_VUE", "C:\\teste-folder\\templatesvue");
} else {
    define("BASE_DIR", "/var/www/");
    define("SEPARATOR", "/");
    define("COPY", "cp -R ");
    define("COPY_FILES", "cp -R ");
    define("REMOVE", "rm ");
    define("DELETE_FILES_FROM_FOLDER", "rm ");
    define("TEMPLATES_BASE_PATH", "/var/templates-base");
    define("TEMPLATES_BASE_PATH_VUE", "/var/templatesvue");
}

if (!function_exists('')) {
    function validateEmail($email)
    {
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
        if (preg_match($regex, $email)) {
            return true;
        } else
            return false;
    }
}

if (!function_exists('icon_link_to_route')) {
    /**
     * Create link to named route with glyphicon icon.
     *
     * @param string $icon
     * @param string $route
     * @param string $title
     * @param array $parameters
     * @param array $attributes
     * @return string
     */
    function icon_link_to_route($icon, $route, $title = null, $parameters = array(), $attributes = array())
    {
        $url = route($route, $parameters);

        $title = (is_null($title)) ? $url : e($title);

        $attributes = app('html')->attributes($attributes);

        return '<a href="' . $url . '"' . $attributes . '>' . '<i class="fa fa-' . e($icon) . '"></i>' . " " . $title . '</a>';
    }
}

if (!function_exists('setApacheConfig')) {

    function setApacheConfig($platformUrl, $url)
    {
        $url = str_replace(' ', '', str_replace('http://', '', str_replace('https://', '', $url)));

        $contentConf = "<VirtualHost *:80> \n";
        $contentConf .= "ServerAdmin webmaster@localhost \n";
        $contentConf .= "ServerName $url \n";
//        $contentConf .= "ServerName $platformUrl.fandone.com.br \n";
//        $contentConf .= "ServerAlias www.$platformUrl.fandone.com.br \n";
        $contentConf .= "DocumentRoot /var/www/$platformUrl \n";
        $contentConf .= "<Directory /var/www/$platformUrl> \n";
        $contentConf .= "Options FollowSymLinks \n";
        $contentConf .= "AllowOverride All \n";
        $contentConf .= "Require all granted \n";
        $contentConf .= "</Directory> \n";
        $contentConf .= "ErrorLog \${APACHE_LOG_DIR}/$url-error.log \n";
        $contentConf .= "CustomLog \${APACHE_LOG_DIR}/$url-access.log combined \n";
        $contentConf .= "</VirtualHost>";

        createConfApacheFile("/etc/apache2/sites-available/$url.conf", $contentConf, $platformUrl);

        exec("cd /var/www && ./domain.sh $url");

        return true;
    }
}

if (!function_exists('copyFile')) {

    function copyFile($source, $destiny)
    {
        exec(COPY . $source . " " . $destiny);
    }
}

if (!function_exists('copyFiles')) {

    function copyFiles($source, $destiny)
    {
        exec(COPY_FILES . $source . " " . $destiny);
    }
}

if (!function_exists('createFolder')) {
    function createFolder($path)
    {
        if (!is_dir($path)) {
            mkdir($path);
        }
    }
}

if (!function_exists('fillFilesFromFolder')) {

    function fillFilesFromFolder($source, $destiny)
    {
        exec(COPY . " " . $source . SEPARATOR . "*.* " . $destiny);
    }
}

if (!function_exists('deleteFile')) {
    function deleteFile($file)
    {
        exec(REMOVE . " " . $file);
    }
}

if (!function_exists('deleteMultFiles')) {
    function deleteMultFiles($files)
    {
        $multiple = implode(" ", $files);
        exec(REMOVE . " " . $multiple);
    }
}

if (!function_exists('deleteFolder')) {
    function deleteFolder($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? deleteFolder("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}


if (!function_exists('deleteFilesFromFolder')) {
    function deleteFilesFromFolder($path)
    {
        exec(DELETE_FILES_FROM_FOLDER . $path . SEPARATOR . "*");
    }
}

if (!function_exists('createFile')) {
    function createFile($name, $content, $path)
    {
        $file = fopen($path . SEPARATOR . $name, 'w+');
        if ($file === false) {
            die('Não foi possível criar o arquivo.');
        }
        fwrite($file, $content);
        fclose($file);
    }
}

if (!function_exists('createConfApacheFile')) {
    function createConfApacheFile($pathFile, $content, $platformUrl)
    {
        if (PHP_OS !== 'WINNT') {
            $file = fopen($pathFile, 'w+');
            if ($file === false) {
                throw new \RuntimeException(sprintf('Erro na criação do arquivo .conf da plataforma: "%s"', $platformUrl));
            }
            fwrite($file, $content);
            fclose($file);
        }
    }
}

if (!function_exists('createSslFile')) {
    function createSslFile($pathFile, $content, $platformUrl)
    {
        if (PHP_OS !== 'WINNT') {
            $file = fopen($pathFile, 'w+');
            if ($file === false) {
                throw new \RuntimeException(sprintf('Erro na criação do arquivo .yml da plataforma: "%s"', $platformUrl));
            }
            fwrite($file, $content);
            fclose($file);
        }
    }
}

function recurse_copy($src, $dst)
{
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

if (!function_exists('setSslConfig')) {

    function setSslConfig($client, $platformUrl, $url)
    {
        $domain = str_replace('www.', '', str_replace('https://', '', str_replace('http://', '', $url)));

        $domainWww = 'www.' . $domain;

        $content = "contact_email: {$client->email}
                    defaults:
                      distinguished_name:
                          country: BR
                          locality: {$client->city}
                          organization_name: {$client->fantasy_name}
                      solver: http

                    certificates:
                      - domain: $domain
                        distinguished_name:
                          organization_name: {$client->fantasy_name}
                        subject_alternative_names:
                          - $domainWww";

        createSslFile("/var/www/ssl-yml/$platformUrl-config-ssl.yml", $content, $platformUrl);

        exec("cd /var/www/ssl-yml && php acmephp.phar run $platformUrl-config-ssl.yml");

        return true;
    }
}

if (!function_exists('passwordStrength')) {
    function passwordStrength(string $password, int $lenght = 5)
    {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if (!$lowercase || !$number || !$specialChars || strlen($password) < $lenght) {
            throw new \Exception('Obrigatório no mínimo 5 caracteres incluindo: letras, números e pelo menos um caractere especial.');
        } else {
            return $password;
        }
    }
}

if (!function_exists('cleanCpfOrCnpj')) {
    function cleanCpfOrCnpj($value)
    {
        $data = str_replace([".", ",", "-", "/"], "", trim($value));
        return $data === "" ? null : $data;
    }
}

if (!function_exists('validateDate')) {
    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
}

if (!function_exists('convertStringToPeriodFilter')) {
    function convertStringToPeriodFilter(?string $inputPeriod): ?object
    {

            if (is_null($inputPeriod)) {
                throw new Exception('Date is null');
            }

            $inputPeriod = str_replace(" ", '', $inputPeriod);

            if (!preg_match('~\d{2}/\d{2}/\d{4}-\d{2}/\d{2}/\d{4}$~', $inputPeriod)) {
                throw new Exception('Unexpected format period');
            }

            $fields = $inputPeriod ? explode('-', $inputPeriod) : ['', ''];
            $begin = convertDateSearch($fields[0]);
            $end = convertDateSearch($fields[1]);

            if (!validateDate($begin, 'Y-m-d') || !validateDate($end, 'Y-m-d')) {
                throw new Exception('Date invalid');
            }

            return new PeriodFilter($begin, $end);

    }
}

if (!function_exists('convertDateSearch')) {
     function convertDateSearch(string $date)
    {
        $date = explode('/', $date);
        return "$date[2]-$date[1]-$date[0]";
    }
}

if (!function_exists('dateBr')) {
    function dateBr($date, $time = 0)
    {
        if ($time == 0)
            $response = date("d/m/Y", strtotime($date));
        else
            $response = date("d/m/Y H:i", strtotime($date));

        return $response;
    }
}

if (!function_exists('sanitizeString')) {
    function sanitizeString($str)
    {
        $str = preg_replace('/[áàãâä]/ui', 'a', $str);
        $str = preg_replace('/[éèêë]/ui', 'e', $str);
        $str = preg_replace('/[íìîï]/ui', 'i', $str);
        $str = preg_replace('/[óòõôö]/ui', 'o', $str);
        $str = preg_replace('/[úùûü]/ui', 'u', $str);
        $str = preg_replace('/[ç]/ui', 'c', $str);
        // $str = preg_replace('/[,(),;:|!"#$%&/=?~^><ªº-]/', '_', $str);
        $str = preg_replace('/[^a-z0-9]/i', '_', $str);
        $str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
        return $str;
    }
}

//generates a random password of length minimum 8
//contains at least one lower case letter, one upper case letter,
// one number and one special character,
//not including ambiguous characters like iIl|1 0oO
if (!function_exists('randomPassword')) {
    function randomPassword($len = 8)
    {

        //enforce min length 8
        if ($len < 8)
            $len = 8;

        //define character libraries - remove ambiguous characters like iIl|1 0oO
        $sets = array();
        //$sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        $sets[] = '0123456789';
        //$sets[] = '~!@#$%^&*(){}[],./?';
        $sets[] = '!@#$%.?';

        $password = '';

        //append a character from each set - gets first 4 characters
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
        }

        //use all characters to fill up to $len
        while (strlen($password) < $len) {
            //get a random set
            $randomSet = $sets[array_rand($sets)];

            //add a random char from the random set
            $password .= $randomSet[array_rand(str_split($randomSet))];
        }

        //shuffle the password string before returning!
        return str_shuffle($password);
    }
}



