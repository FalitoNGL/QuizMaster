package com.quizmaster.data.repository

import com.quizmaster.data.local.SessionManager
import com.quizmaster.data.model.RankingItem
import com.quizmaster.data.model.UserStats
import com.quizmaster.data.remote.LeaderboardApiService
import kotlinx.coroutines.flow.firstOrNull

class LeaderboardRepository(
    private val api: LeaderboardApiService,
    private val sessionManager: SessionManager
) {

    /**
     * Fetch top rankings from the server
     */
    suspend fun getLeaderboard(limit: Int = 20): Result<List<RankingItem>> {
        return try {
            val response = api.getLeaderboard(limit)
            if (response.isSuccessful && response.body() != null) {
                Result.success(response.body()!!.data)
            } else {
                Result.failure(Exception("Failed to fetch leaderboard: ${response.message()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    /**
     * Fetch statistics for the authenticated user
     */
    suspend fun getUserStats(): Result<UserStats> {
        return try {
            val token = sessionManager.authToken.firstOrNull()
            if (token != null) {
                val response = api.getUserStats("Bearer $token")
                if (response.isSuccessful && response.body() != null) {
                    Result.success(response.body()!!.data)
                } else {
                    Result.failure(Exception("Failed to fetch stats: ${response.message()}"))
                }
            } else {
                Result.failure(Exception("Unauthorized: No token found"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }
}
