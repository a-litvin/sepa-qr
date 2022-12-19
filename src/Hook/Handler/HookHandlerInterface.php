<?php declare(strict_types=1);

namespace BelVG\SepaQr\Hook\Handler;

interface HookHandlerInterface
{
    /**
     * @param array $params
     * @return mixed
     */
    public function execute(array $params);
}