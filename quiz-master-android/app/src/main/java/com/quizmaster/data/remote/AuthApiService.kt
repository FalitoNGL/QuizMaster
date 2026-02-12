package com.quizmaster.data.remote

import com.quizmaster.data.model.AuthResponse
import com.quizmaster.data.model.LoginRequest
import com.quizmaster.data.model.RegisterRequest
import com.quizmaster.data.model.User
import retrofit2.Response
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.Header
import retrofit2.http.POST

interface AuthApiService {
    @POST("login")
    suspend fun login(@Body request: LoginRequest): Response<AuthResponse>

    @POST("register")
    suspend fun register(@Body request: RegisterRequest): Response<AuthResponse>

    @POST("logout")
    suspend fun logout(@Header("Authorization") token: String): Response<Void>

    @GET("user")
    suspend fun getUser(@Header("Authorization") token: String): Response<User>
}
