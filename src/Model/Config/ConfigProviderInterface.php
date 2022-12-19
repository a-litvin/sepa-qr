<?php declare(strict_types=1);

namespace BelVG\SepaQr\Model\Config;

interface ConfigProviderInterface
{
    /**
     * @param string $key
     * @param int|null $langId
     * @return string|bool
     */
    public function get(string $key, int $langId = null);
}