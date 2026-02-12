package com.quizmaster.data.model

import com.google.gson.annotations.SerializedName

/**
 * Wrapper untuk response API standar
 */
data class ApiResponse<T>(
    @SerializedName("success") val success: Boolean,
    @SerializedName("message") val message: String? = null,
    @SerializedName("data") val data: T? = null
)

/**
 * Model Kategori Kuis
 */
data class Category(
    @SerializedName("id") val id: Int,
    @SerializedName("name") val name: String,
    @SerializedName("slug") val slug: String,
    @SerializedName("description") val description: String? = null,
    @SerializedName("questions_count") val questionsCount: Int = 0
)

/**
 * Model Soal Kuis
 */
data class Question(
    @SerializedName("id") val id: Int,
    @SerializedName("question_text") val questionText: String,
    @SerializedName("type") val type: String, // "single", "multiple", "ordering"
    @SerializedName("explanation") val explanation: String? = null,
    @SerializedName("reference") val reference: String? = null,
    @SerializedName("options") val options: List<Option>
)

data class Option(
    @SerializedName("id") val id: Int,
    @SerializedName("option_text") val optionText: String,
    @SerializedName("is_correct") val isCorrect: Int = 0 // 1 = Correct, 0 = Wrong
)

/**
 * Response dari endpoint /api/quiz/{id}
 */
data class QuizResponse(
    @SerializedName("success") val success: Boolean,
    @SerializedName("category") val category: Category,
    @SerializedName("total_available") val totalAvailable: Int,
    @SerializedName("questions") val questions: List<Question>
)

/**
 * Request Body untuk submit jawaban
 */
data class SubmitRequest(
    @SerializedName("player_name") val playerName: String,
    @SerializedName("category_id") val categoryId: Int,
    @SerializedName("answers") val answers: List<AnswerItem>
)

data class AnswerItem(
    @SerializedName("question_id") val questionId: Int,
    @SerializedName("answer") val answer: Any?, // Int untuk single, List<Int> untuk multiple
    @SerializedName("time_left") val timeLeft: Int
)

/**
 * Response dari submit
 */
data class SubmitResponse(
    @SerializedName("success") val success: Boolean,
    @SerializedName("result_id") val resultId: Int,
    @SerializedName("player_name") val playerName: String,
    @SerializedName("score") val score: Int,
    @SerializedName("correct") val correct: Int,
    @SerializedName("total") val total: Int,
    @SerializedName("accuracy") val accuracy: Double
)

/**
 * Model Leaderboard Entry
 */
data class LeaderboardEntry(
    @SerializedName("id") val id: Int,
    @SerializedName("player_name") val playerName: String,
    @SerializedName("score") val score: Int,
    @SerializedName("correct_answers") val correctAnswers: Int,
    @SerializedName("total_questions") val totalQuestions: Int,
    @SerializedName("category") val category: Category?
)
