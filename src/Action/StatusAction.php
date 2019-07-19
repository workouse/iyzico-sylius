<?php

declare(strict_types=1);

namespace Eres\SyliusIyzicoPlugin\Action;

use Eres\SyliusIyzicoPlugin\Bridge\IyzicoBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetStatusInterface;

final class StatusAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        $details = ArrayObject::ensureArrayObject($request->getModel());

        if (isset($details['iyzico_status'])) {
            if (IyzicoBridgeInterface::COMPLETED_STATUS === $details['iyzico_status']) {
                $request->markCaptured();
                return;
            }

            if (IyzicoBridgeInterface::FAILED_STATUS === $details['iyzico_status']) {
                $request->markFailed();
                return;
            }
        } else {
            $request->markNew();
            return;
        }


    }

    /**
     * @param mixed $request
     *
     * @return boolean
     */
    public function supports($request): bool
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
