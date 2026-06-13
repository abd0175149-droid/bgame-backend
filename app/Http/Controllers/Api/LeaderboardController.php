<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leaderboard;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    /** GET /api/leaderboard — أفضل 50 لاعب */
    public function index()
    {
        $board = Leaderboard::with('user:id,username,avatar')
            ->orderByDesc('rank_points')
            ->limit(50)
            ->get()
            ->map(function ($entry, $index) {
                return [
                    'rank'        => $index + 1,
                    'username'    => $entry->user->username,
                    'avatar'      => $entry->user->avatar,
                    'rank_points' => $entry->rank_points,
                    'rank_tier'   => $entry->rank_tier,
                    'wins'        => $entry->wins,
                ];
            });

        return response()->json($board);
    }

    /** GET /api/leaderboard/my-rank — رتبة اللاعب الحالي */
    public function myRank(Request $request)
    {
        $user   = $request->user();
        $entry  = Leaderboard::where('user_id', $user->id)->where('season', 1)->first();

        $position = Leaderboard::where('season', 1)
            ->where('rank_points', '>', $entry?->rank_points ?? 0)
            ->count() + 1;

        return response()->json([
            'position'    => $position,
            'rank_points' => $entry?->rank_points ?? 0,
            'rank_tier'   => $entry?->rank_tier ?? 'Bronze',
            'wins'        => $entry?->wins ?? 0,
        ]);
    }
}
