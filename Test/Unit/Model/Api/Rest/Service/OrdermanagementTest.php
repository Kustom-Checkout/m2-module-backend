<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Backend\Test\Unit\Model\Api\Rest\Service;

use Klarna\Backend\Model\Api\Rest\Service\Ordermanagement;
use Klarna\Base\Api\ServiceInterface;
use Klarna\Base\Helper\VersionInfo;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversMethod(Ordermanagement::class, 'addShippingInfo')]
class OrdermanagementTest extends TestCase
{
    private readonly ServiceInterface $service;
    private readonly Ordermanagement $subject;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->createMock(ServiceInterface::class);
        $versionInfo = $this->createStub(VersionInfo::class);
        $versionInfo->method('getFullM2KlarnaVersion')->willReturn('kustom/module-checkout/12.0.0');
        $versionInfo->method('getVersion')->willReturn('12.0.0');
        $versionInfo->method('getMageInfo')->willReturn('Magento Enterprise/2.4.8 developer mode');

        $this->subject = new Ordermanagement($this->service, $versionInfo);
    }

    /**
     * @param string $orderId
     * @param string $captureId
     * @param string $expectedUrl
     *
     * @return void
     */
    #[DataProvider('shippingInfoUrlProvider')]
    public function testAddShippingInfoUsesExpectedUrl(string $orderId, string $captureId, string $expectedUrl): void
    {
        $this->service->expects($this->once())
            ->method('makeRequest')
            ->with($expectedUrl)
            ->willReturn([]);

        $this->subject->addShippingInfo($orderId, $captureId, []);
    }

    /**
     * @return array[]
     */
    public static function shippingInfoUrlProvider(): array
    {
        return [
            'with capture id shipping info is sent to capture endpoint' => [
                'test-order-id-123',
                'test-capture-id-123',
                '/ordermanagement/v1/orders/test-order-id-123/captures/test-capture-id-123/shipping-info',
            ],
            'without capture id shipping info is sent to order endpoint' => [
                'test-order-id-456',
                '',
                '/ordermanagement/v1/orders/test-order-id-456/shipping-info',
            ],
        ];
    }
}
