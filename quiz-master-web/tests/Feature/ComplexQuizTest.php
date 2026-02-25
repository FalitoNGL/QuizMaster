<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComplexQuizTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;

    /**
     * Setup: Membuat data dummy sebelum setiap test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Buat kategori dummy dengan semua required fields
        $this->category = new Category();
        $this->category->name = 'Test Category';
        $this->category->slug = 'test-category';
        $this->category->description = 'Kategori untuk testing';
        $this->category->icon_class = 'fa-test';
        $this->category->save();

        // Buat soal dummy
        $question = new Question();
        $question->category_id = $this->category->id;
        $question->type = 'single';
        $question->question_text = 'Apa ibu kota Indonesia?';
        $question->save();

        // Buat opsi jawaban
        $option1 = new Option();
        $option1->question_id = $question->id;
        $option1->option_text = 'Jakarta';
        $option1->is_correct = true;
        $option1->save();

        $option2 = new Option();
        $option2->question_id = $question->id;
        $option2->option_text = 'Bandung';
        $option2->is_correct = false;
        $option2->save();
    }

    // =========================================================================
    // TEST 1: USER FLOW (Login -> Kategori -> Soal)
    // =========================================================================

    /**
     * Test: User yang sudah login bisa mengakses halaman menu (kategori)
     */
    public function test_authenticated_user_can_access_menu_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Test Category');
    }

    /**
     * Test: User bisa mengakses halaman kuis berdasarkan slug kategori
     */
    public function test_user_can_access_quiz_page_by_category_slug(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/quiz/test-category');

        $response->assertStatus(200);
        $response->assertSee('Apa ibu kota Indonesia?');
    }

    /**
     * Test: Guest (tanpa login) tetap bisa akses halaman menu
     */
    public function test_guest_can_access_menu_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // =========================================================================
    // TEST 2: API ENDPOINT
    // =========================================================================

    /**
     * Test: API /api/categories mengembalikan JSON valid dengan status 200
     */
    public function test_api_categories_returns_json_with_status_200(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'name', 'slug', 'questions_count']
            ]
        ]);
    }

    /**
     * Test: API /api/quiz/{id} mengembalikan soal dalam format JSON
     */
    public function test_api_quiz_returns_questions_for_valid_category(): void
    {
        $response = $this->getJson("/api/quiz/{$this->category->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'category' => ['id', 'name', 'slug'],
            'total_available',
            'questions'
        ]);
    }

    /**
     * Test: API /api/quiz/{id} dengan ID tidak valid mengembalikan 404
     */
    public function test_api_quiz_returns_404_for_invalid_category(): void
    {
        $response = $this->getJson('/api/quiz/99999');

        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    }

    /**
     * Test: API /api/leaderboard mengembalikan data skor
     */
    public function test_api_leaderboard_returns_json(): void
    {
        $response = $this->getJson('/api/leaderboard');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure([
            'success',
            'data'
        ]);
    }

    // =========================================================================
    // TEST 3: SECURITY (Akses tanpa otorisasi)
    // =========================================================================

    /**
     * Test: Akses halaman admin dashboard tanpa login harus redirect ke login
     */
    public function test_admin_dashboard_redirects_unauthenticated_user(): void
    {
        $response = $this->get('/admin');

        // Admin redirect ke halaman login admin
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test: Akses halaman Live Duel tanpa login harus redirect
     */
    public function test_live_lobby_requires_authentication(): void
    {
        $response = $this->get('/live');

        $response->assertRedirect('/login');
    }

    /**
     * Test: Akses halaman Social Hub tanpa login harus redirect
     */
    public function test_social_hub_requires_authentication(): void
    {
        $response = $this->get('/social');

        $response->assertRedirect('/login');
    }

    /**
     * Test: Akses profil edit tanpa login harus redirect
     */
    public function test_profile_edit_requires_authentication(): void
    {
        $response = $this->get('/profile/edit/me');

        $response->assertRedirect('/login');
    }

    /**
     * Test: Mengirim challenge tanpa login harus redirect
     */
    public function test_send_challenge_requires_authentication(): void
    {
        $response = $this->post('/live/challenge/send', [
            'target_id' => 1,
            'category_id' => 1,
            'total_questions' => 10,
            'duration' => 30
        ]);

        $response->assertRedirect('/login');
    }

    // =========================================================================
    // TEST BONUS: INPUT VALIDATION
    // =========================================================================

    /**
     * Test: Submit quiz dengan data tidak valid harus ditolak
     */
    public function test_quiz_submit_rejects_invalid_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/quiz/submit', [
            'category_id' => 'invalid',
            'player_name' => '',
            'answers' => 'bukan array'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['category_id', 'player_name', 'answers']);
    }

    /**
     * Test: Create room dengan duration melebihi batas ditolak
     */
    public function test_create_room_rejects_invalid_duration(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/live/create', [
            'category_id' => $this->category->id,
            'total_questions' => 10,
            'duration' => 999
        ]);

        $response->assertSessionHasErrors(['duration']);
    }
}
