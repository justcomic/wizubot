<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
require_once('./LINEBotTiny.php');
$channelAccessToken = 'a17yHIUmfGvet+euU6yc5dc3fMzI1C+6WtGb4yV1ksSDl31Bj+3Z/VtqlZdrngwaP4z4zcOw4tW+HQhPJUVbqiutfFfEYVg3w5vFDWm3YRXapHHlPUdtk0/JSsNf45hPWFvI88XeWzyzwHrgmdoYcgdB04t89/1O/w1cDnyilFU=';
$channelSecret = 'b438c36b8015d23dd825557735183ff1';
$client = new LINEBotTiny($channelAccessToken, $channelSecret);
foreach ($client->parseEvents() as $event) {
    switch ($event['type']) {
        case 'message':
            $message = $event['message'];
            $json = file_get_contents('https://spreadsheets.google.com/feeds/list/1H81zPYJUNumL0Tu8fW5f0nCMtKHtqZ6ubUFU-Rl4Bsw/od6/public/values?alt=json');
            $data = json_decode($json, true);
            $result = array();
             foreach ($data['feed']['entry'] as $item) {
                $keywords = explode(',', $item['gsx$keyword']['$t']);
                foreach ($keywords as $keyword) {
                    if (mb_strpos($message['text'], $keyword) !== false) {
                        $candidate = array(
                            'thumbnailImageUrl' => $item['gsx$photourl']['$t'],
                            'title' => $item['gsx$title']['$t'],
                            'text' => $item['gsx$title']['$t'],
                            'actions' => array(
                                array(
                                    'type' => 'uri',
                                    'label' => '查看詳情',
                                    'uri' => $item['gsx$url']['$t'],
                                    ),
                                ),
                            );
                        array_push($result, $candidate);
                    }
                }
            }
			foreach ($data['feed']['entry'] as $item) {
                $keywords = explode(',', $item['gsx$keyword']['$t']);
                foreach ($keywords as $keyword) {
                    if (mb_strpos($message['text'], $keyword) !== false) {
						switch ($message['type']) {
							case 'text':
								$client->replyMessage(array(
									'replyToken' => $event['replyToken'],
									'messages' => array(
										array(
											'type' => 'image',
											'originalContentUrl' => $item['gsx$photourl']['$t'],
											'previewImageUrl' => $item['gsx$photourl']['$t'],
										),
										array(
											'type' => 'text',
											'text' => '【連結】   '.$item['gsx$title']['$t'],
										), 
										array(
											'type' => 'text',
											'text' => $item['gsx$url']['$t'],
										),
										
                        ),
                    ));
                    break;
                default:
                    error_log("Unsupporeted message type: " . $message['type']);
                    break;
						}
					}
                }
            }			
            break;
        default:
            error_log("Unsupporeted event type: " . $event['type']);
            break;
    }
};
?>