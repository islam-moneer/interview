<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function getCommentsForPosts()
    {
        $posts = Post::where(['user_id' => $this->id])->get();
        $result = [];
        foreach ($posts as $post)
        {
            $comments = Comment::where(['post_id' => $post->id])->get();
            $result += $comments;
        }

        return $result;
    }

    public function getFriendIds($status = 1)
    {
        $friends = Friend::where(['user_id' => $this->id])
            ->andWhere(['status' => $status])
            ->get();

        return array_map(function($item) {return $item['id'];}, $friends);
    }

    public function addFriend($userId)
    {
        $friend = new Friend();
        $friend->user_id = $this->id;
        $friend->friend_id = $userId;
        $friend->status = 1;
        $friend->save();
        return true;
    }

    public function deleteFriend($userId): void
    {
        $friend = Friend::where(['friend_id' => $userId])->first();
        $friend->delete();
        return true;
    }

    public function hasPosts()
    {
        $posts = Post::where(['user_id' => $this->id])->get();
        return count($posts);
    }

    public function hasActivePosts()
    {
        $activePosts = Post::where(['user_id' => $this->id, 'status' => 1])->get();
        return !empty($activePosts);
    }
}
