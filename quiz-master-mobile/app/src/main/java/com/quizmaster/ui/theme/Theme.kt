package com.quizmaster.ui.theme

import android.app.Activity
import android.os.Build
import android.view.WindowManager
import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.SideEffect
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.toArgb
import androidx.compose.ui.platform.LocalView
import androidx.core.view.WindowCompat

// Define the Premium Color Scheme (Dark/Glassmorphism Aligned with Web)
private val PremiumColorScheme = darkColorScheme(
    primary = LaravelBlue,
    secondary = LaravelPurple,
    tertiary = LaravelPurple,
    background = LaravelSlate950,
    surface = LaravelSlate900,
    onPrimary = Color.White,
    onSecondary = Color.White,
    onTertiary = Color.White,
    onBackground = Color.White,
    onSurface = Color.White,
)

@Composable
fun QuizMasterTheme(
    darkTheme: Boolean = isSystemInDarkTheme(),
    // Dynamic color is available on Android 12+
    dynamicColor: Boolean = false, // DISABLED: We want to enforce our Premium Brand
    content: @Composable () -> Unit
) {
    // We enforce the Premium Dark Scheme regardless of system setting
    // to match the Web App's premium look.
    val colorScheme = PremiumColorScheme

    val view = LocalView.current
    if (!view.isInEditMode) {
        SideEffect {
            val window = (view.context as Activity).window
            window.statusBarColor = colorScheme.background.toArgb()
            
            // Allow content to draw behind status bar (transparent)
            // WindowCompat.setDecorFitsSystemWindows(window, false) // This might require handling insets in screens
            
            WindowCompat.getInsetsController(window, view).isAppearanceLightStatusBars = false // Always light text
        }
    }

    MaterialTheme(
        colorScheme = colorScheme,
        typography = Typography, // Uses the val Typography from Type.kt
        content = content
    )
}


