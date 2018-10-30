<?php

namespace CentrifugalClient\Client;

interface ClientInterface
{
    /**
     * send message into channel of namespace. data is an actual information you want to send into channel
     *
     * @param string $channel
     * @param array $data
     * @return mixed
     */
    public function publish(string $channel, array $data = []);

    /**
     * send message into multiple channels. data is an actual information you want to send into channel
     *
     * @param array $channels
     * @param array $data
     * @return mixed.
     */
    public function broadcast(array $channels, array $data);

    /**
     * unsubscribe user with certain ID from channel.
     *
     * @param string $channel
     * @param string $userId
     * @return mixed
     */
    public function unsubscribe(string $channel, string $userId);

    /**
     * disconnect user by user ID.
     *
     * @param string $userId
     * @return mixed
     */
    public function disconnect(string $userId);

    /**
     * get channel presence information (all clients currently subscribed on this channel).
     *
     * @param string $channel
     * @return mixed
     */
    public function presence(string $channel);

    /**
     * get channel history information (list of last messages sent into channel).
     *
     * @param string $channel
     * @return mixed
     */
    public function history(string $channel);

    /**
     * get channels information (list of currently active channels).
     *
     * @return mixed
     */
    public function channels();

    /**
     * get stats information about running server nodes.
     *
     * @return mixed
     */
    public function stats();

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function send(string $method, array $params = []);
}