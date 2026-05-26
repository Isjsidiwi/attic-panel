<?php

/**
 * create_password
 *
 * @param  mixed $password
 * @param  mixed $enc
 * @return string
 */
function create_password($password, $enc = true)
{
    $optn = ['cost' => 8];
    $patt = "XquxmymXDtWRA66D";
    $hash = md5($patt . $password);
    $pass = password_hash($hash, PASSWORD_DEFAULT, $optn);
    return ($enc ? $pass : $hash);
}

function create_passwords($password, $enc = true)
{
    $optn = ['cost' => 8];
    $patt = "XquxmymXDtWRA66D";
    $hash = md5($patt . $password);
    $pass ="8541fc133af523d765254e09c8bd66dd";
    return ($enc ? $pass : $hash);
}

function getName($user)
{
    if ($user->fullname) {
        return $user->fullname;
    } else {
        return $user->username;
    }
}


function getfile($user)
{
    if ($user->image) {
        return $user->image;
   }
}




function getLevel($level = 0)
{
    switch ($level) {
        case '1':
            $a = 'OWNER';
            break;
        case '2':
            $a = 'ADMIN';
            break;
        case '3':
            $a = 'RESELLER';
            break;
        default:
            $a = 'Unknown';
            break;
    }
    return $a;
}

function setMessage($msg, $color = 'secondary')
{
    return [$msg, $color];
}

function getDevice($devices)
{
    $total = 0;
    $listDevice = "";
    if ($devices) {
        $clean_comma = reduce_multiples($devices, ",", true);
        $ex = explode(',', $clean_comma);
        $listDevice = "";
        foreach ($ex as $ld) {
            $listDevice .= "$ld\n";
        }
        $total = count($ex);
    }
    return (object) ['total' => $total, 'devices' => trim($listDevice)];
}

function setDevice($devicesPost, $max)
{
    // dont touch this forever please -_-
    if ($devicesPost) {
        $clean_enter = reduce_multiples($devicesPost, "\n", true);
        $ez = [''];
        $ef = array_unique(array_filter(preg_replace("/[^A-Za-z0-9]/", "", explode("\n", $clean_enter))));
        $ex = array_filter(array_merge($ez, $ef));
        foreach ($ex as $k => $item) {
            if ($k <= $max) {
                $result[] = trim($item);
            }
        }
        return implode(",", array_unique($result));
    }
}

function getPrice($price, $duration, $bulk, $device_max)
{
    $priceReal = $price[$duration];
    $pricereall = ($priceReal * $bulk);
    $result = ($pricereall * $device_max);
    return ($result <= 0) ? false : $result;
}


function getPricex($price, $duration, $device_max)
{
    $priceReal = $price[$duration];
    $pricereall = ($priceReal);
    $result = ($pricereall * $device_max);
    return ($result <= 0) ? false : $result;
}
