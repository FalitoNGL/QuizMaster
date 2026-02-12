package com.quizmaster.data.repository

import com.quizmaster.data.local.SessionManager
import com.quizmaster.data.model.AuthResponse
import com.quizmaster.data.model.LoginRequest
import com.quizmaster.data.model.RegisterRequest
import com.quizmaster.data.model.User
import com.quizmaster.data.remote.AuthApiService
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.firstOrNull
import retrofit2.Response

class AuthRepository(
    private val api: AuthApiService,
    private val sessionManager: SessionManager
) {

    val authToken: Flow<String?> = sessionManager.authToken
    val userName: Flow<String?> = sessionManager.userName

    suspend fun login(email: String, password: String): Result<User> {
        return try {
            val response = api.login(LoginRequest(email, password))
            if (response.isSuccessful && response.body() != null) {
                val authResponse = response.body()!!
                if (authResponse.accessToken != null) {
                    sessionManager.saveAuthToken(authResponse.accessToken)
                    val user = authResponse.user ?: User(0, "User", email, "adventurer", "Novice")
                    sessionManager.saveUser(user.name, user.email, user.avatar)
                    Result.success(user)
                } else {
                    Result.failure(Exception("No access token received"))
                }
            } else {
                Result.failure(Exception("Login failed: ${response.message()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun register(name: String, email: String, password: String): Result<User> {
        return try {
            val response = api.register(RegisterRequest(name, email, password, password))
            if (response.isSuccessful && response.body() != null) {
                val authResponse = response.body()!!
                if (authResponse.accessToken != null) {
                    sessionManager.saveAuthToken(authResponse.accessToken)
                    val user = authResponse.user ?: User(0, name, email, "adventurer", "Novice")
                    sessionManager.saveUser(user.name, user.email, user.avatar)
                    Result.success(user)
                } else {
                    Result.failure(Exception("No access token received"))
                }
            } else {
                Result.failure(Exception("Registration failed: ${response.errorBody()?.string()}"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun getUserProfile(): Result<User> {
        return try {
            val token = sessionManager.authToken.firstOrNull()
            if (token != null) {
                val response = api.getUser("Bearer $token")
                if (response.isSuccessful && response.body() != null) {
                    val user = response.body()!!
                    sessionManager.saveUser(user.name, user.email, user.avatar)
                    Result.success(user)
                } else {
                    Result.failure(Exception("Failed to fetch profile: ${response.message()}"))
                }
            } else {
                Result.failure(Exception("No auth token found"))
            }
        } catch (e: Exception) {
            Result.failure(e)
        }
    }

    suspend fun logout() {
        try {
            sessionManager.clearSession()
        } catch (e: Exception) {
            sessionManager.clearSession()
        }
    }
}
