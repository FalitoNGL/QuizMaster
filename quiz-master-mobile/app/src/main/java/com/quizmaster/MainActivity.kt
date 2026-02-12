package com.quizmaster

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Surface
import androidx.compose.ui.Modifier
import com.quizmaster.ui.navigation.QuizNavHost
import com.quizmaster.ui.theme.QuizMasterTheme

import androidx.lifecycle.viewmodel.compose.viewModel
import com.quizmaster.data.api.RetrofitClient
import com.quizmaster.data.local.SessionManager
import com.quizmaster.data.repository.AuthRepository
import com.quizmaster.ui.viewmodel.AuthViewModel
import com.quizmaster.ui.viewmodel.AuthViewModelFactory

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val sessionManager = SessionManager(applicationContext)
        val authRepository = AuthRepository(RetrofitClient.authApiService, sessionManager)
        val leaderboardRepository = com.quizmaster.data.repository.LeaderboardRepository(RetrofitClient.leaderboardApiService, sessionManager)

        setContent {
            val authViewModel: com.quizmaster.ui.viewmodel.AuthViewModel = viewModel(
                factory = com.quizmaster.ui.viewmodel.AuthViewModelFactory(authRepository)
            )
            
            val leaderboardViewModel: com.quizmaster.ui.viewmodel.LeaderboardViewModel = viewModel(
                factory = com.quizmaster.ui.viewmodel.LeaderboardViewModelFactory(leaderboardRepository)
            )

            QuizMasterTheme {
                Surface(
                    modifier = Modifier.fillMaxSize(),
                    color = MaterialTheme.colorScheme.background
                ) {
                    QuizNavHost(
                        authViewModel = authViewModel,
                        leaderboardViewModel = leaderboardViewModel
                    )
                }
            }
        }
    }
}
