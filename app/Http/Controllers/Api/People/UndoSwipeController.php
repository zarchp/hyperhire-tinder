<?php

namespace App\Http\Controllers\Api\People;

use App\Http\Controllers\Controller;
use App\Models\Swipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Delete(
 *     path="/api/people/undo-swipes",
 *     operationId="undoLastSwipe",
 *     summary="Undoes the single most recent swipe action performed by the current user.",
 *     tags={"People"},
 *      x={"order": 4},
 *     security={{"X-User-Id": {}}},
 *     @OA\Response(
 *         response=204,
 *         description="Successfully deleted the last swipe record. No content is returned."
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found (No recent swipes exist for this user)."
 *     )
 * )
 */
class UndoSwipeController extends Controller
{
    public function __invoke(Request $request)
    {
        $currentUserId = (int) $request->header('X-User-Id');

        $lastSwipe = Swipe::query()
            ->where('actor_user_id', $currentUserId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastSwipe) {
            return response()->json([
                'message' => 'No recent swipes found to undo.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $lastSwipe->delete();

        return response()->json([], JsonResponse::HTTP_NO_CONTENT);
    }
}
