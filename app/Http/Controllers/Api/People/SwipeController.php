<?php

namespace App\Http\Controllers\Api\People;

use App\Http\Controllers\Controller;
use App\Http\Requests\SwipeRequest;
use App\Models\Swipe;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/api/people/swipes",
 *     operationId="createSwipe",
 *     summary="Record a 'like' or 'dislike' action and check for a mutual match.",
 *     tags={"People"},
 *      x={"order": 3},
 *     security={{"X-User-Id": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"target_user_id", "type"},
 *             @OA\Property(
 *                 property="target_user_id",
 *                 type="integer",
 *                 example=12,
 *                 description="The ID of the user being swiped on."
 *             ),
 *             @OA\Property(
 *                 property="type",
 *                 type="string",
 *                 enum={"like", "dislike"},
 *                 example="like",
 *                 description="The type of swipe action."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Swipe recorded successfully.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Swipe recorded successfully."
 *             ),
 *             @OA\Property(
 *                 property="is_match",
 *                 type="boolean",
 *                 example=true,
 *                 description="True if a mutual like was found (only applies if type='like')."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad Request",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Cannot swipe on yourself."
 *             ),
 *         )
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Conflict",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Already swiped on this person."
 *             ),
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation Error"
 *     )
 * )
 */
class SwipeController extends Controller
{
    public function __invoke(SwipeRequest $request)
    {
        $currentUserId = (int) $request->header('X-User-Id');
        $targetUserId = (int) $request->input('target_user_id');
        $type = $request->input('type');

        if ($currentUserId === $targetUserId) {
            return response()->json([
                'message' => 'Cannot swipe on yourself.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $existingSwipe = Swipe::where('actor_user_id', $currentUserId)
            ->where('target_user_id', $targetUserId)
            ->first();
        if ($existingSwipe) {
            return response()->json([
                'message' => 'Already swiped on this person.',
            ], JsonResponse::HTTP_CONFLICT);
        }

        $isMatch = false;
        if ($type === 'like') {
            $isMatch = $this->checkMutualLike($currentUserId, $targetUserId);
        }

        Swipe::create([
            'actor_user_id' => $currentUserId,
            'target_user_id' => $request->target_user_id,
            'type' => $request->type,
        ]);

        return response()->json([
            'message' => 'Swipe recorded successfully.',
            'is_match' => $isMatch,
        ], JsonResponse::HTTP_CREATED);
    }

    private function checkMutualLike(int $actorUserId, int $targetUserId): bool
    {
        return Swipe::where('actor_user_id', $targetUserId)
            ->where('target_user_id', $actorUserId)
            ->where('type', 'like')
            ->exists();
    }
}
