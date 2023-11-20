<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use App\Integration;

if (PHP_OS === 'WINNT') {
    define("BASE_DIR", "C:\\teste-folder\\");
    define("SEPARATOR", "\\");
    define("COPY", "xcopy");
    define("TEMPLATES_BASE_PATH", "C:\\teste-folder\\templates-base");
    define("REMOVE", "del ");
    define("DEL_DIR", "rmdir /S /Q ");
} else {
    define("BASE_DIR", "/var/www/");
    define("SEPARATOR", "/");
    define("COPY", "cp -R ");
    define("TEMPLATES_BASE_PATH", "/var/templates-base");
    define("REMOVE", "rm ");
    define("DEL_DIR", "rm -rf ");
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
    function icon_link_to_route($icon, $route, $title = null, $parameters = array(), $attributes = array(), $icon_position = 'L')
    {
        $url = route($route, $parameters);

        $title = (is_null($title)) ? $url : e($title);

        $attributes = app('html')->attributes($attributes);

        $icon = '<i class="fa fa-' . e($icon) . '"></i>';

        if ($icon_position == 'L') {
            $iconL = $icon . " ";
            $iconR = "";
        } else {
            $iconL = "";
            $iconR = " " . $icon;
        }

        $response = '<a href="' . $url . '"' . $attributes . '>' . $iconL . $title . $iconR . '</a>';

        return $response;
    }
}

if (!function_exists('')) {

    function createFolderSite($platformUrl)
    {
        if (!mkdir(BASE_DIR . $platformUrl) && !is_dir(BASE_DIR . $platformUrl)) {
            throw new \RuntimeException(sprintf('Erro ao criar o diretório "%s"', BASE_DIR . $platformUrl));
        }

        if (!exec(COPY . " " . BASE_DIR . "base" . SEPARATOR . "*.* " . BASE_DIR . $platformUrl)) {
            throw new \RuntimeException(sprintf('Erro na criação do diretório da plataforma: "%s"', BASE_DIR . $platformUrl));
        }

        return true;
    }
}

if (!function_exists('')) {

    function createConfFile($name, $content, $pathPlatform, $pathSection = false)
    {
        
        $url = $pathPlatform . SEPARATOR;
        $url .= (isset($pathSection)) ? $pathSection . SEPARATOR : "";

        $file = fopen(BASE_DIR . $url . $name, 'w+');
        if ($file === false) {
            die('Não foi possível criar o arquivo.');
        }
        fwrite($file, $content);
        fclose($file);
    }
}

if (!function_exists('')) {

    function createFileConfig($name, $content, $pathPlatform, $pathSection = false, $template_schema = 1)
    {
        $url = $pathPlatform . SEPARATOR;

        $url .= (isset($pathSection)) ? $pathSection . SEPARATOR : "";

        //2 - template vue
        $folder = ($template_schema == 2) ? 'static' : 'config';

        $file = fopen(BASE_DIR . $url . SEPARATOR . $folder . SEPARATOR . $name, 'w+');
        if ($file === false) {
            die('Não foi possível criar o arquivo.');
        }

        fwrite($file, $content);
        fclose($file);
    }
}

if (!function_exists('')) {

    function createForumThemeConfig($name, $pathPlatform, $pathSection = false, $style = 'dark')
    {
        $url = $pathPlatform . SEPARATOR;
        $url .= (isset($pathSection)) ? $pathSection . SEPARATOR : "";

        $destOrigin = BASE_DIR . $url . 'forum' . SEPARATOR . $style . '-style.css';
        $destFile = BASE_DIR . $url . 'forum' . SEPARATOR . $name;
        $style = file_get_contents($destOrigin);

        $file = fopen($destFile, 'w+');
        if ($file === false) {
            die('Não foi possível criar o arquivo.');
        }
        fwrite($file, $style);
        fclose($file);
    }
}


function copyLayoutAssets($template, $slug)
{

    $template_folder = TEMPLATES_BASE_PATH . SEPARATOR . $template . SEPARATOR;
    $asset = BASE_DIR . $slug . SEPARATOR . "assets" . SEPARATOR;

    exec(REMOVE . $asset . "js" . SEPARATOR . "layout.js");
    exec(REMOVE . $asset . "css" . SEPARATOR . "layout.css");

    exec(COPY . " " . $template_folder . "layout.js" . " " . $asset . "js");
    exec(COPY . " " . $template_folder . "layout.css" . " " . $asset . "css");
}


function copyWelcomePage($template, $slug)
{

    $template_folder = TEMPLATES_BASE_PATH . SEPARATOR . $template . SEPARATOR;
    $raiz = BASE_DIR . $slug . SEPARATOR;


    exec(REMOVE . $raiz . "welcome.html");


    exec(COPY . " " . $template_folder . "welcome.html" . " " . $raiz);

}


if (!function_exists('')) {

    function createFolderSection($platformUrl, $sectionUrl, $templateUrl)
    {

        if (!mkdir(BASE_DIR . $platformUrl . SEPARATOR . $sectionUrl) && !is_dir(BASE_DIR . $platformUrl . SEPARATOR . $sectionUrl)) {
            throw new \RuntimeException(sprintf('Erro ao criar o diretório "%s"', BASE_DIR . $platformUrl . SEPARATOR . $sectionUrl));
        }

        exec(COPY . " " . TEMPLATES_BASE_PATH . SEPARATOR . $templateUrl . SEPARATOR . "*.* " . BASE_DIR . $platformUrl . SEPARATOR . $sectionUrl);

        if (!is_dir(BASE_DIR . $platformUrl . SEPARATOR . $sectionUrl)) {
            throw new \RuntimeException(sprintf('Erro na criação do diretório da seção: "%s"', BASE_DIR . $platformUrl . SEPARATOR . $sectionUrl));
        }

        return true;
    }
}

if (!function_exists('')) {

    function deleteFolderSection($platformUrl, $sectionUrl)
    {
        exec(DEL_DIR . BASE_DIR . $platformUrl . SEPARATOR . $sectionUrl . ' ', $output, $returnVar);
        return $returnVar;
    }
}

if (!function_exists('verifyIntegration')) {

    function verifyIntegration($integrationId, $platformId)
    {
        $integration = Integration::where('id_integration', '=', $integrationId)->where('platform_id', '=', $platformId)->first();
        if ($integration === null) {
            return false;
        }
        return ($integration->flag_enable == 0) ? false : true;
    }
}

//cria um vetor com números de um até max
if (!function_exists('setVetorOrder')) {

    function setVetorOrder($max)
    {
        $numbers = range(1, $max);
        $orders = array_combine($numbers, $numbers);
        return $orders;
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

if (!function_exists('dateDb')) {
    function dateDb($date)
    {
        $year = substr($date, 6);
        $month = substr($date, 3, -5);
        $day = substr($date, 0, -8);
        return $year . "-" . $month . "-" . $day;
    }
}

if (!function_exists('dayOfWeek')) {
    function dayOfWeek($date, $type = 'pt-BR')
    {
        if ($type === 'pt-BR') {
            switch (date('l', strtotime($date))) {
                case 'Sunday':
                    return 'Domingo';
                    break;
                case 'Monday':
                    return 'Segunda';
                    break;
                case 'Tuesday':
                    return 'Terça';
                    break;
                case 'Wednesday':
                    return 'Quarta';
                    break;
                case 'Thursday':
                    return 'Quinta';
                    break;
                case 'Friday':
                    return 'Sexta';
                    break;
                case 'Saturday':
                    return 'Sábado';
                    break;
            }
        }
    }
}

if (!function_exists('ageCalc')) {
    function ageCalc($birthday)
    {
        $date = new \DateTime($birthday);

        $interval = $date->diff(new \DateTime(date('Y-m-d')));

        return (int)$interval->format('%Y');
    }
}

if (!function_exists('getTotalLabel')) {
    /**
     * @param array $model
     * @param string $singular retorno no singular
     * @param string $plural retorno no plural (opcional), caso não informado adiciona 's' no final
     * @return string
     */
    function getTotalLabel($model, $singular, $plural = 0)
    {
        $total = $model->count();
        $total_label = "$total ";
        $total_label .= ($total == 1) ? $singular : (($plural === 0) ? $singular . 's' : $plural);
        return $total_label;
    }
}


if (!function_exists('resumeString')) {
    /**
     * @param array $model
     * @param string $singular retorno no singular
     * @param string $plural retorno no plural (opcional), caso não informado adiciona 's' no final
     * @return string
     */
    function resumeString($string, $chars = 10)
    {
        return mb_strimwidth($string, 0, $chars + 3, "...");
    }
}

if (!function_exists('addUrlOfficialApacheConf')) {

    function addUrlOfficialApacheConf($url, $urlOfficial)
    {
        $url = str_replace(' ', '', str_replace('http://', '', str_replace('https://', '', $url)));

        $pathFile = "/etc/apache2/sites-available/$url.conf";

        if (PHP_OS === 'WINNT') {
            $pathFile = "C:\\teste-folder\\$url.conf";
        }

        $novaUrl = $urlOfficial;
        $linhaCustomLog = $linhaRewriteEngine = $linhaUltimoRewriteCond = $linhaUrlExistente = 0;

        $arquivo = fopen($pathFile, 'r');

        if ($arquivo == false) {
            throw new \RuntimeException(sprintf('Erro na abertura do arquivo .conf da plataforma: "%s"', $url));
        }

        $i = 0;
        while (!feof($arquivo)) {
            $linha = fgets($arquivo);
            $i++;

            preg_match('/RewriteEngine\son/', $linha, $outputArray);

            if (count($outputArray) > 0) {
                $linhaRewriteEngine = $i;
            }

            preg_match('/RewriteCond/', $linha, $outputArray);

            if (count($outputArray) > 0) {
                $linhaUltimoRewriteCond = $i;
            }

            preg_match('/' . $novaUrl . '/', $linha, $outputArray);

            if (count($outputArray) > 0) {
                $linhaUrlExistente = $i;
            }

            preg_match('/^CustomLog/', $linha, $outputArray);

            if (count($outputArray) > 0) {
                $linhaCustomLog = $i;
            }
        }

        fclose($arquivo);

        if ($linhaRewriteEngine > 0 && $linhaUltimoRewriteCond > 0 && $linhaUrlExistente <= 0) {
            $arquivo = fopen($pathFile, 'r+');
            $string = "";
            $i = 0;

            while (!feof($arquivo)) {
                $linha = fgets($arquivo);
                $i++;

                if ($i === $linhaUltimoRewriteCond) {
                    $string .= $linha . "[OR] \n";
                    $string .= "RewriteCond %{SERVER_NAME} =$novaUrl \n";
                } else {
                    $string .= $linha;
                }
            }

            rewind($arquivo);

            ftruncate($arquivo, 0);

            if (!fwrite($arquivo, $string)) {
                throw new \RuntimeException(sprintf('Erro na atualização do arquivo .conf da plataforma: "%s"', $url));
            }

            fclose($arquivo);
        }

        if ($linhaRewriteEngine === 0 && $linhaUltimoRewriteCond === 0 && $linhaCustomLog > 0) {

            $arquivo = fopen($pathFile, 'r+');
            $string = "";
            $i = 0;

            while (!feof($arquivo)) {
                $linha = fgets($arquivo);
                $i++;

                if ($i === $linhaCustomLog) {
                    $string .= $linha . " \n";
                    $string .= "RewriteEngine on \n";
                    $string .= "RewriteCond %{SERVER_NAME} =$novaUrl \n";
                    $string .= "RewriteRule ^ https://%{SERVER_NAME}%{REQUEST_URI} [END,NE,R=permanent] \n";
                } else {
                    $string .= $linha;
                }
            }

            rewind($arquivo);

            ftruncate($arquivo, 0);

            if (!fwrite($arquivo, $string)) {
                throw new \RuntimeException(sprintf('Erro na atualização do arquivo .conf da plataforma: "%s"', $url));
            }

            fclose($arquivo);
        }

        exec("cd /var/www && ./domain.sh $url");

        return true;
    }
}

if (!function_exists('setApacheConfig')) {

    function setApacheConfig($platformUrl, $url, $urlOld)
    {
        $url = str_replace(' ', '', str_replace('http://', '', str_replace('https://', '', $url)));

        $contentConf = "<VirtualHost *:80> \n";
        $contentConf .= "ServerAdmin webmaster@localhost \n";
        $contentConf .= "ServerName $url \n";
        $contentConf .= "DocumentRoot /var/www/$platformUrl \n";
        $contentConf .= "<Directory /var/www/$platformUrl> \n";
        $contentConf .= "Options FollowSymLinks \n";
        $contentConf .= "AllowOverride All \n";
        $contentConf .= "Require all granted \n";
        $contentConf .= "</Directory> \n";
        $contentConf .= "ErrorLog \${APACHE_LOG_DIR}/$url-error.log \n";
        $contentConf .= "CustomLog \${APACHE_LOG_DIR}/$url-access.log combined \n";
        $contentConf .= "</VirtualHost>";

        $pathFile = "/etc/apache2/sites-available/$url.conf";
        $pathFileOld = "/etc/apache2/sites-available/$urlOld.conf";

        if (PHP_OS === 'WINNT') {
            $pathFile = "C:\\teste-folder\\$url.conf";
            $pathFileOld = "C:\\teste-folder\\$urlOld.conf";
        }

        exec(REMOVE . ' ' . $pathFileOld);

        createConfApacheFile($pathFile, $contentConf, $platformUrl);

        exec("cd /var/www && ./domain.sh $url");

        return true;
    }
}

if (!function_exists('createConfApacheFile')) {
    function createConfApacheFile($pathFile, $content, $platformUrl)
    {
        $file = fopen($pathFile, 'w+');
        if ($file === false) {
            throw new \RuntimeException(sprintf('Erro na criação do arquivo .conf da plataforma: "%s"', $platformUrl));
        }
        fwrite($file, $content);
        fclose($file);
    }
}

if (!function_exists('platformOff')) {
    function platformOff($url, $urlOfficial)
    {
        $url = str_replace(' ', '', str_replace('http://', '', str_replace('https://', '', $url)));
        $pathFile = "/etc/apache2/sites-available/$url.conf";
        if (PHP_OS === 'WINNT') {
            $pathFile = "C:\\teste-folder\\$url.conf";
        }
        exec(REMOVE . ' ' . $pathFile);

        $url = str_replace(' ', '', str_replace('http://', '', str_replace('https://', '', $urlOfficial)));
        $pathFile = "/etc/apache2/sites-available/$url.conf";
        if (PHP_OS === 'WINNT') {
            $pathFile = "C:\\teste-folder\\$url.conf";
        }
        exec(REMOVE . ' ' . $pathFile);
    }
}
if (!function_exists('generateRandomString')) {
    function generateRandomString($length = 10)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';//abcdefghijklmnopqrstuvwxyz
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if (!function_exists('dateBr')) {
    function dateBr($data, $time = 0)
    {
        if (empty($data)) return;

        if ($time == 0)
            $response = date("d/m/Y", strtotime($data));
        else
            $response = date("d/m/Y H:i", strtotime($data));

        return $response;
    }
}

if (!function_exists('formatCoin')) {
    function formatCoin($value, $currency = 'BRL', $addSymbol = true)
    {
        if (!$value) return;
        $value = floatval($value);

        switch ($currency) {
            case 'USD':
                $coin = '$';
                $decimalPoint = '.';
                $thounsandPoint = ',';
                break;
            case 'EUR':
                $coin = 'Є';
                $decimalPoint = ',';
                $thounsandPoint = '.';
                break;
            default:
                $coin = 'R$';
                $decimalPoint = ',';
                $thounsandPoint = '.';
                break;
        }

        $result = number_format($value, 2, $decimalPoint, $thounsandPoint);
        return ($addSymbol) ? $coin . $result : $result;
    }
}

if (!function_exists('isJSON')) {
    function isJSON($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) ? true : false;
    }
}

if (!function_exists('arrayAddPrefixKey')) {
    function arrayAddPrefixKey($prefix, $array)
    {
        return array_combine(
            array_map(function ($key) use ($prefix) {
                return $prefix . $key;
            }, array_keys($array)),
            $array
        );
    }
}

if (!function_exists('getPaymentType')) {
    function getPaymentType($paymentType)
    {
        $type = '';
        switch ($paymentType) {
            case 'credit_card':
                $type = 'Cartão de crédito';
                break;
            case 'boleto':
                $type = 'Boleto';
                break;
            case 'pix':
                $type = 'Pix';
                break;
        }

        return $type;
    }
}

if (!function_exists('validateDate')) {
    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        if (empty($date)) return false;
        return (DateTime::createFromFormat($format, $date) !== false);
    }
}

if (!function_exists('parseBrDate')) {
    function parseBrDate(string $date)
    {
        if (empty($date)) return '';
        $date = explode('/', $date);
        return "$date[2]-$date[1]-$date[0]";
    }
}

if (!function_exists('parseFloat')) {
    function parseFloat(string $data)
    {
        $data = str_replace(',', '.', $data);
        $data = preg_replace('/\.(?=.*\.)/', '', $data);
        return floatval($data);
    }
}

if (!function_exists('getSubscriptionStatusByPayment')) {
    function getSubscriptionStatusByPayment($paymentStatus)
    {
        $status = '';
        switch ($paymentStatus) {
            case 'paid':
                $status = 'Ativo';
                break;
            case 'canceled':
                $status = 'Cancelado';
                break;
            case 'failed':
                $status = 'Pagamento atrasado';
                break;
            case 'pending':
                $status = 'Pagamento pendente';
                break;
            case 'expired':
                $status = 'Pagamento expirado';
                break;
        }

        return $status;
    }
}

if (!function_exists('in_array_field')) {
    function in_array_field($needle, $needle_field, $haystack, $strict = false)
    {
        if ($strict) {
            foreach ($haystack as $item)
                if (isset($item->$needle_field) && $item->$needle_field === $needle)
                    return true;
        } else {
            foreach ($haystack as $item)
                if (isset($item->$needle_field) && $item->$needle_field == $needle)
                    return true;
        }
        return false;
    }
}

if (!function_exists('keygen')) {
    function keygen($size = 8, $upper = true, $lower = true, $numbers = true)
    {
        $ma = "ABCDEFGHIJKLMNOPQRSTUVYXWZ";
        $mi = "abcdefghijklmnopqrstuvyxwz";
        $nu = "0123456789";
        $pass = '';

        if ($upper) {
            $pass .= str_shuffle($ma);
        }

        if ($lower) {
            $pass .= str_shuffle($mi);
        }

        if ($numbers) {
            $pass .= str_shuffle($nu);
        }

        // Return the password
        return substr(str_shuffle($pass), 0, $size);
    }
}

if (!function_exists('normalizeZipCode')) {
    function normalizeZipCode(string $zipcode, string $country = 'br')
    {
        if (empty($zipcode) || !is_string($zipcode)) return $zipcode;
        if (!is_string($country)) $country = 'br';

        $country = strtolower($country);
        $zipcode = trim(preg_replace('/\s/', '', $zipcode)); //remove all spaces
        if ($country === 'br' ||
            $country === 'bra' ||
            $country === 'brazil' ||
            $country === 'brasil'
        ) {
            $zipcode = preg_replace('/[^0-9]/', '', $zipcode); //only numbers
        }

        return $zipcode;
    }
}

if (!function_exists('removeSchemeFromUrl')) {
    function removeSchemeFromUrl(string $url)
    {
        if (empty($url) || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return $url;
        }

        return preg_replace("#^[^:/.]*[:/]+#i", "", $url);
    }
}

if (!function_exists('removeAccentsAndEspecialChars')) {
    function removeAccentsAndEspecialChars(string $string)
    {
        $withAccent = ['à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú'];
        $tradeWord = ['a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', '0', 'U', 'U', 'U'];
        $st = str_replace($withAccent, $tradeWord, $string);

        return preg_replace("/[^A-Za-z0-9]/i", '', $st);
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

if (!function_exists('generateToken')) {
    function generateToken(string $data, ?string $secret = null): string {
        if (empty($secret)) $secret = env('APP_KEY', 'bKMnhXoPRH');
        return hash_hmac('md5', $data, $secret);
    }
}
