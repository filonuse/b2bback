<?php

namespace App\Repositories;

use App\Models\User;
use Czim\Repository\BaseRepository;
use App\Models\Message;

class MessageRepository extends BaseRepository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Message::class;
    }

    /**
     * @param User $user
     * @param int $toUserId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function history(User $user, int $toUserId)
    {
        return $this->query()
            ->orWhereIn('id', function ($query) use($user, $toUserId){
                return $query
                    ->select('um1.message_id')
                    ->from('user_messages as um1')
                    ->where('um1.from_user_id', $user->id)
                    ->where('um1.to_user_id', $toUserId);
            })
            ->orWhereIn('id', function ($query) use($user, $toUserId){
                return $query
                    ->select('um2.message_id')
                    ->from('user_messages as um2')
                    ->where('um2.from_user_id', $toUserId)
                    ->where('um2.to_user_id', $user->id);
            })
            ->with('users')
            ->orderBy('created_at', 'desc');
    }
}
