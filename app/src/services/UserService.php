<?php

namespace App\Services;

use App\DTOs\RegisterUserDTO;
use App\DTOs\UpdateUserDTO;
use App\Exceptions\PendingFineException;
use App\Exceptions\UserBlockedException;
use App\Models\Fine;
use App\Models\User;
use Illuminate\Support\Collection;

class UserService
{
    public function createUser(RegisterUserDTO $dto): User
    {
        return User::create([
            'name'     => $dto->name,
            'cpf'      => $dto->cpf,
            'email'    => $dto->email,
            'password' => $dto->password,
            'role'     => $dto->role,
        ]);
    }

    public function updateUser(string $id, UpdateUserDTO $dto): User
    {
        $user = $this->findUser($id);

        $data = array_filter([
            'name'     => $dto->name,
            'email'    => $dto->email,
            'password' => $dto->password,
            'role'     => $dto->role,
        ], fn ($value) => $value !== null);

        $user->update($data);

        return $user->fresh();
    }

    public function deleteUser(string $id): void
    {
        $this->findUser($id)->delete();
    }

    public function blockUser(string $id): User
    {
        $user = $this->findUser($id);
        $user->update(['blocked' => true]);

        return $user->fresh();
    }

    public function unblockUser(string $id): User
    {
        $user = $this->findUser($id);
        $user->update(['blocked' => false]);

        return $user->fresh();
    }

    public function listUsers(array $filters = []): Collection
    {
        $query = User::query();

        if (!empty($filters['role'])) {
            $query->byRole($filters['role']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        return $query->get();
    }

    public function findUser(string $id): User
    {
        return User::findOrFail($id);
    }

    public function assertUserNotBlocked(User $user): void
    {
        if ($user->blocked) {
            throw new UserBlockedException();
        }
    }

    public function assertNoPendingFines(User $user): void
    {
        $hasPendingFines = Fine::where('user_id', $user->id)
            ->where('paid', false)
            ->exists();

        if ($hasPendingFines) {
            throw new PendingFineException();
        }
    }
}
