package com.quizmaster.data.remote

import com.quizmaster.data.model.LeaderboardResponse
import com.quizmaster.data.model.StatsResponse
import retrofit2.Response
import retrofit2.http.GET
import retrofit2.http.Header
import retrofit2.http.Query

interface LeaderboardApiService {
    
    @GET("leaderboard")
    suspend fun getLeaderboard(
        @Query("limit") limit: Int = 20
    ): Response<LeaderboardResponse>

    @GET("stats")
    suspend fun getUserStats(
        @Header("Authorization") token: String
    ): Response<StatsResponse>
}
