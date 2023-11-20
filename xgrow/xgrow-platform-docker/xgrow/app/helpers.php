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
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
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

if (!function_exists('generateRandomString')) {
    function generateRandomString($length = 10)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'; //abcdefghijklmnopqrstuvwxyz
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
        if (
            $country === 'br' ||
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
    function generateToken(string $data, ?string $secret = null): string
    {
        if (empty($secret)) $secret = env('APP_KEY', 'bKMnhXoPRH');
        return hash_hmac('md5', $data, $secret);
    }
}

if (!function_exists('arraySearchInner')) {
    function arraySearchInner($array, $attr, $val)
    {
        $arraykey = null;
        foreach ($array as $key => $inner) {
            if ($inner->$attr === $val) {
                $arraykey = $key;
            }
        }
        return $arraykey;
    }
}

if (!function_exists('formatDateAndTime')) {
    function formatDateAndTime($value, $format = 'd/m/Y')
    {
        // Utiliza a classe de Carbon para converter ao formato de data ou hora desejado
        return Carbon\Carbon::parse($value)->format($format);
    }
}

if (!function_exists('subtractDate')) {
    function subtractDate($daysToSubtract)
    {
        return date('Y-m-d', strtotime("- $daysToSubtract days", strtotime(date('Y-m-d'))));
    }
}

if (!function_exists('documentValid')) {
    function cnpjValid($cnpj): bool
    {
        // Deixa o CNPJ com apenas números
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        // Garante que o CNPJ é uma string
        $cnpj = (string)$cnpj;

        // O valor original
        $cnpjOriginal = $cnpj;

        // Captura os primeiros 12 números do CNPJ
        $primeirosNumerosCnpj = substr($cnpj, 0, 12);

        /**
         * Multiplicação do CNPJ
         *
         * @param string $cnpj Os digitos do CNPJ
         * @param int $posicoes A posição que vai iniciar a regressão
         * @return int O
         *
         */
        if (!function_exists('multiplicaCnpj')) {
            function multiplicaCnpj($cnpj, $posicao = 5)
            {
                // Variável para o cálculo
                $calculo = 0;

                // Laço para percorrer os item do cnpj
                for ($i = 0; $i < strlen($cnpj); $i++) {
                    // Cálculo mais posição do CNPJ * a posição
                    $calculo = $calculo + ($cnpj[$i] * $posicao);

                    // Decrementa a posição a cada volta do laço
                    $posicao--;

                    // Se a posição for menor que 2, ela se torna 9
                    if ($posicao < 2) {
                        $posicao = 9;
                    }
                }
                // Retorna o cálculo
                return $calculo;
            }
        }

        // Faz o primeiro cálculo
        $primeiroCalculo = multiplicaCnpj($primeirosNumerosCnpj);

        // Se o resto da divisão entre o primeiro cálculo e 11 for menor que 2, o primeiro
        // Dígito é zero (0), caso contrário é 11 - o resto da divisão entre o cálculo e 11
        $primeiroDigito = ($primeiroCalculo % 11) < 2 ? 0 : 11 - ($primeiroCalculo % 11);

        // Concatena o primeiro dígito nos 12 primeiros números do CNPJ
        // Agora temos 13 números aqui
        $primeirosNumerosCnpj .= $primeiroDigito;

        // O segundo cálculo é a mesma coisa do primeiro, porém, começa na posição 6
        $segundoCalculo = multiplicaCnpj($primeirosNumerosCnpj, 6);
        $segundoDigito = ($segundoCalculo % 11) < 2 ? 0 : 11 - ($segundoCalculo % 11);

        // Concatena o segundo dígito ao CNPJ
        $cnpj = $primeirosNumerosCnpj . $segundoDigito;

        // Verifica se o CNPJ gerado é idêntico ao enviado
        return $cnpj === $cnpjOriginal ? true : false;
    }
}

if (!function_exists('validaCPF')) {
    function cpfValid($cpf)
    {
        // Extrai somente os números
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);

        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }
}

/**
 * @param $value
 * @param $mask
 * @return string
 * @example $cnpj = mask('00011144423614', '##.###.###/####-##');
 * @example $cpf = mask('00011123645', '###.###.###-##');
 */
if (!function_exists('mask')) {
    function mask($value, $mask): string
    {
        $element = '';

        $k = 0;

        for ($i = 0; $i <= strlen($mask) - 1; $i++) {

            if ($mask[$i] == '#') {

                if (isset($value[$k])) $element .= $value[$k++];
            } else {

                if (isset($mask[$i])) $element .= $mask[$i];
            }
        }

        return $element;
    }
}
