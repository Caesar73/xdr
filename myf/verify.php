<?php

/**
 * 验证eamil
 * @param string $value
 * @param int $length
 * @return boolean
 */
function isEmail($value, $match = '/^[\w\d]+[\w\d-.]*@[\w\d-.]+\.[\w\d]{2,10}$/i') {
    $v = trim($value);
    if (empty($v))
        return false;
    return preg_match($match, $v);
}

/**
 * 验证电话号码
 * @param string $value
 * @return boolean
 */
function isTelephone($value, $match = '/^0[0-9]{2,3}[-]?\d{7,8}$/') {
    $v = trim($value);
    if (empty($v))
        return false;
    return preg_match($match, $v);
}

/**
 * 验证手机
 * @param string $value
 * @param string $match
 * @return boolean
 */
function isMobile($value, $match = '/^[(86)|0]?(13\d{9})|(15\d{9})|(18\d{9})$/') {
    $v = trim($value);
    if (empty($v))
        return false;
    return preg_match($match, $v);
}

/**
 * 验证邮政编码
 * @param string $value
 * @param string $match
 * @return boolean
 */
function isPostcode($value, $match = '/\d{6}/') {
    $v = trim($value);
    if (empty($v))
        return false;
    return preg_match($match, $v);
}

/**
 * 验证IP
 * @param string $value
 * @param string $match
 * @return boolean
 */
function isIP($value, $match = '/^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/') {
    $v = trim($value);
    if (empty($v))
        return false;
    return preg_match($match, $v);
}

/**
 * 验证身份证号码
 * @param string $value
 * @param string $match
 * @return boolean
 */
function isIDcard($value, $match = '/^\d{6}((1[89])|(2\d))\d{2}((0\d)|(1[0-2]))((3[01])|([0-2]\d))\d{3}(\d|X)$/i') {
    $v = trim($value);
    if (empty($v))
        return false;
    else if (strlen($v) > 18)
        return false;
    return preg_match($match, $v);
}

/**
 * *
 * 验证URL
 * @param string $value
 * @param string $match
 * @return boolean
 */
function isURL($value, $match = '/^(http:\/\/)?(https:\/\/)?([\w\d-]+\.)+[\w-]+(\/[\d\w-.\/?%&=]*)?$/') {
    $v = strtolower(trim($value));
    if (empty($v))
        return false;
    return preg_match($match, $v);
}
