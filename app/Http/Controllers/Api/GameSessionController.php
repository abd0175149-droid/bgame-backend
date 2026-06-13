<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameSession;
use App\Models\Leaderboard;
use Illuminate\Http\Request;

class GameSessionController extends Controller
{
    /** POST /api/sessions/start */
    public function start(Request $request)
    {
        $data = $request->validate([
            'place_id' => 'nullable|exists:saved_places,id',
            'mode'     => 'required|in:casual,ranked',
        ]);

        // إنهاء أي جلسة نشطة قديمة
        $request->user()->gameSessions()
            ->where('status', 'active')
            ->update(['status' => 'abandoned', 'ended_at' => now()]);

        $session = $request->user()->gameSessions()->create([
            'place_id' => $data['place_id'] ?? null,
            'mode'     => $data['mode'],
            'status'   => 'active',
        ]);

        return response()->json($session, 201);
    }

    /** POST /api/sessions/{id}/end */
    public function end(Request $request, GameSession $gameSession)
    {
        if ($gameSession->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'score'               => 'required|integer|min:0',
            'waves_survived'      => 'required|integer|min:0',
            'resources_collected' => 'required|integer|min:0',
            'duration_seconds'    => 'required|integer|min:0',
        ]);

        $gameSession->update(array_merge($data, [
            'status'   => 'completed',
            'ended_at' => now(),
        ]));

        // تحديث نقاط الرانك إذا كان الوضع ranked
        if ($gameSession->mode === 'ranked') {
            $rankPoints = $this->calculateRankPoints($data);

            $request->user()->increment('rank_points', $rankPoints);

            // تحديث Leaderboard
            Leaderboard::updateOrCreate(
                ['user_id' => $request->user()->id, 'season' => 1],
                [
                    'rank_points' => $request->user()->rank_points,
                    'rank_tier'   => $this->getTier($request->user()->rank_points),
                ]
            );
        }

        return response()->json([
            'session'     => $gameSession->fresh(),
            'rank_points' => $request->user()->rank_points,
        ]);
    }

    private function calculateRankPoints(array $data): int
    {
        return ($data['waves_survived'] * 10)
             + ($data['resources_collected'] * 2)
             + intdiv($data['score'], 100);
    }

    private function getTier(int $points): string
    {
        return match(true) {
            $points >= 3000 => 'Diamond',
            $points >= 1500 => 'Gold',
            $points >= 500  => 'Silver',
            default         => 'Bronze',
        };
    }
}
