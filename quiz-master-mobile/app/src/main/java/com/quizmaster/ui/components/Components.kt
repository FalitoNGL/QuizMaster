package com.quizmaster.ui.components

import androidx.compose.animation.core.*
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.interaction.MutableInteractionSource
import androidx.compose.foundation.interaction.collectIsPressedAsState
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Home
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.draw.drawWithContent
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.graphicsLayer
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.Dp
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material.icons.filled.Info
import com.quizmaster.ui.theme.*

/**
 * Advanced Glassmorphism Card with:
 * - Inner shadow (simulated with border)
 * - Multi-layered gradient border
 * - Drop shadow
 */
@Composable
fun GlassyCard(
    modifier: Modifier = Modifier,
    shape: RoundedCornerShape = RoundedCornerShape(24.dp),
    containerColor: Color? = null,
    borderColor: Color? = null,
    borderWidth: Dp = 1.dp,
    contentPadding: Dp = 16.dp,
    content: @Composable () -> Unit
) {
    Box(
        modifier = modifier
            .shadow(
                elevation = 16.dp,
                shape = shape,
                ambientColor = Color.Black.copy(alpha = 0.5f),
                spotColor = Color.Black.copy(alpha = 0.5f)
            )
            .clip(shape)
            .background(
                if (containerColor != null) {
                    Brush.linearGradient(
                        colors = listOf(containerColor, containerColor.copy(alpha = 0.5f))
                    )
                } else {
                    Brush.verticalGradient(
                        colors = listOf(
                            GlassCardTop,
                            GlassCardBottom
                        )
                    )
                }
            )
            .border(
                width = borderWidth,
                brush = if (borderColor != null) {
                    Brush.linearGradient(colors = listOf(borderColor, borderColor.copy(alpha = 0.5f)))
                } else {
                    Brush.linearGradient(
                        colors = listOf(
                            GlassBorder,
                            Color.Transparent,
                            GlassBorder.copy(alpha = 0.3f)
                        )
                    )
                },
                shape = shape
            )
            .padding(2.dp)
    ) {
        Surface(
            color = Color.Transparent,
            modifier = Modifier.padding(contentPadding)
        ) {
            content()
        }
    }
}

/**
 * Premium Interactive Button with:
 * - Scale animation on press
 * - Mesh-style gradient
 * - Glowing shadow
 */
@Composable
fun PremiumButton(
    text: String,
    onClick: () -> Unit,
    modifier: Modifier = Modifier,
    enabled: Boolean = true
) {
    val interactionSource = remember { MutableInteractionSource() }
    val isPressed by interactionSource.collectIsPressedAsState()
    
    val scale by animateFloatAsState(
        targetValue = if (isPressed) 0.96f else 1f,
        label = "scale"
    )
    
    val buttonBrush = Brush.linearGradient(
        colors = listOf(LaravelBlue, LaravelPurple)
    )

    Surface(
        onClick = onClick,
        modifier = modifier
            .graphicsLayer {
                scaleX = scale
                scaleY = scale
            }
            .shadow(
                elevation = if (isPressed) 4.dp else 12.dp,
                shape = RoundedCornerShape(16.dp),
                spotColor = LaravelBlue.copy(alpha = 0.6f)
            ),
        shape = RoundedCornerShape(16.dp),
        color = Color.Transparent,
        enabled = enabled,
        interactionSource = interactionSource
    ) {
        Box(
            modifier = Modifier
                .background(buttonBrush)
                .padding(vertical = 12.dp, horizontal = 24.dp),
            contentAlignment = Alignment.Center
        ) {
            Text(
                text = text.uppercase(),
                color = Color.White,
                fontSize = 15.sp,
                fontWeight = FontWeight.ExtraBold,
                letterSpacing = 1.sp
            )
        }
    }
}

/**
 * Shimmer Effect for Loading States
 */
@Composable
fun ShimmerItem(
    modifier: Modifier = Modifier,
    shape: RoundedCornerShape = RoundedCornerShape(16.dp)
) {
    val transition = rememberInfiniteTransition(label = "shimmer")
    val translateAnim by transition.animateFloat(
        initialValue = 0f,
        targetValue = 1000f,
        animationSpec = infiniteRepeatable(
            animation = tween(1200, easing = LinearEasing),
            repeatMode = RepeatMode.Restart
        ),
        label = "shimmer_offset"
    )

    val shimmerColors = listOf(
        Color.White.copy(alpha = 0.05f),
        Color.White.copy(alpha = 0.15f),
        Color.White.copy(alpha = 0.05f),
    )

    val brush = Brush.linearGradient(
        colors = shimmerColors,
        start = Offset(10f, 10f),
        end = Offset(translateAnim, translateAnim)
    )

    Box(
        modifier = modifier
            .clip(shape)
            .background(brush)
    )
}

/**
 * Premium Bottom Navigation Bar with Glassmorphism
 */
@Composable
fun PremiumBottomNavBar(
    navController: androidx.navigation.NavController,
    items: List<com.quizmaster.ui.navigation.Screen>
) {
    val navBackStackEntry by navController.currentBackStackEntryAsState()
    val currentRoute = navBackStackEntry?.destination?.route

    androidx.compose.material3.NavigationBar(
        containerColor = LaravelSlate950.copy(alpha = 0.8f),
        modifier = Modifier
            .fillMaxWidth()
            .height(90.dp)
            .clip(RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp))
            .border(
                1.dp,
                Brush.verticalGradient(
                    colors = listOf(Color.White.copy(alpha = 0.1f), Color.Transparent)
                ),
                RoundedCornerShape(topStart = 32.dp, topEnd = 32.dp)
            ),
        tonalElevation = 0.dp
    ) {
        items.forEach { screen ->
            val isSelected = currentRoute == screen.route
            
            NavigationBarItem(
                selected = isSelected,
                onClick = {
                    if (currentRoute != screen.route) {
                        navController.navigate(screen.route) {
                            popUpTo(com.quizmaster.ui.navigation.Screen.Home.route) {
                                saveState = true
                            }
                            launchSingleTop = true
                            restoreState = true
                        }
                    }
                },
                icon = {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Icon(
                            imageVector = screen.icon ?: androidx.compose.material.icons.Icons.Default.Home,
                            contentDescription = screen.route,
                            tint = if (isSelected) LaravelBlue else Color.White.copy(alpha = 0.4f),
                            modifier = Modifier.size(if (isSelected) 28.dp else 24.dp)
                        )
                        if (isSelected) {
                            Box(
                                modifier = Modifier
                                    .padding(top = 4.dp)
                                    .size(4.dp)
                                    .clip(CircleShape)
                                    .background(LaravelBlue)
                            )
                        }
                    }
                },
                label = {
                    val label = when(screen.route) {
                        "achievements" -> "Medals"
                        "leaderboard" -> "Rank"
                        else -> screen.route.replaceFirstChar { it.uppercase() }
                    }
                    Text(
                        text = label,
                        color = if (isSelected) Color.White else Color.White.copy(alpha = 0.4f),
                        fontWeight = if (isSelected) FontWeight.ExtraBold else FontWeight.Medium,
                        fontSize = 13.sp, // Increased from 11sp
                        maxLines = 1
                    )
                },
                colors = NavigationBarItemDefaults.colors(
                    indicatorColor = Color.Transparent
                )
            )
        }
    }
}

/**
 * Mesh-style Background with vibrant radial glows
 */
@Composable
fun MeshBackground(
    modifier: Modifier = Modifier,
    content: @Composable () -> Unit
) {
    Box(
        modifier = modifier
            .fillMaxSize()
            .background(BackgroundGradient)
    ) {
        // Atmospheric Mesh Glow 1 (Top Left)
        Box(
            modifier = Modifier
                .offset(x = (-200).dp, y = (-200).dp)
                .size(800.dp)
                .background(
                    Brush.radialGradient(
                        colors = listOf(MeshBlue, Color.Transparent)
                    )
                )
        )

        // Atmospheric Mesh Glow 2 (Bottom Right)
        Box(
            modifier = Modifier
                .align(Alignment.BottomEnd)
                .offset(x = 200.dp, y = 200.dp)
                .size(800.dp)
                .background(
                    Brush.radialGradient(
                        colors = listOf(MeshPurple, Color.Transparent)
                    )
                )
        )

        // Atmospheric Mesh Glow 3 (Center Left - Re-balanced)
        Box(
            modifier = Modifier
                .align(Alignment.CenterStart)
                .offset(x = (-150).dp)
                .size(600.dp)
                .background(
                    Brush.radialGradient(
                        colors = listOf(MeshPurple.copy(alpha = 0.1f), Color.Transparent)
                    )
                )
        )

        content()
    }
}

/**
 * Segmented Progress Bar for Quiz
 */
@Composable
fun SegmentedProgressBar(
    modifier: Modifier = Modifier,
    count: Int,
    currentIndex: Int,
    getStatus: (Int) -> Int // 1: Correct, -1: Wrong, 0: Pending
) {
    Row(
        modifier = modifier.fillMaxWidth(),
        horizontalArrangement = Arrangement.spacedBy(4.dp)
    ) {
        for (i in 0 until count) {
            val status = getStatus(i)
            val color = when {
                i == currentIndex -> Color(0xFFFBBF24) // Yellow
                status == 1 -> Color(0xFF10B981)   // Green
                status == -1 -> Color(0xFFEF4444)  // Red
                else -> Color.White.copy(alpha = 0.1f) // Gray
            }
            Box(
                modifier = Modifier
                    .weight(1f)
                    .height(8.dp)
                    .clip(RoundedCornerShape(2.dp))
                    .background(color)
            )
        }
    }
}
