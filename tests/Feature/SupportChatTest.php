<?php

namespace Tests\Feature;

use App\Ai\Agents\SupportBot;
use App\Models\AgentConversation;
use App\Models\AgentConversationMessage;
use App\Models\User;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\Data\Meta;
use Laravel\Ai\Responses\Data\Usage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_support_chat_page_loads_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('support-chat.index'));

        $response->assertOk();
        $response->assertSee('Customer Support Chatbot');
    }

    public function test_support_chat_store_persists_conversation_messages(): void
    {
        $user = User::factory()->create();

        $this->app->instance(SupportBot::class, new class extends SupportBot
        {
            public function prompt(
                string $prompt,
                array $attachments = [],
                \Laravel\Ai\Enums\Lab|array|string|null $provider = null,
                ?string $model = null,
                ?int $timeout = null,
            ): AgentResponse
            {
                return new AgentResponse(
                    invocationId: 'test-invocation',
                    text: 'Test response: '.$prompt,
                    usage: new Usage,
                    meta: new Meta(provider: 'test', model: 'test-model'),
                );
            }
        });

        $response = $this->actingAs($user)->postJson(route('support-chat.store'), [
            'message' => 'Kiem tra don ORD-00023',
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Test response: Kiem tra don ORD-00023',
            ]);

        $this->assertDatabaseCount('agent_conversations', 1);

        $conversation = AgentConversation::query()->first();
        $this->assertNotNull($conversation);

        $this->assertDatabaseHas('agent_conversation_messages', [
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => 'Kiem tra don ORD-00023',
        ]);

        $this->assertDatabaseHas('agent_conversation_messages', [
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => 'Test response: Kiem tra don ORD-00023',
        ]);

        $this->assertTrue(
            AgentConversationMessage::query()->where('conversation_id', $conversation->id)->count() === 2
        );
    }

    public function test_chat_send_route_uses_the_same_chat_controller(): void
    {
        $user = User::factory()->create();

        $this->app->instance(SupportBot::class, new class extends SupportBot
        {
            public function prompt(
                string $prompt,
                array $attachments = [],
                \Laravel\Ai\Enums\Lab|array|string|null $provider = null,
                ?string $model = null,
                ?int $timeout = null,
            ): AgentResponse
            {
                return new AgentResponse(
                    invocationId: 'test-chat-send',
                    text: 'Chat send response',
                    usage: new Usage,
                    meta: new Meta(provider: 'test', model: 'test-model'),
                );
            }
        });

        $response = $this->actingAs($user)->postJson(route('chat.send'), [
            'message' => 'Tu van ban hang',
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Chat send response',
            ]);
    }
}
