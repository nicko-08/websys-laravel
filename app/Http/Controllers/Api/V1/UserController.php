<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Users\CreateUser;
use App\Actions\Users\UpdateUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Events\UserModified;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Users
 * User management endpoints
 */
class UserController extends Controller
{
    private const PER_PAGE = 20;

    public function __construct(
        private readonly CreateUser $createUser,
        private readonly UpdateUser $updateUser
    ) {}

    /**
     * List users
     *
     * @authenticated
     *
     * @apiResourceCollection App\Http\Resources\UserResource
     *
     * @apiResourceModel App\Models\User
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);

        return UserResource::collection(
            User::query()
                ->latest()
                ->paginate(self::PER_PAGE)
        );
    }

    /**
     * Create user
     *
     * @authenticated
     *
     * @bodyParam name string required Full name of the user. Example: Juan Dela Cruz
     * @bodyParam email string required Unique email address. Example: juan.delacruz@gov.ph
     * @bodyParam password string required Account password. Example: StrongP@ssw0rd
     * @bodyParam password_confirmation string required Must match the password. Example: StrongP@ssw0rd
     * @bodyParam role string required User role. Example: budget-officer
     *
     * @apiResource App\Http\Resources\UserResource
     *
     * @apiResourceModel App\Models\User
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $user = $this->createUser->execute($request->validated());

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Show user
     *
     * @authenticated
     *
     * @apiResource App\Http\Resources\UserResource
     *
     * @apiResourceModel App\Models\User
     */
    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);

        return new UserResource($user);
    }

    /**
     * Update user
     *
     * @authenticated
     *
     * @bodyParam name string Full name of the user. Example: Juan Dela Cruz
     * @bodyParam email string Unique email address. Example: juan.delacruz@gov.ph
     * @bodyParam password string New account password (optional). Example: StrongP@ssw0rd
     * @bodyParam password_confirmation string Required if password is present. Must match the password. Example: StrongP@ssw0rd
     * @bodyParam role string User role. Example: budget-officer
     *
     * @apiResource App\Http\Resources\UserResource
     *
     * @apiResourceModel App\Models\User
     */
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $this->authorize('update', $user);

        $user = $this->updateUser->execute($user, $request->validated());

        return new UserResource($user);
    }

    /**
     * Delete user
     *
     * @authenticated
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        // Prevent self-deletion
        if ($user->id === request()->user()->id) {
            return response()->json(
                ['message' => 'You cannot delete your own account.'],
                Response::HTTP_FORBIDDEN
            );
        }

        // Fire audit event BEFORE deleting
        event(new UserModified(
            model: $user,
            user: request()->user(),
            action: 'deleted'
        ));

        $user->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
