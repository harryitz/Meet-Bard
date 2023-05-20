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
    private string $SNlM0e = "";

    public function __construct(
        private User $user,
        private int $timeout = 10
    ){
        $this->requestid = rand(pow(10, 3-1), pow(10, 3)-1);
        $this->SNlM0e = $this->_get_snim0e();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isValid(): bool
    {
        return $this->SNlM0e !== "";
    }


    private function _get_snim0e(): string {
        $result = $this->getCURL();
        $body = $result->getBody();
        preg_match('/"SNlM0e":"(.*?)"/', $body, $matches);
        return $matches[1] ?? "";
    }

    public function ask(string $question): ?array {
        $params = [
            "bl" => "boq_assistant-bard-web-server_20230514.20_p0",
            "_reqid" => strval($this->requestid),
            "rt" => "c",
        ];
        $question_struct = [
            ["What is Ddos"],
            null,
            [$this->conversation_id, $this->response_id, $this->choice_id],
        ];
        $data = [
            "f.req" => json_encode([null, json_encode($question_struct)]),
            "at" => $this->SNlM0e,
        ];

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
                return ["id" => $i[0], "content" => $i[1]];
            }, $body_struct[4]),
        );
        $this->conversation_id = $return["conversation_id"];
        $this->response_id = $return["response_id"];
        $this->choice_id = $return["choices"][0]["id"];
        $this->requestid += 1000;

        return $return;
    }

    private function getCURL(): ?InternetRequestResult {
        try {
            $headers = [
                "Host: bard.google.com",
                "X-Same-Domain: 1",
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36",
                "Content-Type: application/x-www-form-urlencoded;charset=UTF-8",
                "Origin: https://bard.google.com",
                "Referer: https://bard.google.com/",
            ];
            $extraOpts = [
                CURLOPT_COOKIE => '__Secure-1PSID=' . $this->getUser()->getToken()
            ];
            return Internet::simpleCurl("https://bard.google.com/u/1/?hl=en", $this->timeout, $headers, $extraOpts);
        }catch(InternetException $ex){
            $err = $ex->getMessage();
            return null;
        }
    }

    private function postCURL(string $url, array $data): ?InternetRequestResult
    {
        try {
            $headers = [
                "Host: bard.google.com",
                "X-Same-Domain: 1",
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36",
                "Content-Type: application/x-www-form-urlencoded;charset=UTF-8",
                "Origin: https://bard.google.com",
                "Referer: https://bard.google.com/",
            ];
            $extraOpts = [
                CURLOPT_COOKIE => '__Secure-1PSID=' . $this->getUser()->getToken(),
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => http_build_query($data)
            ];

            return Internet::simpleCurl($url, $this->timeout, $headers, $extraOpts);
        } catch(InternetException $ex){
            $err = $ex->getMessage();
            return null;
        }
    }
}