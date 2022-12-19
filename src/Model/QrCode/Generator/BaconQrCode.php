<?php declare(strict_types=1);

namespace BelVG\SepaQr\Model\QrCode\Generator;

use Address;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BelVG\SepaQr\Model\Config\BankWireConfigProvider;
use BelVG\SepaQr\Model\QrCode\GeneratorInterface;
use Context;
use Customer;
use Order;

class BaconQrCode implements GeneratorInterface
{
    /**
     * @var BankWireConfigProvider
     */
    private $configProvider;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->configProvider = new BankWireConfigProvider();
    }

    /**
     * @inheritDoc
     */
    public function generate(Order $order, string $filename): void
    {
        /** @var Customer $customer */
        $customer = $order->getCustomer();
        /** @var Address $customer */
        $billingAddress = new Address($order->id_address_invoice);

        $qrCodeData[] = self::UPNQR;                                                    // Lead style
        $qrCodeData[] = '';                                                             // Payer's IBAN
        $qrCodeData[] = '';                                                             // Deposit
        $qrCodeData[] = '';                                                             // Lift
        $qrCodeData[] = '';                                                             // Payer reference
        $qrCodeData[] = $customer->firstname . ' ' . $customer->lastname;               // Name of payer
        $qrCodeData[] = $billingAddress->address1 . ' ' . $billingAddress->address2;    // Street and no. of the payer
        $qrCodeData[] = $billingAddress->city;                                          // Place of payer
        $qrCodeData[] = $this->formatPrice((float)$order->total_paid);                  // Amount
        $qrCodeData[] = '';                                                             // Payment date
        $qrCodeData[] = '';                                                             // Required
        $qrCodeData[] = self::PURPOSE_CODE;                                             // Purpose Code
        $qrCodeData[] = $this->getPurposeOfPayment();                                   // Purpose of payment
        $qrCodeData[] = '';                                                             // Payment deadline
        $qrCodeData[] = $this->configProvider->get('BANK_WIRE_IBAN');               // Recipient's IBAN
        $qrCodeData[] = self::RECIPIENT_REFERENCE . $order->id;                         // Recipient reference
        $qrCodeData[] = $this->configProvider->get('BANK_WIRE_COMPANY');            // Recipient Name
        $qrCodeData[] = $this->configProvider->get('BANK_WIRE_STREET');             // Street and no. recipient
        $qrCodeData[] = $this->configProvider->get('BANK_WIRE_CITY');               // Recipient location

        $charCount = 0;
        foreach ($qrCodeData as $value) {
            $charCount += strlen($value);
        }
        $qrCodeData[] = $charCount;

        $qrCodeString = implode("\n", $qrCodeData);
        $qrCodeString .= "\n";
        $qrCodeString .= str_pad(mb_strlen($qrCodeString) . '', 3, '0', STR_PAD_LEFT) . "\n";
        $qrCodeString = str_pad($qrCodeString, 410, ' ', STR_PAD_RIGHT);

        $renderer = new ImageRenderer(
            new RendererStyle(180),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $writer->writeFile($qrCodeString, $filename, self::ENCODING, ErrorCorrectionLevel::M());
    }

    /**
     * @return string
     */
    private function getPurposeOfPayment(): string
    {
        $store = $this->configProvider->get('PS_POHISTVO_LANGUAGE_TO_DOMAIN', (int) Context::getContext()->language->id);
        if (is_string($store)) {
            $store = explode('.', $store);
            $store = $store[0];
        } else {
            $store = '';
        }

        return $store;
    }

    /**
     * Price should be without decimal point 11 chars length with leading 0
     *
     * @param float $price
     * @return string
     */
    private function formatPrice(float $price): string
    {
        $precision = $this->configProvider->get('PS_PRICE_DISPLAY_PRECISION') ?: 0;
        $price *= pow(10, $precision);
        return str_pad((string)$price, 11, '0', STR_PAD_LEFT);
    }
}