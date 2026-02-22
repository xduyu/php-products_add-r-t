<?php

/**
 * Паттерны для валидации данных
 * Использование: preg_match($pattern, $string)
 */

class ValidationPatterns
{
    // ==================== ОСНОВНЫЕ ПАТТЕРНЫ ====================

    /**
     * Имя/Фамилия: только буквы, пробелы, дефисы
     */
    const NAME = '/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u';

    /**
     * Пароль: хотя бы одна заглавная буква
     */
    const UPPERCASE = '/[A-ZА-ЯЁ]/u';

    /**
     * Пароль: хотя бы одна строчная буква
     */
    const LOWERCASE = '/[a-zа-яё]/u';

    /**
     * Пароль: хотя бы одна цифра
     */
    const DIGIT = '/[0-9]/';

    /**
     * Пароль: хотя бы один специальный символ
     */
    const SPECIAL_CHAR = '/[!@#$%^&*()\-_=+{};:,<.>]/';


    // ==================== ДОПОЛНИТЕЛЬНЫЕ ПАТТЕРНЫ ====================

    /**
     * Email (базовая проверка)
     */
    const EMAIL = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

    /**
     * Строгая проверка email
     */
    const EMAIL_STRICT = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

    /**
     * Телефон (русский формат)
     */
    const PHONE_RU = '/^(\+7|8)[\s\-]?\(?\d{3}\)?[\s\-]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}$/';

    /**
     * Телефон (международный формат)
     */
    const PHONE_INTL = '/^\+[1-9]{1}[0-9]{3,14}$/';

    /**
     * Дата в формате YYYY-MM-DD
     */
    const DATE_ISO = '/^\d{4}-\d{2}-\d{2}$/';

    /**
     * Дата в формате DD.MM.YYYY
     */
    const DATE_RU = '/^\d{2}\.\d{2}\.\d{4}$/';

    /**
     * Время в формате HH:MM
     */
    const TIME = '/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/';

    /**
     * Целое число (положительное и отрицательное)
     */
    const INTEGER = '/^-?\d+$/';

    /**
     * Число с плавающей точкой
     */
    const FLOAT = '/^-?\d+\.?\d*$/';

    /**
     * Только цифры
     */
    const DIGITS_ONLY = '/^\d+$/';

    /**
     * Без специальных символов (только буквы и цифры)
     */
    const ALPHANUMERIC = '/^[a-zA-Zа-яА-ЯёЁ0-9]+$/u';

    /**
     * URL адрес
     */
    const URL = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';

    /**
     * IPv4 адрес
     */
    const IPV4 = '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/';

    /**
     * HEX цвет (#ffffff или #fff)
     */
    const HEX_COLOR = '/^#([a-f0-9]{6}|[a-f0-9]{3})$/i';

    /**
     * Логин: буквы, цифры, подчеркивание, 3-20 символов
     */
    const USERNAME = '/^[a-zA-Z0-9_]{3,20}$/';

    /**
     * Strong password: минимум 8 символов, буквы в обоих регистрах, цифры, спецсимволы
     */
    const STRONG_PASSWORD = '/^(?=.*[a-zа-яё])(?=.*[A-ZА-ЯЁ])(?=.*\d)(?=.*[!@#$%^&*()\-_=+{};:,<.>])[a-zA-Zа-яА-ЯёЁ0-9!@#$%^&*()\-_=+{};:,<.>]{8,}$/u';

    /**
     * ZIP код (российский)
     */
    const ZIP_RU = '/^\d{6}$/';

    /**
     * ИНН (российский)
     */
    const INN = '/^\d{10,12}$/';

    /**
     * Кредитная карта (основные форматы)
     */
    const CREDIT_CARD = '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|6(?:011|5[0-9]{2})[0-9]{12}|(?:2131|1800|35\d{3})\d{11})$/';


    // ==================== МЕТОДЫ ДЛЯ ПРОВЕРКИ ====================

    /**
     * Проверить строку по паттерну
     */
    public static function match($pattern, $string)
    {
        return preg_match($pattern, $string) === 1;
    }

    /**
     * Проверить имя/фамилию
     */
    public static function isValidName($name)
    {
        return self::match(self::NAME, $name);
    }

    /**
     * Проверить email
     */
    public static function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Проверить strong password
     */
    public static function isValidStrongPassword($password)
    {
        return self::match(self::STRONG_PASSWORD, $password);
    }

    /**
     * Проверить телефон
     */
    public static function isValidPhone($phone)
    {
        return self::match(self::PHONE_RU, $phone) || self::match(self::PHONE_INTL, $phone);
    }
}

// ==================== ПРИМЕРЫ ИСПОЛЬЗОВАНИЯ ====================

/*
// Пример использования:
require_once 'patterns.php';

// Проверка имени
if (ValidationPatterns::isValidName("Иван-Петров")) {
    echo "Имя valid\n";
}

// Проверка email
if (ValidationPatterns::isValidEmail("test@example.com")) {
    echo "Email valid\n";
}

// Проверка пароля
if (ValidationPatterns::isValidStrongPassword("Password123!")) {
    echo "Пароль strong\n";
}

// Проверка телефона
if (ValidationPatterns::isValidPhone("+79161234567")) {
    echo "Телефон valid\n";
}

// Прямое использование паттернов
if (preg_match(ValidationPatterns::DATE_ISO, "2023-12-31")) {
    echo "Дата valid\n";
}
*/
