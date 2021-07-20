<?php

namespace srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method;

use ilCurlConnection;

/**
 * Class ExternalDateMethod
 *
 * @package srag\Plugins\SrLearningProgressReset\LearningProgressReset\Settings\Method
 */
class ExternalDateMethod extends AbstractMethod
{

    const ID = 2;
    const KEY = "external_date";


    /**
     * @inheritDoc
     */
    protected function getDate(int $user_id) : string
    {
        $rest_url = "get_date";

        $headers = [
            "Accept"       => "application/json",
            "Content-Type" => "application/json"
        ];

        $post_data = [
            "object_id" => $this->settings->getObjId(),
            "user_id"   => $user_id
        ];

        $result = $this->doRequest($rest_url, $headers, json_encode($post_data));

        $result_json = json_decode($result, true);

        if (
            empty($result_json)
            || empty($date = strval($result_json["date"]))
        ) {
            throw $this->exception($user_id, "Invalid response : " . $result);
        }

        return $date;
    }


    /**
     * @inheritDoc
     */
    protected function setDateToToday(int $user_id) : bool
    {
        $rest_url = "set_date_to_today";

        $headers = [
            "Accept"       => "application/json",
            "Content-Type" => "application/json"
        ];

        $post_data = [
            "object_id" => $this->settings->getObjId(),
            "user_id"   => $user_id
        ];

        $result = $this->doRequest($rest_url, $headers, json_encode($post_data));

        $result_json = json_decode($result, true);

        if (
            empty($result_json)
            || empty($ok = boolval($result_json["ok"]))
        ) {
            return false;
        }

        return $ok;
    }


    /**
     * @param string $rest_url
     * @param array  $headers
     * @param mixed  $post_data
     *
     * @return string|null
     */
    private function doRequest(string $rest_url, array $headers, $post_data = null) : ?string
    {
        $curlConnection = null;

        try {
            $curlConnection = $this->initCurlConnection($this->settings->getExternalDateUrl() . "/" . $rest_url, $headers);

            if ($post_data !== null) {
                $curlConnection->setOpt(CURLOPT_POST, true);
                $curlConnection->setOpt(CURLOPT_POSTFIELDS, $post_data);
            }

            $result = $curlConnection->exec();

            return $result;
        } finally {
            if ($curlConnection !== null) {
                $curlConnection->close();
                $curlConnection = null;
            }
        }
    }


    /**
     * @param string $url
     * @param array  $headers
     *
     * @return ilCurlConnection
     */
    private function initCurlConnection(string $url, array $headers) : ilCurlConnection
    {
        $curlConnection = new ilCurlConnection($url);

        $curlConnection->init();

        $headers["User-Agent"] = "ILIAS " . self::version()->getILIASVersion();
        $curlConnection->setOpt(CURLOPT_HTTPHEADER, array_map(function (string $key, string $value) : string {
            return ($key . ": " . $value);
        }, array_keys($headers), $headers));

        $curlConnection->setOpt(CURLOPT_FOLLOWLOCATION, true);

        $curlConnection->setOpt(CURLOPT_RETURNTRANSFER, true);

        $curlConnection->setOpt(CURLOPT_VERBOSE, false/*(intval(DEVMODE) === 1)*/);

        return $curlConnection;
    }
}
