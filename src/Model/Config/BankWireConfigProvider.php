<?php declare(strict_types=1);

namespace BelVG\SepaQr\Model\Config;

use Configuration;

class BankWireConfigProvider implements ConfigProviderInterface
{
    private const BANK_WIRE_OWNER = 'BANK_WIRE_OWNER';

    private const BANK_WIRE_COMPANY = 'BANK_WIRE_COMPANY';

    private const BANK_WIRE_CITY = 'BANK_WIRE_CITY';

    private const BANK_WIRE_STREET = 'BANK_WIRE_STREET';

    private const BANK_WIRE_DETAILS = 'BANK_WIRE_DETAILS';

    private const BANK_WIRE_BIC = 'BANK_WIRE_BIC';

    private const BANK_WIRE_IBAN = 'BANK_WIRE_IBAN';

    /**
     * @var array
     */
    private $values = [];

    /**
     * @inheritDoc
     */
    public function get(string $key, int $langId = null)
    {
        if (!isset($this->values[$key])) {
            $newKey = $this->prepareKey($key);
            $value = $newKey === $key ? Configuration::get($newKey, $langId) : $this->get($newKey);
            if (false !== $value) {
                $value = $this->prepareValue($key, $value);
            }
            $this->values[$key] = $value;
        }

        return $this->values[$key];
    }

    /**
     * @param string $key
     * @return string
     */
    private function prepareKey(string $key): string
    {
        switch ($key) {
            case self::BANK_WIRE_COMPANY:
            case self::BANK_WIRE_CITY:
            case self::BANK_WIRE_STREET:
                $key = self::BANK_WIRE_OWNER;
                break;
            case self::BANK_WIRE_BIC:
            case self::BANK_WIRE_IBAN:
                $key = self::BANK_WIRE_DETAILS;
                break;
        }

        return $key;
    }

    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    private function prepareValue(string $key, string $value): string
    {
        switch ($key) {
            // Get first element of the Account owner
            case self::BANK_WIRE_COMPANY:
                $value = explode(', ', $value);
                $value = array_pop($value);
                break;
            // Get city name from third element of the Account owner
            case self::BANK_WIRE_CITY:
                $value = explode(', ', $value);
                $value = $value[2] ?? [];
                $value = explode(' ', $value);
                $value = array_pop($value);
                break;
            // Get second element of the Account owner
            case self::BANK_WIRE_STREET:
                $value = explode(', ', $value);
                $value = $value[1] ?? '';
                break;
            // Get last element of the Account details
            case self::BANK_WIRE_BIC:
                $value = explode(' ', $value);
                $value = array_pop($value);
                $value = trim($value, '()');
                break;
            // Remove last element of the Account details
            case self::BANK_WIRE_IBAN:
                $value = explode(' ', $value);
                array_pop($value);
                $value = implode('', $value);
                break;
        }

        return $value;
    }
}