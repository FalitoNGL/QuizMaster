package com.quizmaster.ui.theme

import androidx.compose.ui.graphics.Color

val Purple80 = Color(0xFFD0BCFF)
val PurpleGrey80 = Color(0xFFCCC2DC)
val Pink80 = Color(0xFFEFB8C8)

val Purple40 = Color(0xFF6650a4)
val PurpleGrey40 = Color(0xFF625b71)
val Pink40 = Color(0xFF7D5260)

// Premium Colors (Aligned with Laravel Web)
val LaravelBlue = Color(0xFF2563EB) // Blue-600
val LaravelPurple = Color(0xFF9333EA) // Purple-600
val LaravelSlate900 = Color(0xFF0F172A)
val LaravelSlate950 = Color(0xFF020617)

// Glassmorphism System
val GlassWhite = Color(0xFFFFFFFF).copy(alpha = 0.10f) // Reduced for delicacy
val GlassBorder = Color(0xFFFFFFFF).copy(alpha = 0.12f) // Whisper-thin border
val GlassCardTop = Color(0xFFFFFFFF).copy(alpha = 0.15f)
val GlassCardBottom = Color(0xFFFFFFFF).copy(alpha = 0.05f)

// Mesh Background Glows
val MeshBlue = Color(0xFF2563EB).copy(alpha = 0.20f) // Softer, more atmospheric
val MeshPurple = Color(0xFF9333EA).copy(alpha = 0.20f) // Softer, more atmospheric
val GlassBlack = Color(0x80020617) // 50% Slate-950

// Semantic Colors
val SuccessGreen = Color(0xFF22C55E) // Green-500
val ErrorRed = Color(0xFFEF4444) // Red-500
val WarningYellow = Color(0xFFF59E0B) // Amber-500

// Category Theme Colors (Game Changer)
val CatSecurity = Color(0xFF10B981) // Emerald-500
val CatBio = Color(0xFFEC4899) // Pink-500
val CatElectro = Color(0xFFF59E0B) // Amber-500
val CatCode = Color(0xFF3B82F6) // Blue-500
val CatIntel = Color(0xFF8B5CF6) // Violet-500
val CatGeneral = Color(0xFF06B6D4) // Cyan-500

// Shared Brushes
val MeshGradient = androidx.compose.ui.graphics.Brush.linearGradient(
    colors = listOf(LaravelBlue, LaravelPurple)
)

val BackgroundGradient = androidx.compose.ui.graphics.Brush.verticalGradient(
    colors = listOf(LaravelSlate900, LaravelSlate950)
)
