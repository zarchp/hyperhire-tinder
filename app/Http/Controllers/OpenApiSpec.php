<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Tinder Clone Backend API",
 *     version="1.0.0",
 *     description="Backend services for a Tinder-style application, built with Laravel. Authentication is handled via the custom X-User-Id header for testing."
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Main API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="X-User-Id",
 *     type="apiKey",
 *     in="header",
 *     name="X-User-Id",
 *     description="The ID of the user performing the action (simulating an authenticated user). Defaults to 1 if omitted."
 * )
 *
 * @OA\Schema(
 *     schema="UserPicture",
 *     title="User Picture",
 *     description="A picture belonging to a user profile.",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="image_url", type="string", example="https://loremflickr.com/500/500/face,person"),
 *     @OA\Property(property="order", type="integer", example=0, description="Order of the picture (0 is primary).")
 * )
 *
 * @OA\Schema(
 *     schema="UserProfile",
 *     title="User Profile",
 *     description="A complete profile for a user.",
 *     @OA\Property(property="id", type="integer", example=10),
 *     @OA\Property(property="name", type="string", example="Jane Doe"),
 *     @OA\Property(property="age", type="integer", example=25),
 *     @OA\Property(property="location", type="string", example="Jakarta"),
 *     @OA\Property(
 *         property="pictures",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/UserPicture")
 *     )
 * )
 */
class OpenApiSpec
{
    //
}
