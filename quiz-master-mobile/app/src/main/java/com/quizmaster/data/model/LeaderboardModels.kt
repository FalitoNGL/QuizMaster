package com.quizmaster.data.model

import com.google.gson.annotations.SerializedName

/**
 * Data model for Leaderboard ranking item
 */
data class RankingItem(
    val id: Int,
    @SerializedName("player_name") val playerName: String,
    @SerializedName("player_avatar") val playerAvatar: String?,
    @SerializedName("category_id") val categoryId: Int,
    val score: Int,
    @SerializedName("correct_answers") val correctAnswers: Int,
    @SerializedName("total_questions") val totalQuestions: Int,
    @SerializedName("created_at") val createdAt: String,
    val category: Category? = null
)

/**
 * API Response for Leaderboard
 */
data class LeaderboardResponse(
    val success: Boolean,
    val data: List<RankingItem>
)

/**
 * Data model for User Statistics per category
 */
data class CategoryStats(
    @SerializedName("category_name") val categoryName: String,
    val played: Int,
    @SerializedName("total_score") val totalScore: Int,
    val accuracy: Double
)

/**
 * Data model for comprehensive User Statistics
 */
data class UserStats(
    @SerializedName("player_name") val playerName: String,
    val avatar: String?,
    val bio: String?,
    val title: String?,
    @SerializedName("joined_at") val joinedAt: String?,
    val level: Int,
    @SerializedName("total_score") val totalScore: Int,
    @SerializedName("total_games") val totalGames: Int,
    val accuracy: Double,
    @SerializedName("progress_to_next_level") val progressToNextLevel: Double,
    @SerializedName("category_stats") val categoryStats: List<CategoryStats>
)

/**
 * API Response for Stats
 */
data class StatsResponse(
    val success: Boolean,
    val data: UserStats
)
