<?php

namespace App\Services;

use Predis\Client;

class ReviewService
{
    private $redisClient;

    public function __construct()
    {
        $this->redisClient = new Client('tcp://51.195.148.42:6379');
    }

    public function clearCache($productId = -1)
    {
        if ($productId == -1)
        {
            $keys = $this->redisClient->keys('product_*');

            $result = 0;
            foreach ($keys as $key)
            {
                $result +=  $this->redisClient->del($key);
            }
        }
        else
        {
            $result = $this->redisClient->del('product_'.$productId);
        }

        return $result;
    }

    public function getReviews($productId)
    {
        $cacheKey = 'product_'.$productId;

        if ($this->redisClient->exists($cacheKey))
        {
            $cache = $this->redisClient->get($cacheKey);

            $value = json_decode($cache);

            $count[1]   = $value->count;
            $average[1] = $value->average;
        }
        else
        {
            $reviewUrl = "http://www.feefo.com/feefo/xmlfeed.jsp?logon=www.amara.co.uk&vendorref=$productId&limit=1";

            $reviews = \file_get_contents($reviewUrl);

            $count = [];
            \preg_match('/\\<COUNT\\>([0-9]+)\\<\\/COUNT\\>/i', $reviews, $count);

            $average = [];
            \preg_match('/\\<AVERAGE\\>([0-9]+)\\<\\/AVERAGE\\>/i', $reviews, $average);

            $this->redisClient->set($cacheKey, json_encode(['count' => $count[1], 'average' => $average[1]]));
            $this->redisClient->expire($cacheKey, 86400);
        }

        $result = [
            'count' => $count[1],
            'average' => $average[1]
        ];

        return $result;
    }
}