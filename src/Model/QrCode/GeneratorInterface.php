<?php declare(strict_types=1);

namespace BelVG\SepaQr\Model\QrCode;

use Order;

interface GeneratorInterface
{
    /**
     * Generator constants
     */
    const UPNQR = 'UPNQR';
    const PURPOSE_CODE = 'WEBI';
    const RECIPIENT_REFERENCE = 'SI00';
    const ENCODING = 'ISO-8859-2';

    /**
     * @param Order $order
     * @param string $filename
     * @return void
     */
    public function generate(Order $order, string $filename): void;
}