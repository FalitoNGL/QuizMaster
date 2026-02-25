package com.quizmaster.ui.navigation

import androidx.compose.runtime.Composable
import androidx.lifecycle.viewmodel.compose.viewModel
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.EmojiEvents
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.Leaderboard
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.Settings
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.graphics.Color
import androidx.navigation.NavHostController
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.navigation.compose.rememberNavController
import com.quizmaster.ui.components.PremiumBottomNavBar
import androidx.compose.material3.Scaffold
import androidx.compose.runtime.getValue
import androidx.compose.ui.Modifier
import androidx.compose.foundation.layout.padding
import com.quizmaster.ui.screens.HomeScreen
import com.quizmaster.ui.components.MeshBackground
import com.quizmaster.ui.screens.LoginScreen
import com.quizmaster.ui.screens.QuizScreen
import com.quizmaster.ui.screens.RegisterScreen
import com.quizmaster.ui.screens.ResultScreen
import com.quizmaster.ui.screens.SplashScreen
import com.quizmaster.ui.viewmodel.AuthViewModel
import com.quizmaster.ui.viewmodel.LeaderboardViewModel
import com.quizmaster.ui.viewmodel.QuizViewModel
import com.quizmaster.ui.screens.LeaderboardScreen
import com.quizmaster.ui.screens.StatsScreen

/**
 * Definisi routes aplikasi
 */
sealed class Screen(val route: String, val icon: ImageVector? = null) {
    object Login : Screen("login")
    object Register : Screen("register")
    object Splash : Screen("splash")
    object Home : Screen("home", Icons.Default.Home)
    object Quiz : Screen("quiz")
    object Result : Screen("result")
    object Achievements : Screen("achievements", Icons.Default.EmojiEvents)
    object Leaderboard : Screen("leaderboard", Icons.Default.Leaderboard)
    object Stats : Screen("stats", Icons.Default.Person)
    object Settings : Screen("settings", Icons.Default.Settings)
}

/**
 * Navigation Host utama
 */
@Composable
fun QuizNavHost(
    navController: NavHostController = rememberNavController(),
    viewModel: QuizViewModel = viewModel(),
    authViewModel: AuthViewModel,
    leaderboardViewModel: LeaderboardViewModel
) {
    val navBackStackEntry by navController.currentBackStackEntryAsState()
    val currentRoute = navBackStackEntry?.destination?.route

    // Screens that should show the Bottom Navigation Bar
    val navBarScreens = listOf(
        Screen.Home.route,
        Screen.Achievements.route,
        Screen.Leaderboard.route,
        Screen.Stats.route,
        Screen.Settings.route
    )

    MeshBackground {
        Scaffold(
            containerColor = Color.Transparent,
            bottomBar = {
                if (currentRoute in navBarScreens) {
                    PremiumBottomNavBar(
                        navController = navController,
                        items = listOf(Screen.Home, Screen.Achievements, Screen.Leaderboard, Screen.Stats, Screen.Settings)
                    )
                }
            }
        ) { innerPadding ->
        NavHost(
            navController = navController,
            startDestination = Screen.Splash.route,
            modifier = Modifier.padding(innerPadding)
        ) {
            composable(Screen.Splash.route) {
                SplashScreen(navController = navController, authViewModel = authViewModel)
            }

            // Auth Screens
            composable(Screen.Login.route) {
                LoginScreen(navController = navController, viewModel = authViewModel)
            }
            
            composable(Screen.Register.route) {
                RegisterScreen(navController = navController, viewModel = authViewModel)
            }

            // Home Screen - Daftar Kategori
            composable(Screen.Home.route) {
                HomeScreen(
                    viewModel = viewModel,
                    authViewModel = authViewModel,
                    onCategoryClick = { category ->
                        navController.navigate(Screen.Quiz.route)
                    },
                    onLeaderboardClick = { navController.navigate(Screen.Leaderboard.route) },
                    onStatsClick = { navController.navigate(Screen.Stats.route) },
                    leaderboardViewModel = leaderboardViewModel
                )
            }

            // Quiz Screen - Gameplay
            composable(Screen.Quiz.route) {
                QuizScreen(
                    viewModel = viewModel,
                    onQuizComplete = {
                        navController.navigate(Screen.Result.route) {
                            popUpTo(Screen.Home.route)
                        }
                    }
                )
            }

            // Result Screen - Hasil Akhir
            composable(Screen.Result.route) {
                ResultScreen(
                    viewModel = viewModel,
                    onPlayAgain = {
                        viewModel.currentCategory.value?.let { category ->
                            viewModel.loadQuiz(category)
                            navController.navigate(Screen.Quiz.route) {
                                popUpTo(Screen.Home.route)
                            }
                        }
                    },
                    onGoHome = {
                        viewModel.resetQuiz()
                        navController.navigate(Screen.Home.route) {
                            popUpTo(Screen.Home.route) { inclusive = true }
                        }
                    }
                )
            }

            // Leaderboard Screen
            composable(Screen.Leaderboard.route) {
                LeaderboardScreen(
                    viewModel = leaderboardViewModel,
                    onBackClick = { navController.popBackStack() }
                )
            }

            // Achievements Screen
            composable(Screen.Achievements.route) {
                com.quizmaster.ui.screens.AchievementsScreen(
                    onBackClick = { navController.popBackStack() }
                )
            }

            // Stats Screen
            composable(Screen.Stats.route) {
                StatsScreen(
                    viewModel = leaderboardViewModel,
                    authViewModel = authViewModel,
                    onLogout = {
                        navController.navigate(Screen.Login.route) {
                            popUpTo(0) { inclusive = true }
                        }
                    },
                    onBackClick = { navController.popBackStack() }
                )
            }

            // Settings Screen
            composable(Screen.Settings.route) {
                com.quizmaster.ui.screens.SettingsScreen(
                    authViewModel = authViewModel,
                    onLogout = {
                        navController.navigate(Screen.Login.route) {
                            popUpTo(0) { inclusive = true }
                        }
                    },
                    onBackClick = { navController.popBackStack() }
                )
            }
        }
        }
    }
}
