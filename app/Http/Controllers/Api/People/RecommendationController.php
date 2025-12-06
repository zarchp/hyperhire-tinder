<?php

namespace App\Http\Controllers\Api\People;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeopleCollection;
use App\Models\Swipe;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/api/people",
 *     operationId="getRecommendedPeople",
 *     summary="Get list of recommended people to swipe on",
 *     tags={"People"},
 *     x={"order": 1},
 *     security={{"X-User-Id": {}}},
 *
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         required=false,
 *         description="The page number to retrieve",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         required=false,
 *         description="Number of results per page",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="List of profiles the current user has not yet swiped on (excluding themselves).",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/UserProfile")
 *             ),
 *             @OA\Property(property="links", type="object"),
 *             @OA\Property(property="meta", type="object")
 *         )
 *     ),
 * )
 */
class RecommendationController extends Controller
{
    public function __invoke(Request $request)
    {
        $currentUserId = (int) $request->header('X-User-Id');
        $perPage = $request->query('per_page', 10);
        $swipedUserIds = Swipe::where('actor_user_id', $currentUserId)->pluck('target_user_id');

        $recommendations = User::query()
            ->with('pictures')
            ->where('id', '!=', $currentUserId)
            ->whereNotIn('id', $swipedUserIds)
            ->paginate($perPage);

        return new PeopleCollection($recommendations)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_OK);
    }
}
