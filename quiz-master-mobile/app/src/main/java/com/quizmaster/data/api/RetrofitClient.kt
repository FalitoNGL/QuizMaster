package com.quizmaster.data.api

import okhttp3.OkHttpClient
import okhttp3.Interceptor
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit
import com.quizmaster.data.remote.AuthApiService
import com.quizmaster.data.remote.LeaderboardApiService

/**
 * Retrofit Client Singleton
 * Ganti BASE_URL dengan alamat server Laravel Anda
 */
object RetrofitClient {

    // GANTI INI dengan IP server Laravel Anda
    // Untuk emulator Android: 10.0.2.2 (localhost emulator)
    // Untuk device fisik: IP komputer Anda (contoh: 192.168.1.100)
    private const val BASE_URL = "http://10.0.2.2:8000/api/"

    private val loggingInterceptor = HttpLoggingInterceptor().apply {
        level = HttpLoggingInterceptor.Level.BODY
    }

    private val okHttpClient = OkHttpClient.Builder()
        .addInterceptor { chain ->
            val request = chain.request().newBuilder()
                .addHeader("Accept", "application/json")
                .build()
            chain.proceed(request)
        }
        .addInterceptor(loggingInterceptor)
        .connectTimeout(30, TimeUnit.SECONDS)
        .readTimeout(30, TimeUnit.SECONDS)
        .writeTimeout(30, TimeUnit.SECONDS)
        .build()

    private val retrofit: Retrofit = Retrofit.Builder()
        .baseUrl(BASE_URL)
        .client(okHttpClient)
        .addConverterFactory(GsonConverterFactory.create())
        .build()

    val apiService: QuizApiService = retrofit.create(QuizApiService::class.java)
    val authApiService: AuthApiService = retrofit.create(AuthApiService::class.java)
    val leaderboardApiService: LeaderboardApiService = retrofit.create(LeaderboardApiService::class.java)
}
