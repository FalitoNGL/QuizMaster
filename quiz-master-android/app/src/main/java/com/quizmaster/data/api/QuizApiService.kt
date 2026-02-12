package com.quizmaster.data.api

import com.quizmaster.data.model.*
import retrofit2.Response
import retrofit2.http.*

/**
 * Retrofit API Service Interface
 * Base URL: http://YOUR_LARAVEL_SERVER/api/
 */
interface QuizApiService {

    /**
     * GET /api/categories
     * Mengambil daftar semua kategori kuis
     */
    @GET("categories")
    suspend fun getCategories(): Response<ApiResponse<List<Category>>>

    /**
     * GET /api/quiz/{id}
     * Mengambil soal berdasarkan kategori ID
     * @param id ID kategori
     * @param limit Jumlah soal (default 10)
     */
    @GET("quiz/{id}")
    suspend fun getQuiz(
        @Path("id") id: Int,
        @Query("limit") limit: Int = 10
    ): Response<QuizResponse>

    /**
     * POST /api/quiz/submit
     * Mengirim jawaban dan mendapatkan skor
     */
    @POST("quiz/submit")
    suspend fun submitQuiz(
        @Body request: SubmitRequest
    ): Response<SubmitResponse>

    /**
     * GET /api/leaderboard
     * Mengambil papan peringkat
     */
    @GET("leaderboard")
    suspend fun getLeaderboard(
        @Query("limit") limit: Int = 20
    ): Response<ApiResponse<List<LeaderboardEntry>>>
}
