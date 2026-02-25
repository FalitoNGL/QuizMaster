package com.quizmaster.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.itemsIndexed
import androidx.compose.foundation.Image
import androidx.compose.ui.res.painterResource
import com.quizmaster.R


import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Leaderboard
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material3.*
import androidx.compose.material3.pulltorefresh.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.AsyncImage
import androidx.compose.animation.core.*
import androidx.compose.foundation.border
import androidx.compose.material.icons.filled.*
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.graphicsLayer
import androidx.compose.ui.graphics.vector.ImageVector
import com.quizmaster.data.model.Category
import com.quizmaster.ui.components.GlassyCard
import com.quizmaster.ui.components.PremiumLoadingScreen
import com.quizmaster.ui.components.ShimmerItem
import com.quizmaster.ui.theme.*
import com.quizmaster.ui.viewmodel.AuthViewModel
import com.quizmaster.ui.viewmodel.QuizViewModel
import kotlinx.coroutines.delay

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun HomeScreen(
    viewModel: QuizViewModel,
    authViewModel: AuthViewModel,
    onCategoryClick: (Category) -> Unit,
    onLeaderboardClick: () -> Unit,
    onStatsClick: () -> Unit,
    leaderboardViewModel: com.quizmaster.ui.viewmodel.LeaderboardViewModel // Added
) {
    val categories = viewModel.categories.value
    val isLoading = viewModel.isLoading.value
    val error = viewModel.error.value
    val userProfile = authViewModel.user.collectAsState().value
    val userStats by leaderboardViewModel.userStats.collectAsState()

    val pullRefreshState = rememberPullToRefreshState()
    var showSetupDialog by remember { mutableStateOf(false) }
    var selectedCategoryForSetup by remember { mutableStateOf<Category?>(null) }

    LaunchedEffect(Unit) {
        viewModel.loadCategories()
        leaderboardViewModel.fetchUserStats() // Load real stats
    }

    if (showSetupDialog && selectedCategoryForSetup != null) {
        QuizSetupDialog(
            category = selectedCategoryForSetup!!,
            onDismiss = { showSetupDialog = false },
            onConfirm = { limit, timer ->
                showSetupDialog = false
                viewModel.loadQuiz(selectedCategoryForSetup!!, limit, timer)
                onCategoryClick(selectedCategoryForSetup!!)
            }
        )
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
    ) {
        PullToRefreshBox(
            modifier = Modifier.fillMaxSize(),
            state = pullRefreshState,
            isRefreshing = isLoading,
            onRefresh = { viewModel.loadCategories() },
            indicator = {
                PullToRefreshDefaults.Indicator(
                    state = pullRefreshState,
                    isRefreshing = isLoading,
                    modifier = Modifier.align(Alignment.TopCenter),
                    color = LaravelBlue,
                    containerColor = Color.Transparent
                )
            }
        ) {
            // Full screen loader for initial load when no categories exist
            if (isLoading && categories.isEmpty()) {
                PremiumLoadingScreen(message = "Syncing Categories...")
            }
            LazyVerticalGrid(
                columns = GridCells.Fixed(2),
                modifier = Modifier
                    .fillMaxSize()
                    .padding(horizontal = 24.dp),
                horizontalArrangement = Arrangement.spacedBy(16.dp),
                verticalArrangement = Arrangement.spacedBy(16.dp),
                contentPadding = PaddingValues(bottom = 100.dp)
            ) {
                // Header (Logo)
                item(span = { androidx.compose.foundation.lazy.grid.GridItemSpan(2) }) {
                    Row(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(top = 24.dp, bottom = 4.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Image(
                            painter = painterResource(id = R.drawable.ic_logo_quantum),
                            contentDescription = "QuizMaster Logo",
                            modifier = Modifier.size(32.dp)
                        )
                        Spacer(modifier = Modifier.width(8.dp))
                        Text(
                            text = "QuizMaster",
                            color = Color.White.copy(alpha = 0.9f),
                            fontSize = 20.sp,
                            fontWeight = FontWeight.Bold,
                            letterSpacing = 1.sp
                        )
                    }
                }

                // Hero Section
                item(span = { androidx.compose.foundation.lazy.grid.GridItemSpan(2) }) {
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(top = 24.dp, bottom = 20.dp),
                        contentAlignment = Alignment.Center
                    ) {
                        Column(horizontalAlignment = Alignment.CenterHorizontally) {
                            // Avatar
                            Box(
                                modifier = Modifier
                                    .size(80.dp)
                                    .clip(CircleShape)
                                    .background(Brush.linearGradient(listOf(LaravelBlue, LaravelPurple)))
                                    .border(3.dp, Color.White.copy(alpha = 0.5f), CircleShape)
                                    .graphicsLayer {
                                        shadowElevation = 20f
                                        shape = CircleShape
                                        clip = true
                                    },
                                contentAlignment = Alignment.Center
                            ) {
                                Text(
                                    text = userProfile?.name?.firstOrNull()?.toString() ?: "F",
                                    color = Color.White,
                                    fontWeight = FontWeight.Black,
                                    fontSize = 32.sp
                                )
                                // Online Indicator
                                Box(
                                    modifier = Modifier
                                        .size(16.dp)
                                        .align(Alignment.BottomEnd)
                                        .offset(x = (-4).dp, y = (-4).dp)
                                        .clip(CircleShape)
                                        .background(Color(0xFF22c55e))
                                        .border(2.dp, Color(0xFF0d1117), CircleShape)
                                )
                            }

                            Spacer(modifier = Modifier.height(16.dp))

                            Text(
                                text = "Halo,",
                                color = Color.White.copy(alpha = 0.6f),
                                fontSize = 14.sp,
                                fontWeight = FontWeight.Medium
                            )
                            Text(
                                text = userProfile?.name ?: "Falito Eriano Nainggolan",
                                color = Color.White,
                                fontSize = 22.sp,
                                fontWeight = FontWeight.Black,
                                textAlign = androidx.compose.ui.text.style.TextAlign.Center,
                                lineHeight = 28.sp
                            )
                            Text(
                                text = "Siap untuk menguji pengetahuanmu hari ini?",
                                color = Color.White.copy(alpha = 0.5f),
                                fontSize = 12.sp,
                                modifier = Modifier.padding(top = 4.dp)
                            )

                            // Quick Stats
                            Row(
                                modifier = Modifier
                                    .padding(top = 20.dp)
                                    .fillMaxWidth(),
                                horizontalArrangement = Arrangement.Center,
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                QuickStatItem("Total Skor", String.format("%,d", userStats?.totalScore ?: 0), LaravelBlue)
                                Spacer(modifier = Modifier.width(16.dp))
                                QuickStatItem("Level", (userStats?.level ?: 1).toString(), Color(0xFFeab308))
                                Spacer(modifier = Modifier.width(16.dp))
                                QuickStatItem("Akurasi", "${(userStats?.accuracy ?: 0.0).toInt()}%", Color(0xFF22c55e))
                            }
                        }
                    }
                }

                // Section Title
                item(span = { androidx.compose.foundation.lazy.grid.GridItemSpan(2) }) {
                    Text(
                        text = "Pick Your Challenge",
                        color = Color.White,
                        fontSize = 20.sp,
                        fontWeight = FontWeight.ExtraBold,
                        modifier = Modifier.padding(top = 12.dp, bottom = 8.dp)
                    )
                }

                when {
                    isLoading -> {
                        items(6) { ShimmerItem(modifier = Modifier.height(180.dp), shape = RoundedCornerShape(28.dp)) }
                    }
                    error != null -> {
                        item(span = { androidx.compose.foundation.lazy.grid.GridItemSpan(2) }) {
                            Box(modifier = Modifier.fillMaxWidth().height(200.dp), contentAlignment = Alignment.Center) {
                                Text(text = "Error: $error", color = Color.Red)
                            }
                        }
                    }
                    else -> {
                        itemsIndexed(categories) { index, category ->
                            // Staggered Animation State
                            val alphaAnim = remember { Animatable(0f) }
                            val slideAnim = remember { Animatable(50f) }
                            
                            LaunchedEffect(Unit) {
                                delay(index.toLong() * 80L) // Staggered delay
                                alphaAnim.animateTo(1f, animationSpec = tween(400))
                                slideAnim.animateTo(0f, animationSpec = spring(dampingRatio = Spring.DampingRatioLowBouncy, stiffness = Spring.StiffnessLow))
                            }

                            Box(
                                modifier = Modifier.graphicsLayer {
                                    alpha = alphaAnim.value
                                    translationY = slideAnim.value
                                }
                            ) {
                                HomeScreenCategoryCard(
                                    category = category,
                                    onClick = { 
                                        selectedCategoryForSetup = category
                                        showSetupDialog = true
                                    }
                                )
                            }
                        }
                    }
                }
            }
        }
    }
}

@Composable
fun QuizSetupDialog(
    category: Category,
    onDismiss: () -> Unit,
    onConfirm: (Int, Int) -> Unit
) {
    val maxQuestions = if (category.questionsCount > 0) category.questionsCount.toFloat() else 10f
    var limit by androidx.compose.runtime.remember { androidx.compose.runtime.mutableStateOf(if (maxQuestions < 10f) maxQuestions else 10f) }
    var timer by androidx.compose.runtime.remember { androidx.compose.runtime.mutableStateOf(30f) }

    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = Color(0xFF1E293B),
        title = {
            Text(
                text = "Quiz Settings: ${category.name}",
                color = Color.White,
                fontWeight = FontWeight.Bold,
                fontSize = 20.sp
            )
        },
        text = {
            Column {
                Text(
                    text = "Number of Questions: ${limit.toInt()}",
                    color = Color.White.copy(alpha = 0.8f),
                    fontSize = 14.sp
                )
                Slider(
                    value = limit,
                    onValueChange = { limit = it },
                    valueRange = 1f..maxQuestions,
                    steps = if (maxQuestions > 1f) (maxQuestions - 2).toInt().coerceAtLeast(0) else 0,
                    colors = SliderDefaults.colors(
                        thumbColor = LaravelBlue,
                        activeTrackColor = LaravelBlue
                    )
                )
                
                Spacer(modifier = Modifier.height(16.dp))
                
                Text(
                    text = "Time Limit: ${timer.toInt()} seconds",
                    color = Color.White.copy(alpha = 0.8f),
                    fontSize = 14.sp
                )
                Slider(
                    value = timer,
                    onValueChange = { timer = it },
                    valueRange = 10f..120f,
                    steps = 11,
                    colors = SliderDefaults.colors(
                        thumbColor = LaravelBlue,
                        activeTrackColor = LaravelBlue
                    )
                )
            }
        },
        confirmButton = {
            Button(
                onClick = { onConfirm(limit.toInt(), timer.toInt()) },
                colors = ButtonDefaults.buttonColors(containerColor = LaravelBlue),
                shape = RoundedCornerShape(12.dp)
            ) {
                Text("Start Quiz", fontWeight = FontWeight.Bold)
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) {
                Text("Cancel", color = Color.White.copy(alpha = 0.5f))
            }
        }
    )
}

@Composable
fun HomeScreenCategoryCard(
    category: Category,
    onClick: () -> Unit
) {
    val theme = getCategoryTheme(category.name)

    Box(
        modifier = Modifier
            .fillMaxWidth()
            .height(180.dp)
            .graphicsLayer {
                clip = true
                shape = RoundedCornerShape(32.dp)
            }
            .clickable(onClick = onClick)
    ) {
        // Base Glassy Background with Theme-colored Gradient
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(
                    Brush.verticalGradient(
                        colors = listOf(
                            Color(0xFF1e293b).copy(alpha = 0.8f),
                            Color(0xFF0f172a).copy(alpha = 0.95f)
                        )
                    )
                )
                .border(
                    width = 1.dp,
                    brush = Brush.linearGradient(
                        colors = listOf(
                            Color.White.copy(alpha = 0.15f),
                            theme.color.copy(alpha = 0.4f),
                            Color.Transparent
                        )
                    ),
                    shape = RoundedCornerShape(32.dp)
                )
        )

        // Accent Glow Texture
        Box(
            modifier = Modifier
                .fillMaxSize()
                .graphicsLayer { alpha = 0.25f }
                .background(
                    Brush.radialGradient(
                        colors = listOf(theme.color, Color.Transparent),
                        center = androidx.compose.ui.geometry.Offset(300f, 0f),
                        radius = 400f
                    )
                )
        )

        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(20.dp),
            verticalArrangement = Arrangement.SpaceBetween
        ) {
            // Header: Icon and Floating Stats
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Box(
                    modifier = Modifier
                        .size(54.dp)
                        .clip(RoundedCornerShape(18.dp))
                        .background(
                            Brush.linearGradient(
                                colors = listOf(theme.color, theme.color.copy(alpha = 0.6f))
                            )
                        )
                        .border(1.dp, Color.White.copy(alpha = 0.3f), RoundedCornerShape(18.dp)),
                    contentAlignment = Alignment.Center
                ) {
                    Icon(
                        imageVector = theme.icon,
                        contentDescription = null,
                        tint = Color.White,
                        modifier = Modifier.size(26.dp)
                    )
                }

                // Questions Count Badge (Glassy Style)
                Surface(
                    color = Color.White.copy(alpha = 0.05f),
                    shape = RoundedCornerShape(12.dp),
                    border = androidx.compose.foundation.BorderStroke(0.5.dp, Color.White.copy(alpha = 0.1f))
                ) {
                    Text(
                        text = "${category.questionsCount} Qs",
                        color = Color.White.copy(alpha = 0.7f),
                        fontSize = 13.sp, // Increased from 11sp
                        fontWeight = FontWeight.Bold,
                        modifier = Modifier.padding(horizontal = 10.dp, vertical = 5.dp)
                    )
                }
            }

            // Body: Title and Animated Action
            Column {
                val isLongTitle = category.name.length > 18
                Text(
                    text = category.name,
                    color = Color.White,
                    fontSize = if (isLongTitle) 15.sp else 18.sp,
                    fontWeight = FontWeight.ExtraBold,
                    lineHeight = if (isLongTitle) 19.sp else 22.sp,
                    maxLines = 2,
                    modifier = Modifier.fillMaxWidth()
                )
                
                Spacer(modifier = Modifier.height(if (isLongTitle) 12.dp else 10.dp))
                
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .clip(RoundedCornerShape(14.dp))
                        .background(Color.White.copy(alpha = 0.03f))
                        .padding(horizontal = 12.dp, vertical = 8.dp),
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.SpaceBetween
                ) {
                    Text(
                        text = "LETS PLAY",
                        color = theme.color,
                        fontSize = 13.sp, // Increased from 10sp
                        fontWeight = FontWeight.Black,
                        letterSpacing = 1.5.sp
                    )
                    Icon(
                        Icons.Default.ArrowForward,
                        contentDescription = null,
                        tint = theme.color,
                        modifier = Modifier.size(14.dp)
                    )
                }
            }
        }
    }
}

@Composable
fun QuickStatItem(label: String, value: String, color: Color) {
    Column(
        modifier = Modifier
            .clip(RoundedCornerShape(12.dp))
            .background(Color.White.copy(alpha = 0.05f))
            .padding(vertical = 8.dp, horizontal = 12.dp),
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        Text(text = value, color = color, fontWeight = FontWeight.Bold, fontSize = 18.sp) // Increased from 16sp
        Text(text = label, color = Color(0xFF64748b), fontSize = 12.sp) // Increased from 10sp
    }
}

// --- Helper for Dynamic Theming ---
data class CategoryTheme(val color: Color, val icon: ImageVector)

fun getCategoryTheme(name: String): CategoryTheme {
    return when {
        name.contains("Keamanan", ignoreCase = true) || name.contains("Security", ignoreCase = true) -> CategoryTheme(CatSecurity, Icons.Default.Security)
        name.contains("Biologi", ignoreCase = true) || name.contains("Bio", ignoreCase = true) -> CategoryTheme(CatBio, Icons.Default.Eco)
        name.contains("Elektronika", ignoreCase = true) || name.contains("Electro", ignoreCase = true) -> CategoryTheme(CatElectro, Icons.Default.Bolt)
        name.contains("Sandi", ignoreCase = true) || name.contains("Code", ignoreCase = true) || name.contains("Pemrograman", ignoreCase = true) -> CategoryTheme(CatCode, Icons.Default.Code)
        name.contains("Intelijen", ignoreCase = true) || name.contains("Brain", ignoreCase = true) -> CategoryTheme(CatIntel, Icons.Default.Psychology)
        name.contains("Jaringan", ignoreCase = true) || name.contains("Network", ignoreCase = true) -> CategoryTheme(CatGeneral, Icons.Default.Hub)
        else -> CategoryTheme(CatGeneral, Icons.Default.School) // Fallback
    }
}
