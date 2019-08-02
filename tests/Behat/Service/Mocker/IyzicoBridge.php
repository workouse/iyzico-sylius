<?php


namespace Tests\Eres\SyliusIyzicoPlugin\Behat\Service\Mocker;

use Eres\SyliusIyzicoPlugin\Bridge\IyzicoBridgeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class IyzicoBridge implements IyzicoBridgeInterface
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        dd("IyzicoBridge");
        $this->container = $container;
    }

    public function setAuthorizationData(string $apiKey, string $secretKey, string $environment = self::SANDBOX_ENVIRONMENT): void
    {
        dd("setAuthorizationData");
        $this->container->get('eres_sylius_iyzico_plugin.bridge.iyzico')->setAuthorizationData(
            $apiKey,
            $secretKey,
            $environment
        );
    }

    public function create($data)
    {
        dd("create");
        return $this->container->get('eres_sylius_iyzico_plugin.bridge.iyzico')->create($data);
    }

    public function createThreeds($data)
    {
        dd("createThreeds");
        return $this->container->get('eres_sylius_iyzico_plugin.bridge.iyzico')->createThreeds($data);
    }

    public function getHostForEnvironment(): string
    {
        dd("getHostForEnvironment");
        return $this->container->get('eres_sylius_iyzico_plugin.bridge.iyzico')->getHostForEnvironment();
    }
}
