<?php

namespace Modules\BookStack\Auth\Access;

use stdClass;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Modules\BookStack\Auth\User;
use Illuminate\Database\Connection as Database;
use Modules\BookStack\Exceptions\UserTokenExpiredException;
use Modules\BookStack\Exceptions\UserTokenNotFoundException;

class UserTokenService
{
    /**
     * Name of table where user tokens are stored.
     *
     * @var string
     */
    protected $tokenTable = 'user_tokens';

    /**
     * Token expiry time in hours.
     *
     * @var int
     */
    protected $expiryTime = 24;

    protected $db;

    /**
     * UserTokenService constructor.
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Delete all email confirmations that belong to a user.
     *
     * @return mixed
     */
    public function deleteByUser(User $user)
    {
        return $this->db->table($this->tokenTable)
            ->where('user_id', '=', $user->id)
            ->delete();
    }

    /**
     * Get the user id from a token, while check the token exists and has not expired.
     *
     *
     * @throws UserTokenNotFoundException
     * @throws UserTokenExpiredException
     */
    public function checkTokenAndGetUserId(string $token): int
    {
        $entry = $this->getEntryByToken($token);

        if (is_null($entry)) {
            throw new UserTokenNotFoundException('Token "' . $token . '" not found');
        }

        if ($this->entryExpired($entry)) {
            throw new UserTokenExpiredException("Token of id {$entry->id} has expired.", $entry->user_id);
        }

        return $entry->user_id;
    }

    /**
     * Creates a unique token within the email confirmation database.
     */
    protected function generateToken(): string
    {
        $token = Str::random(24);
        while ($this->tokenExists($token)) {
            $token = Str::random(25);
        }

        return $token;
    }

    /**
     * Generate and store a token for the given user.
     */
    protected function createTokenForUser(User $user): string
    {
        $token = $this->generateToken();
        $this->db->table($this->tokenTable)->insert([
            'user_id' => $user->id,
            'token' => $token,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return $token;
    }

    /**
     * Check if the given token exists.
     */
    protected function tokenExists(string $token): bool
    {
        return $this->db->table($this->tokenTable)
            ->where('token', '=', $token)->exists();
    }

    /**
     * Get a token entry for the given token.
     *
     * @return object|null
     */
    protected function getEntryByToken(string $token)
    {
        return $this->db->table($this->tokenTable)
            ->where('token', '=', $token)
            ->first();
    }

    /**
     * Check if the given token entry has expired.
     */
    protected function entryExpired(stdClass $tokenEntry): bool
    {
        return Carbon::now()->subHours($this->expiryTime)
            ->gt(new Carbon($tokenEntry->created_at));
    }
}