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
    onStatsClick: () -> Unit
) {
    val categories = viewModel.categories.value
    val isLoading = viewModel.isLoading.value
    val error = viewModel.error.value
    val userProfile = authViewModel.user.collectAsState().value

    val pullRefreshState = rememberPullToRefreshState()
    var showSetupDialog by remember { mutableStateOf(false) }
    var selectedCategoryForSetup by remember { mutableStateOf<Category?>(null) }

    LaunchedEffect(Unit) {
        viewModel.loadCategories()
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
                    containerColor = Color.White.copy(alpha = 0.1f)
                )
            }
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(horizontal = 24.dp)
            ) {
                // Brand Header (Added per user request)
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

                // Header with user profile (Game Changer Upgrade)
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(top = 16.dp, bottom = 20.dp),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Column {
                        Row(verticalAlignment = Alignment.CenterVertically) {
                            Text(
                                text = "Hi, ${userProfile?.name?.split(" ")?.firstOrNull() ?: "Master"}",
                                color = Color.White,
                                fontSize = 28.sp,
                                fontWeight = FontWeight.Black,
                                letterSpacing = (-1).sp
                            )
                            Spacer(modifier = Modifier.width(8.dp))
                            Text(text = "🔥", fontSize = 24.sp) // Streak Fire
                        }
                        Text(
                            text = "Let's crush some quizzes today! 🚀",
                            color = Color.White.copy(alpha = 0.7f),
                            fontSize = 14.sp,
                            fontWeight = FontWeight.Medium
                        )
                    }
                    
                    // Profile Avatar Placeholder
                    Box(
                        modifier = Modifier
                            .size(48.dp)
                            .clip(CircleShape)
                            .background(Brush.linearGradient(listOf(LaravelBlue, LaravelPurple)))
                            .border(2.dp, Color.White.copy(alpha = 0.2f), CircleShape),
                        contentAlignment = Alignment.Center
                    ) {
                        Text(
                            text = userProfile?.name?.firstOrNull()?.toString() ?: "Q",
                            color = Color.White,
                            fontWeight = FontWeight.Bold,
                            fontSize = 20.sp
                        )
                    }
                }

                Spacer(modifier = Modifier.height(32.dp))

                // Categories Section
                Text(
                    text = "Pick Your Challenge",
                    color = Color.White,
                    fontSize = 20.sp,
                    fontWeight = FontWeight.ExtraBold,
                    modifier = Modifier.padding(bottom = 20.dp)
                )

                when {
                    isLoading -> {
                        LazyVerticalGrid(
                            columns = GridCells.Fixed(2),
                            horizontalArrangement = Arrangement.spacedBy(16.dp),
                            verticalArrangement = Arrangement.spacedBy(16.dp)
                        ) {
                            items(6) { ShimmerItem(modifier = Modifier.height(180.dp), shape = RoundedCornerShape(28.dp)) }
                        }
                    }
                    error != null -> {
                        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                            Text(text = "Error: $error", color = Color.Red)
                        }
                    }
                    else -> {
                        LazyVerticalGrid(
                            columns = GridCells.Fixed(2),
                            horizontalArrangement = Arrangement.spacedBy(16.dp),
                            verticalArrangement = Arrangement.spacedBy(16.dp),
                            contentPadding = PaddingValues(bottom = 100.dp) // Extra padding for bottom nav
                        ) {
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

    GlassyCard(
        modifier = Modifier
            .fillMaxWidth()
            .height(200.dp) // Tall, sleek cards
            .clickable(onClick = onClick),
        shape = RoundedCornerShape(32.dp),
        containerColor = theme.color.copy(alpha = 0.05f), // Subtle tint
        borderColor = theme.color.copy(alpha = 0.3f)
    ) {
        Column(
            modifier = Modifier.fillMaxSize(),
            verticalArrangement = Arrangement.SpaceBetween,
            horizontalAlignment = Alignment.Start
        ) {
            // Icon Bubble
            Box(
                modifier = Modifier
                    .size(52.dp)
                    .clip(CircleShape)
                    .background(theme.color.copy(alpha = 0.15f))
                    .border(1.dp, theme.color.copy(alpha = 0.3f), CircleShape),
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = theme.icon,
                    contentDescription = null,
                    tint = theme.color,
                    modifier = Modifier.size(26.dp)
                )
            }
            
            Column {
                Text(
                    text = category.name,
                    color = Color.White,
                    fontSize = 18.sp,
                    fontWeight = FontWeight.Black,
                    lineHeight = 22.sp,
                    maxLines = 2,
                    letterSpacing = (-0.5).sp
                )
                
                Spacer(modifier = Modifier.height(6.dp))
                
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Box(
                        modifier = Modifier
                            .size(6.dp)
                            .clip(CircleShape)
                            .background(theme.color)
                    )
                    Spacer(modifier = Modifier.width(6.dp))
                    Text(
                        text = "${category.questionsCount} Questions",
                        color = Color.White.copy(alpha = 0.5f),
                        fontSize = 12.sp,
                        fontWeight = FontWeight.Bold
                    )
                }
            }
        }
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
