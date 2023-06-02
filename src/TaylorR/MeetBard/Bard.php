<?php

declare(strict_types=1);

namespace TaylorR\MeetBard;

use pocketmine\utils\Internet;
use pocketmine\utils\InternetException;
use pocketmine\utils\InternetRequestResult;
use TaylorR\MeetBard\security\User;

class Bard {

    private int $requestid;
    private string $conversation_id = "";
    private string $response_id = "";
    private string $choice_id = "";

    public function __construct(
        private User $user,
        private int $timeout = 10
    ){
        $this->requestid = rand(pow(10, 3-1), pow(10, 3)-1);
        $this->getUser()->setSNlM0e($this->_get_snim0e());
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->user->getSNlM0e() !== "";
    }

    private function _get_snim0e(): string {
        $result = $this->getCURL();
        $body = $result->getBody();
        preg_match('/"SNlM0e":"(.*?)"/', $body, $matches);
        return $matches[1] ?? "";
    }

    /**
     * @param string $question
     * @return array|string[]|null
     */
    public function ask(string $question): ?array {
        $params = array(
            "bl" => "boq_assistant-bard-web-server_20230514.20_p0",
            "_reqid" => strval($this->requestid),
            "rt" => "c",
        );
        $question_struct = array(
            array(
                $question
            ),
            null,
            array(
                $this->conversation_id,
                $this->response_id,
                $this->choice_id
            ),
        );
        $data = array(
            "f.req" => json_encode([null, json_encode($question_struct)]),
            "at" => $this->getUser()->getSNlM0e(),
        );
        $url = 'https://bard.google.com/u/1/_/BardChatUi/data/assistant.lamda.BardFrontendService/StreamGenerate?' . http_build_query($params);
        $result = $this->postCURL($url, $data);
        $json = json_decode(explode("\n", $result->getBody())[3], true)[0][2];
        if (is_null($json)) {
            return array(
                "content" => "Response error" . $json
            );
        }
        $body_struct = json_decode($json, true);
        $return = array(
            "content" => $body_struct[0][0],
            "conversation_id" => $body_struct[1][0],
            "response_id" => $body_struct[1][1],
            "factualityQueries" => $body_struct[3],
            "textQuery" => $body_struct[2][0] ?? "",
            "choices" => array_map(function ($i) {
                return array(
                    "id" => $i[0],
                    "content" => $i[1]
                );
            }, $body_struct[4]),
        );
        $this->conversation_id = $return["conversation_id"];
        $this->response_id = $return["response_id"];
        $this->choice_id = $return["choices"][0]["id"];
        $this->requestid += 1000;

        return $return;
    }

    /**
     * @return InternetRequestResult|null
     */
    private function getCURL(): ?InternetRequestResult {
        try {
            $extraOpts = array(
                CURLOPT_COOKIE => '__Secure-1PSID=' . $this->getUser()->getToken()
            );
            return Internet::simpleCurl("https://bard.google.com/u/1/?hl=en", $this->timeout, [], $extraOpts);
        }catch(InternetException $ex){
            $err = $ex->getMessage();
            return null;
        }
    }

    /**
     * @param string $url
     * @param array $data
     * @return InternetRequestResult|null
     */
    private function postCURL(string $url, array $data): ?InternetRequestResult
    {
        try {
            $extraOpts = [
                CURLOPT_COOKIE => '__Secure-1PSID=' . $this->getUser()->getToken(),
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => http_build_query($data)
            ];

            return Internet::simpleCurl($url, $this->timeout, [], $extraOpts);
        } catch(InternetException $ex){
            $err = $ex->getMessage();
            return null;
        }
    }
}