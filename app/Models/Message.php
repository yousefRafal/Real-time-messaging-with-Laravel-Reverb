<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Message Model
 * 
 * Represents a chat message in the system
 * 
 * @property int $id
 * @property string $content
 * @property string|null $user_name
 * @property string|null $user_id
 * @property string $channel
 * @property array|null $metadata
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Message extends Model
{
    use HasFactory;

    protected $connection = 'chat';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'user_name',
        'user_id',
        'channel',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get messages for a specific channel
     *
     * @param string $channel
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getChannelMessages(string $channel = 'general', int $limit = 50)
    {
        return static::where('channel', $channel)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Create a new message
     *
     * @param string $content
     * @param string $channel
     * @param string|null $userName
     * @param string|null $userId
     * @param array|null $metadata
     * @return static
     */
    public static function createMessage(
        string $content,
        string $channel = 'general',
        ?string $userName = null,
        ?string $userId = null,
        ?array $metadata = null
    ): static {
        return static::create([
            'content' => $content,
            'channel' => $channel,
            'user_name' => $userName,
            'user_id' => $userId,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get formatted message data for broadcasting
     *
     * @return array
     */
    public function toBroadcastArray(): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'user_name' => $this->user_name ?? 'Anonymous',
            'user_id' => $this->user_id,
            'channel' => $this->channel,
            'timestamp' => $this->created_at->toISOString(),
            'formatted_time' => $this->created_at->format('H:i'),
            'metadata' => $this->metadata,
        ];
    }
}
