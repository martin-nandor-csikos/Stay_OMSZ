<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use DateTime;

class DiscordController extends Controller
{
    private function getDiscordRoles()
    {
        $client = new Client([
            'headers' => [
                'Authorization' => env('DISCORD_BOT_TOKEN'),
                'Accept-Charset' => 'UTF-8',
            ],
        ]);

        try {
            $response = $client->get('https://discord.com/api/v10/guilds/' . env("DISCORD_GUILD_ID") . '/roles');

            $body = (string) $response->getBody();
            $utf8Body = mb_convert_encoding($body, 'UTF-8');
            $roles = json_decode($utf8Body, true);

            return $roles;
        } catch (\Throwable $th) {
            return null;
        }
    }

    private function returnDiscordRole($roles, $id)
    {
        if ($roles == null) {
            return "(Nincs rang)";
        }

        $id_new = preg_replace("/[^0-9]/", "", $id);

        foreach ($roles as $role) {
            if ($role["id"] == $id_new) {
                return $role["name"];
            }
        }

        return null;
    }

    private function getDiscordUser($id)
    {
        $client = new Client([
            'headers' => [
                'Authorization' => env('DISCORD_BOT_TOKEN'),
                'Accept-Charset' => 'UTF-8',
            ],
        ]);

        try {
            $response = $client->get('https://discord.com/api/v10/guilds/' . env("DISCORD_GUILD_ID") . '/members/' . $id);
            $body = (string) $response->getBody();
            $utf8Body = mb_convert_encoding($body, 'UTF-8');
            $user = json_decode($utf8Body, true);

            return $user;
        } catch (\Throwable $th) {
            return null;
        }
    }

    private function returnDiscordUser($id)
    {
        $id_new = preg_replace("/[^0-9]/", "", $id);
        $user = $this->getDiscordUser($id_new);

        if ($user == null) {
            return "(Volt tag)";
        }

        return $user["nick"];
    }

    private function getDiscordChannel($id)
    {
        $client = new Client([
            'headers' => [
                'Authorization' => env('DISCORD_BOT_TOKEN'),
                'Accept-Charset' => 'UTF-8',
            ],
        ]);

        try {
            $response = $client->get('https://discord.com/api/v10/channels/' . $id);

            $body = (string) $response->getBody();
            $utf8Body = mb_convert_encoding($body, 'UTF-8');
            $channel = json_decode($utf8Body, true);

            return $channel;
        } catch (\Throwable $th) {
            return null;
        }
    }

    private function returnDiscordChannel($id)
    {
        $id_new = preg_replace("/[^0-9]/", "", $id);
        $channel = $this->getDiscordChannel($id_new);

        if ($channel == null) {
            return "(Nem létező channel)";
        }

        return $channel["name"];
    }

    public function getDiscordAnnouncements()
    {
        if (env('APP_ENV') == 'local') {
            return [
                [
                    "time" => "2024-07-01 12:00:00",
                    "author" => "DummyUser1",
                    "message" => "Welcome to the server! <i><b>@Admin</b></i>",
                    "images" => [
                        "https://via.placeholder.com/150"
                    ]
                ],
                [
                    "time" => "2024-07-02 14:30:00",
                    "author" => "DummyUser2",
                    "message" => "Don't forget the meeting at 5pm. <i>@Everyone</i>",
                    "images" => []
                ],
                [
                    "time" => "2024-07-03 09:15:00",
                    "author" => "DummyUser3",
                    "message" => "Check out the new channel: </i>#announcements</i>",
                    "images" => []
                ],
                [
                    "time" => "2024-07-04 18:45:00",
                    "author" => "DummyUser4",
                    "message" => "Server maintenance tonight.",
                    "images" => []
                ],
                [
                    "time" => "2024-07-05 20:00:00",
                    "author" => "DummyUser5",
                    "message" => "Happy Friday everyone!",
                    "images" => []
                ],
            ];
        }
        $client = new Client([
            'headers' => [
                'Authorization' => env('DISCORD_BOT_TOKEN'),
                'Accept-Charset' => 'UTF-8',
            ],
        ]);

        $messages = $client->get('https://discord.com/api/v10/channels/1225882367671931002/messages');
        $roles = $this->getDiscordRoles();

        $body = (string) $messages->getBody();
        $utf8Body = mb_convert_encoding($body, 'UTF-8');
        $messages = json_decode($utf8Body, true);

        $firstFiveMessages[][] = array();

        // I have no fucking idea how this works, but it does
        for ($i = 0; $i < 5; $i++) {
            $timestamp = new DateTime($messages[$i]["timestamp"]);
            $firstFiveMessages[$i]["time"] = $timestamp->format('Y-m-d H:i:s');
            $firstFiveMessages[$i]["author"] = $this->returnDiscordUser($messages[$i]["author"]["id"]);

            $words = explode(" ", $messages[$i]["content"]);

            foreach ($words as &$word) {
                $newline_explode = explode("\n", $word);
                foreach ($newline_explode as &$newline_explode_word) {
                    if (preg_match('/<@&[0-9]/', $newline_explode_word)) {
                        $newline_explode_word = "<i><b>@" . $this->returnDiscordRole($roles, $newline_explode_word) . "</b></i>";
                    }

                    if (preg_match('/<@[0-9]/', $newline_explode_word)) {
                        $newline_explode_word = "<i>@" . $this->returnDiscordUser($newline_explode_word) . "</i>";
                    }

                    if (preg_match('/<#[0-9]/', $newline_explode_word)) {
                        $newline_explode_word = "</i>#" . $this->returnDiscordChannel($newline_explode_word) . "</i>";
                    }
                }

                $word = implode("\n", $newline_explode);
            }

            $firstFiveMessages[$i]["message"] = implode(" ", $words);
            $firstFiveMessages[$i]["message"] = nl2br($firstFiveMessages[$i]['message']);

            if (count($messages[$i]["attachments"]) > 0) {
                for ($j = 0; $j < count($messages[$i]["attachments"]); $j++) {
                    $firstFiveMessages[$i]["images"][$j] = $messages[$i]["attachments"][$j]["url"];
                }
            }
        }

        return $firstFiveMessages;
    }
}
