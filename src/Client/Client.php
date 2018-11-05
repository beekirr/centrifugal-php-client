<?php

namespace Centrifuge\Client;

use Centrifuge\HashGenerator\HashGenerator;
use Centrifuge\HashGenerator\HashGeneratorInterface;
use Centrifuge\Transport\Transport;
use Centrifuge\Transport\TransportInterface;

class Client implements ClientInterface
{
    public const VERSION = '1.0.0';

    protected $host;
    protected $secret;
    private $transport;
    private $hashGenerator;

    /**
     * Client constructor.
     *
     * @param string $host
     * @param string $secret
     * @param TransportInterface $transport
     * @param HashGeneratorInterface $hashGenerator
     */
    public function __construct(
        $host,
        $secret,
        TransportInterface $transport = null,
        HashGeneratorInterface $hashGenerator = null
    ) {
        $this->host = $host;
        $this->secret = $secret;
        $this->transport = $transport ?? new Transport();
        $this->hashGenerator = $hashGenerator ?? new HashGenerator($secret);
    }

    /**
     * send message into channel of namespace. data is an actual information you want to send into channel
     *
     * @param string $channel
     * @param array $data
     * @return mixed
     */
    public function publish(string $channel, array $data = [])
    {
        return $this->send(
            'publish', [
                'channel' => $channel,
                'data' => $data,
            ]
        );
    }

    /**
     * send message into multiple channels. data is an actual information you want to send into channel
     *
     * @param array $channels
     * @param array $data
     * @return mixed.
     */
    public function broadcast(array $channels, array $data)
    {
        return $this->send(
            'broadcast', [
                'channels' => $channels,
                'data' => $data,
            ]
        );
    }

    /**
     * unsubscribe user with certain ID from channel.
     *
     * @param string $channel
     * @param string $userId
     * @return mixed
     */
    public function unsubscribe(string $channel, string $userId)
    {
        return $this->send(
            'unsubscribe', [
                'channel' => $channel,
                'user' => $userId,
            ]
        );
    }

    /**
     * disconnect user by user ID.
     *
     * @param string $userId
     * @return mixed
     */
    public function disconnect(string $userId)
    {
        return $this->send(
            'disconnect', [
                'user' => $userId,
            ]
        );
    }

    /**
     * get channel presence information (all clients currently subscribed on this channel).
     *
     * @param string $channel
     * @return mixed
     */
    public function presence(string $channel)
    {
        return $this->send(
            'presence', [
                'channel' => $channel,
            ]
        );
    }

    /**
     * get channel history information (list of last messages sent into channel).
     *
     * @param string $channel
     * @return mixed
     */
    public function history(string $channel)
    {
        return $this->send(
            'history', [
                'channel' => $channel,
            ]
        );
    }

    /**
     * get channels information (list of currently active channels).
     *
     * @return mixed
     */
    public function channels()
    {
        return $this->send('channels', []);
    }

    /**
     * get stats information about running server nodes.
     *
     * @return mixed
     */
    public function stats()
    {
        return $this->send('stats', []);
    }

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function send(string $method, array $params = [])
    {
        $data = json_encode(
            [
                'method' => $method,
                'params' => $params,
            ]
        );

        if (!$data) {
            throw new CentrifugeClientException('JSON message encoding failure');
        }

        return $this->transport->communicate(
            $this->host,
            [
                'data' => $data,
                'sign' => $this->generateApiSign($data),
            ]
        );
    }

    /**
     * @param string $data
     * @return string $hash
     */
    public function generateApiSign(string $data): string
    {
        return $this->hashGenerator->generate([$data]);
    }

    /**
     * Generate client connection token
     *
     * @param string $user
     * @param string $timestamp
     * @param string $info
     * @return string
     */
    public function generateClientToken($user, $timestamp, $info = ''): string
    {
        return $this->hashGenerator->generate(
            [
                $user,
                $timestamp,
                $info,
            ]
        );
    }

    /**
     * @param string $client
     * @param string $channel
     * @param string $info
     * @return string
     */
    public function generateChannelSign($client, $channel, $info = ''): string
    {
        return $this->hashGenerator->generate(
            [
                $client,
                $channel,
                $info,
            ]
        );
    }
}
