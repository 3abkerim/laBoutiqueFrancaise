<?php

namespace App\Class;

use Mailjet\Client;
use Mailjet\Resources;


class Mail
{
    private $api_key = '5ba21391ca8adae159fdbe27665c3ce2';
    private $api_key_secret = 'e2159e154108df455270a90f238666d5';
    public function send($to_email, $to_name, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "contact@azizdev.fr",
                        'Name' => "La Boutique Française"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 5591620,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'CONTENT' => $content,
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}
