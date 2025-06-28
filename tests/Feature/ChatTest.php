<?php

namespace Tests\Feature;

use App\Events\ChatMessage;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('chat');
    }

    public function test_can_send_message_successfully(): void
    {
        Event::fake();

        $messageData = [
            'content' => 'Hello, this is a test message!',
            'channel' => 'general',
            'user_name' => 'Test User'
        ];

        $response = $this->postJson('/api/chat/send', $messageData);

        $response->assertStatus(201)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Message sent successfully'
                ]);

        // Verify message was saved to database
        $this->assertDatabaseHas('messages', [
            'content' => 'Hello, this is a test message!',
            'channel' => 'general',
            'user_name' => 'Test User'
        ]);

        // Verify event was dispatched
        Event::assertDispatched(ChatMessage::class);
    }

    public function test_message_validation_works(): void
    {
        // Test empty content
        $response = $this->postJson('/api/chat/send', [
            'content' => ''
        ]);
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['content']);

        // Test content too long
        $response = $this->postJson('/api/chat/send', [
            'content' => str_repeat('a', 1001)
        ]);
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['content']);

        // Test invalid channel name
        $response = $this->postJson('/api/chat/send', [
            'content' => 'Valid message',
            'channel' => 'invalid channel name!'
        ]);
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['channel']);
    }

    public function test_can_retrieve_messages(): void
    {
        // Create test messages
        Message::createMessage('First message', 'general', 'User 1');
        Message::createMessage('Second message', 'general', 'User 2');
        Message::createMessage('Third message', 'other', 'User 3');

        $response = $this->getJson('/api/chat/messages/general');

        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success'
                ])
                ->assertJsonCount(2, 'data');
    }

    public function test_rate_limiting_works(): void
    {
        $messageData = [
            'content' => 'Test message',
            'channel' => 'general'
        ];

        // Send 10 messages (should work)
        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/chat/send', $messageData);
            $response->assertStatus(201);
        }

        // 11th message should be rate limited
        $response = $this->postJson('/api/chat/send', $messageData);
        $response->assertStatus(429)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Too many messages. Please wait before sending another message.'
                ]);
    }

    public function test_message_model_methods(): void
    {
        $message = Message::createMessage(
            'Test content',
            'test-channel',
            'Test User',
            'user123'
        );

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('Test content', $message->content);
        $this->assertEquals('test-channel', $message->channel);
        $this->assertEquals('Test User', $message->user_name);
        $this->assertEquals('user123', $message->user_id);

        // Test broadcast array
        $broadcastData = $message->toBroadcastArray();
        $this->assertArrayHasKey('id', $broadcastData);
        $this->assertArrayHasKey('content', $broadcastData);
        $this->assertArrayHasKey('user_name', $broadcastData);
        $this->assertArrayHasKey('timestamp', $broadcastData);
        $this->assertArrayHasKey('formatted_time', $broadcastData);
    }

    public function test_legacy_route_still_works(): void
    {
        Event::fake();

        $response = $this->postJson('/send-message', [
            'content' => 'Legacy message test'
        ]);

        $response->assertStatus(201);
        Event::assertDispatched(ChatMessage::class);
    }
}
