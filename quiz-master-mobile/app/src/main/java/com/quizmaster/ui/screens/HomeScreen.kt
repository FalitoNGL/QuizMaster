package com.quizmaster.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
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
import com.quizmaster.data.model.Category
import com.quizmaster.ui.components.GlassyCard
import com.quizmaster.ui.components.ShimmerItem
import com.quizmaster.ui.theme.BackgroundGradient
import com.quizmaster.ui.theme.LaravelBlue
import com.quizmaster.ui.viewmodel.AuthViewModel
import com.quizmaster.ui.viewmodel.QuizViewModel

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
                // Header with user profile
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(top = 32.dp, bottom = 12.dp),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Column {
                        Text(
                            text = "Hi, ${userProfile?.name ?: "Quiz Master"}",
                            color = Color.White,
                            fontSize = 24.sp,
                            fontWeight = FontWeight.ExtraBold,
                            letterSpacing = (-0.8).sp
                        )
                        Text(
                            text = "Ready to boost your score?",
                            color = Color.White.copy(alpha = 0.6f),
                            fontSize = 14.sp,
                            fontWeight = FontWeight.Medium
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
                            contentPadding = PaddingValues(bottom = 32.dp)
                        ) {
                            items(categories) { category ->
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
    GlassyCard(
        modifier = Modifier
            .fillMaxWidth()
            .height(180.dp) // Uniform Height
            .clickable(onClick = onClick),
        shape = RoundedCornerShape(28.dp)
    ) {
        Column(
            modifier = Modifier.fillMaxSize(),
            verticalArrangement = Arrangement.SpaceBetween,
            horizontalAlignment = Alignment.Start
        ) {
            Box(
                modifier = Modifier
                    .size(44.dp)
                    .clip(RoundedCornerShape(12.dp))
                    .background(LaravelBlue.copy(alpha = 0.1f)),
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    imageVector = Icons.Default.PlayArrow,
                    contentDescription = null,
                    tint = LaravelBlue,
                    modifier = Modifier.size(24.dp)
                )
            }
            
            Column {
                Text(
                    text = category.name,
                    color = Color.White,
                    fontSize = 17.sp,
                    fontWeight = FontWeight.ExtraBold,
                    lineHeight = 20.sp,
                    maxLines = 2
                )
                
                Spacer(modifier = Modifier.height(4.dp))
                
                Text(
                    text = "${category.questionsCount} Questions",
                    color = Color.White.copy(alpha = 0.4f),
                    fontSize = 12.sp,
                    fontWeight = FontWeight.SemiBold
                )
            }
        }
    }
}
