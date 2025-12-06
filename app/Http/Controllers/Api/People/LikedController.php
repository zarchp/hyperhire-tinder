<?php

namespace App\Http\Controllers\Api\People;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeopleCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/api/people/liked",
 *     operationId="getLikedPeople",
 *     summary="Get list of people that the current user has liked.",
 *     tags={"People"},
 *      x={"order": 2},
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
 *         description="List of people the current user has liked.",
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
 *     )
 * )
 */
class LikedController extends Controller
{
    public function __invoke(Request $request)
    {
        $currentUserId = (int) $request->header('X-User-Id');
        $perPage = $request->query('per_page', 10);

        $likedUsers = User::query()
            ->with('pictures')
            ->whereHas('receivedSwipes', function ($query) use ($currentUserId) {
                $query->where('actor_user_id', $currentUserId)
                    ->where('type', 'like');
            })
            ->paginate($perPage);

        return new PeopleCollection($likedUsers)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_OK);
    }
}
