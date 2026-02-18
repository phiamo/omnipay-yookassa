<?php
/**
 * YooKassa driver for Omnipay payment processing library
 *
 * @link      https://github.com/arhitov/omnipay-yookassa
 * @package   omnipay-yookassa
 * @license   MIT
 * @copyright Copyright (c) 2021, Igor Tverdokhleb, igor-tv@mail.ru
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnipay\YooKassa\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Message\RequestInterface;

class IncomingNotificationResponse extends AbstractResponse implements NotificationInterface
{
    /**
     * @return RequestInterface|AbstractRequest
     */
    public function getRequest()
    {
        return parent::getRequest();
    }

    /**
     * CompletePurchaseResponse constructor.
     * @param RequestInterface $request
     * @param array $data
     * @throws \Exception
     */
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);
    }

    public function getTransactionId()
    {
        return $this->data['object']['metadata']['transactionId'] ?? null;
    }

    public function getTransactionReference()
    {
        return $this->data['object']['id'] ?? null;
    }

    public function isSuccessful()
    {
        return $this->getTransactionReference() !== null;
    }

    public function getTransactionStatus(): string
    {
        $status = $this->data['object']['status'] ?? null;
        return match ($status) {
            'succeeded' => NotificationInterface::STATUS_COMPLETED,
            'pending', 'waiting_for_capture' => NotificationInterface::STATUS_PENDING,
            'canceled', 'failed' => NotificationInterface::STATUS_FAILED,
            default => NotificationInterface::STATUS_PENDING,
        };
    }

    public function getMessage(): string
    {
        return $this->data['object']['description'] ?? '';
    }
}
