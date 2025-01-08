<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Hamcrest\Type\IsInteger;

class DashboardController extends Controller
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

    private function getDiscordAnnouncements()
    {
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

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $topReports = DB::table('reports')
            ->join('users', 'users.id', '=', 'reports.user_id')
            ->select('users.charactername', DB::raw('count(reports.user_id) as reportCount'))
            ->groupBy('users.charactername')
            ->orderBy('reportCount', 'desc')
            ->limit(5)
            ->get();

        $reportCount = DB::table('reports')
            ->select(
                DB::raw('count(reports.user_id) as reportCount'),
            )
            ->where('reports.user_id', '=', $request->user()->id)
            ->groupBy('reports.user_id')
            ->orderBy('reportCount', 'desc')
            ->limit(1)
            ->get();

        if ($reportCount->isEmpty()) {
            $reportCount = collect([
                (object) [
                    'reportCount' => '0'
                ]
            ]);
        }

        $lastReportDate = DB::table('reports')
            ->select('reports.created_at')
            ->where('reports.user_id', '=', $request->user()->id)
            ->orderBy('reports.created_at', 'desc')
            ->limit(1)
            ->get();

        if ($lastReportDate->isEmpty()) {
            $lastReportDate = collect([
                (object) [
                    'created_at' => '-'
                ]
            ]);
        }

        $dutyMinuteSum = DB::table('duty_times')
            ->select(
                DB::raw('sum(duty_times.minutes) as dutyMinuteSum'),
            )
            ->where('duty_times.user_id', '=', $request->user()->id)
            ->limit(1)
            ->get();

        if ($dutyMinuteSum->isEmpty()) {
            $dutyMinuteSum = collect([
                (object) [
                    'dutyMinuteSum' => '0'
                ]
            ]);
        }

        $allReportCount = DB::table('reports')
            ->select(
                DB::raw('count(reports.id) as allReportCount'),
            )
            ->limit(1)
            ->get();

        if ($allReportCount->isEmpty()) {
            $allReportCount = collect([
                (object) [
                    'allReportCount' => '0'
                ]
            ]);
        }

        $topDutyTime = DB::table('duty_times')
            ->select(
                DB::raw('sum(duty_times.minutes) as topDutyTime'),
                'duty_times.user_id'
            )
            ->groupBy('user_id')
            ->orderBy('topDutyTime', 'desc')
            ->limit(1)
            ->get();

        $sumDutyTime = DB::table('duty_times')
            ->select(
                DB::raw('sum(duty_times.minutes) as sumDutyTime'),
            )
            ->get();

        if ($sumDutyTime->isEmpty()) {
            $sumDutyTime = collect([
                (object) [
                    'sumDutyTime' => '0',
                ]
            ]);
        }

        if ($topDutyTime->isEmpty()) {
            $topDutyTime = collect([
                (object) [
                    'topDutyTime' => '0',
                    'user_id' => '-'
                ]
            ]);
        }

        if ($reportCount[0]->reportCount == '0' || $allReportCount[0]->allReportCount == '0') {
            $percentage = 0;
        } else {
            $percentage = round(($reportCount[0]->reportCount / $allReportCount[0]->allReportCount) * 100);
        }

        if ($topDutyTime[0]->topDutyTime == '0' || $dutyMinuteSum[0]->dutyMinuteSum == '0') {
            $minutesUntilTopDutyTime = 0;
        } else {
            $minutesUntilTopDutyTime = $topDutyTime[0]->topDutyTime - $dutyMinuteSum[0]->dutyMinuteSum;
        }

        $minimumDutyTime = 800;
        $minimumReportCount = 15;
        $minimumDoubleRankupDutyTime = 1800;
        $minimumDoubleRankupReportCount = 40;
        $discordAnnouncements = null;

        if (env('DISCORD_BOT_TOKEN') != "" && env('DISCORD_GUILD_ID') != "") {
            $discordAnnouncements = $this->getDiscordAnnouncements();
        }

        return view('dashboard', [
            'topReports' => $topReports,
            'reportCount' => $reportCount[0]->reportCount,
            'lastReportDate' => $lastReportDate[0]->created_at,
            'dutyMinuteSum' => $dutyMinuteSum[0]->dutyMinuteSum,
            'allReportCount' => $allReportCount[0]->allReportCount,
            'userReportPercentage' => $percentage,
            'minutesUntilTopDutyTime' => $minutesUntilTopDutyTime,
            'minimumDutyTime' => $minimumDutyTime,
            'minimumReportCount' => $minimumReportCount,
            'minimumDoubleRankupDutyTime' => $minimumDoubleRankupDutyTime,
            'minimumDoubleRankupReportCount' => $minimumDoubleRankupReportCount,
            'sumDutyTime'=> $sumDutyTime[0]->sumDutyTime,
            'discordAnnouncements' => $discordAnnouncements,
        ]);
    }
}
