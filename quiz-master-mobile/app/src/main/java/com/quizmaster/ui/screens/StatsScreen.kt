package com.quizmaster.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.quizmaster.ui.components.GlassyCard
import com.quizmaster.ui.components.ShimmerItem
import com.quizmaster.ui.theme.*
import com.quizmaster.ui.viewmodel.LeaderboardViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun StatsScreen(
    viewModel: LeaderboardViewModel,
    onBackClick: () -> Unit
) {
    val stats by viewModel.userStats.collectAsState()
    val isLoading by viewModel.isLoading.collectAsState()

    LaunchedEffect(Unit) {
        viewModel.fetchUserStats()
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
    ) {
        Column(modifier = Modifier.fillMaxSize()) {
            CenterAlignedTopAppBar(
                title = {
                    Text(
                        "Your Insights",
                        color = Color.White,
                        fontWeight = FontWeight.ExtraBold,
                        fontSize = 20.sp
                    )
                },
                navigationIcon = {
                    IconButton(onClick = onBackClick) {
                        Icon(
                            Icons.Default.ArrowBack,
                            contentDescription = "Back",
                            tint = Color.White
                        )
                    }
                },
                colors = TopAppBarDefaults.centerAlignedTopAppBarColors(
                    containerColor = Color.Transparent
                )
            )

            if (isLoading && stats == null) {
                Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator(color = Color.White)
                }
            } else if (stats != null) {
                LazyColumn(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(horizontal = 24.dp),
                    verticalArrangement = Arrangement.spacedBy(20.dp),
                    contentPadding = PaddingValues(bottom = 40.dp)
                ) {
                    item {
                        PremiumLevelCard(stats!!)
                    }

                    item {
                        Row(modifier = Modifier.fillMaxWidth()) {
                            StatsGridItem(
                                title = "Total IQ",
                                value = "${stats!!.totalScore}",
                                icon = Icons.Default.ElectricBolt,
                                color = Color(0xFFFFEB3B),
                                modifier = Modifier.weight(1f)
                            )
                            Spacer(modifier = Modifier.width(16.dp))
                            StatsGridItem(
                                title = "Accuracy",
                                value = "${stats!!.accuracy.toInt()}%",
                                icon = Icons.Default.Timeline,
                                color = SuccessGreen,
                                modifier = Modifier.weight(1f)
                            )
                        }
                    }

                    item {
                        Text(
                            text = "Performance Breakdown",
                            color = Color.White,
                            fontSize = 18.sp,
                            fontWeight = FontWeight.ExtraBold,
                            modifier = Modifier.padding(top = 8.dp)
                        )
                    }

                    items(stats!!.categoryStats) { category ->
                        CategoryStatPremiumRow(category)
                    }
                }
            }
        }
    }
}

@Composable
fun PremiumLevelCard(stats: com.quizmaster.data.model.UserStats) {
    GlassyCard(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(32.dp)
    ) {
        Column(modifier = Modifier.padding(8.dp)) {
            Row(
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.SpaceBetween,
                modifier = Modifier.fillMaxWidth()
            ) {
                Column {
                    Text(
                        "Current Rank",
                        color = Color.White.copy(alpha = 0.5f),
                        fontSize = 12.sp,
                        fontWeight = FontWeight.Bold
                    )
                    Text(
                        "Elite Specialist", // Dynamic rank name would be better later
                        color = Color.White,
                        fontWeight = FontWeight.ExtraBold,
                        fontSize = 22.sp
                    )
                }
                
                Box(
                    modifier = Modifier
                        .size(60.dp)
                        .clip(CircleShape)
                        .background(MeshGradient),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        "Lvl ${stats.level}",
                        color = Color.White,
                        fontWeight = FontWeight.Black,
                        fontSize = 18.sp
                    )
                }
            }
            
            Spacer(modifier = Modifier.height(24.dp))
            
            Column {
                Row(
                    horizontalArrangement = Arrangement.SpaceBetween,
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Text(
                        "EXP Progress",
                        color = Color.White.copy(alpha = 0.5f),
                        fontSize = 12.sp,
                        fontWeight = FontWeight.Bold
                    )
                    Text(
                        "${stats.progressToNextLevel.toInt()}%",
                        color = Color.White,
                        fontSize = 12.sp,
                        fontWeight = FontWeight.Black
                    )
                }
                Spacer(modifier = Modifier.height(8.dp))
                LinearProgressIndicator(
                    progress = stats.progressToNextLevel.toFloat() / 100f,
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(10.dp)
                        .clip(CircleShape),
                    color = LaravelBlue,
                    trackColor = Color.White.copy(alpha = 0.1f)
                )
            }
        }
    }
}

@Composable
fun StatsGridItem(title: String, value: String, icon: ImageVector, color: Color, modifier: Modifier = Modifier) {
    GlassyCard(
        modifier = modifier,
        shape = RoundedCornerShape(24.dp)
    ) {
        Column(
            modifier = Modifier.padding(4.dp),
            horizontalAlignment = Alignment.Start
        ) {
            Icon(
                icon, 
                contentDescription = null, 
                tint = color,
                modifier = Modifier.size(28.dp)
            )
            Spacer(modifier = Modifier.height(12.dp))
            Text(
                value, 
                color = Color.White, 
                fontWeight = FontWeight.Black, 
                fontSize = 24.sp,
                letterSpacing = (-0.5).sp
            )
            Text(
                title, 
                color = Color.White.copy(alpha = 0.4f), 
                fontSize = 12.sp,
                fontWeight = FontWeight.Bold
            )
        }
    }
}

@Composable
fun CategoryStatPremiumRow(stat: com.quizmaster.data.model.CategoryStats) {
    val progress = (stat.accuracy / 100).toFloat()
    
    GlassyCard(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(24.dp)
    ) {
        Row(
            modifier = Modifier.fillMaxWidth(),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Text(
                    stat.categoryName, 
                    color = Color.White, 
                    fontWeight = FontWeight.ExtraBold,
                    fontSize = 16.sp
                )
                Text(
                    "${stat.played} Attempts", 
                    color = Color.White.copy(alpha = 0.4f), 
                    fontSize = 12.sp,
                    fontWeight = FontWeight.Bold
                )
            }
            
            Spacer(modifier = Modifier.width(16.dp))
            
            Box(contentAlignment = Alignment.Center) {
                CircularProgressIndicator(
                    progress = progress,
                    modifier = Modifier.size(48.dp),
                    color = if (progress >= 0.7f) SuccessGreen else if (progress >= 0.4f) WarningYellow else ErrorRed,
                    strokeWidth = 4.dp,
                    trackColor = Color.White.copy(alpha = 0.1f)
                )
                Text(
                    "${stat.accuracy.toInt()}%",
                    color = Color.White,
                    fontSize = 10.sp,
                    fontWeight = FontWeight.Black
                )
            }
        }
    }
}
