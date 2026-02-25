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
import androidx.compose.foundation.border
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.graphicsLayer
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.AsyncImage
import com.quizmaster.ui.components.GlassyCard
import com.quizmaster.ui.theme.*
import com.quizmaster.ui.viewmodel.LeaderboardViewModel
import com.quizmaster.ui.viewmodel.AuthViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun StatsScreen(
    viewModel: LeaderboardViewModel,
    authViewModel: AuthViewModel,
    onLogout: () -> Unit,
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
            if (isLoading && stats == null) {
                Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator(color = Color.White)
                }
            } else {
                LazyColumn(
                    modifier = Modifier
                        .fillMaxSize(),
                    verticalArrangement = Arrangement.spacedBy(12.dp),
                    contentPadding = PaddingValues(top = 0.dp, bottom = 100.dp)
                ) {
                    // Profile Header (Cover & Avatar)
                    item {
                        Box(
                            modifier = Modifier
                                .fillMaxWidth()
                                .height(260.dp)
                        ) {
                            AsyncImage(
                                model = "https://images.unsplash.com/photo-1614850523296-d8c1af93d400?q=80&w=1000&auto=format&fit=crop",
                                contentDescription = null,
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .height(200.dp),
                                contentScale = ContentScale.Crop
                            )
                            Box(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .height(200.dp)
                                    .background(
                                        Brush.verticalGradient(
                                            colors = listOf(Color.Black.copy(alpha = 0.4f), Color.Transparent, LaravelSlate950)
                                        )
                                    )
                            )
                            

                            Box(
                                modifier = Modifier
                                    .align(Alignment.BottomCenter)
                                    .size(110.dp)
                                    .clip(CircleShape)
                                    .background(LaravelSlate950)
                                    .padding(4.dp)
                            ) {
                                Box(
                                    modifier = Modifier
                                        .fillMaxSize()
                                        .clip(CircleShape)
                                        .background(Brush.linearGradient(listOf(LaravelBlue, LaravelPurple)))
                                        .border(3.dp, Color.White.copy(alpha = 0.5f), CircleShape)
                                        .graphicsLayer {
                                            shadowElevation = 30f
                                            shape = CircleShape
                                            clip = true
                                        },
                                    contentAlignment = Alignment.Center
                                ) {
                                    Text(
                                        text = stats?.playerName?.firstOrNull()?.toString()?.uppercase() ?: "F",
                                        color = Color.White,
                                        fontWeight = FontWeight.Black,
                                        fontSize = 42.sp
                                    )
                                }
                            }
                        }
                    }

                    // User Info
                    item {
                        Column(
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(horizontal = 24.dp),
                            horizontalAlignment = Alignment.CenterHorizontally
                        ) {
                            Text(
                                text = stats?.playerName?.uppercase() ?: "FALITO ERIANO NAINGGOLAN",
                                color = Color.White,
                                fontSize = 24.sp,
                                fontWeight = FontWeight.Black,
                                letterSpacing = 0.5.sp
                            )
                            Text(
                                text = stats?.bio?.uppercase() ?: "QUIZ MASTER PLAYER",
                                color = LaravelBlue,
                                fontSize = 14.sp,
                                fontWeight = FontWeight.ExtraBold,
                                letterSpacing = 1.sp
                            )
                            Text(
                                text = "Bergabung sejak ${stats?.joinedAt ?: "Feb 2026"}",
                                color = Color.White.copy(alpha = 0.5f),
                                fontSize = 13.sp,
                                fontWeight = FontWeight.Medium,
                                modifier = Modifier.padding(top = 6.dp)
                            )
                        }
                    }

                    if (stats != null) {
                        item {
                            Box(modifier = Modifier.padding(horizontal = 24.dp)) {
                                PremiumLevelCard(stats!!)
                            }
                        }

                        item {
                            Row(modifier = Modifier.fillMaxWidth().padding(horizontal = 24.dp)) {
                                StatsGridItem(
                                    title = "Total Skor",
                                    value = String.format("%,d", stats!!.totalScore),
                                    icon = Icons.Default.Analytics,
                                    color = Color(0xFF6366f1),
                                    modifier = Modifier.weight(1f)
                                )
                                Spacer(modifier = Modifier.width(12.dp))
                                StatsGridItem(
                                    title = "Akurasi",
                                    value = "${stats!!.accuracy.toInt()}%",
                                    icon = Icons.Default.GpsFixed,
                                    color = Color(0xFF10b981),
                                    modifier = Modifier.weight(1f)
                                )
                            }
                        }

                        item {
                            Text(
                                text = "Koleksi Trophy",
                                color = Color.White,
                                fontSize = 16.sp,
                                fontWeight = FontWeight.Bold,
                                modifier = Modifier.padding(horizontal = 24.dp).padding(top = 4.dp)
                            )
                        }

                        item {
                            Row(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .height(110.dp)
                                    .padding(horizontal = 24.dp),
                                horizontalArrangement = Arrangement.spacedBy(12.dp)
                            ) {
                                repeat(3) { index ->
                                    Box(modifier = Modifier.weight(1f)) {
                                        TrophyPlaceholder(index)
                                    }
                                }
                            }
                        }

                        item {
                            Text(
                                text = "Statistik Kategori",
                                color = Color.White,
                                fontSize = 16.sp,
                                fontWeight = FontWeight.Bold,
                                modifier = Modifier.padding(horizontal = 24.dp).padding(top = 4.dp)
                            )
                        }

                        items(stats!!.categoryStats) { category ->
                            Box(modifier = Modifier.padding(horizontal = 24.dp)) {
                                CategoryStatPremiumRow(category)
                            }
                        }
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
        Column(modifier = Modifier.padding(16.dp)) {
            Row(
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.SpaceBetween,
                modifier = Modifier.fillMaxWidth()
            ) {
                Column {
                    Text(
                        "CURRENT RANK",
                        color = Color.White.copy(alpha = 0.6f),
                        fontSize = 13.sp,
                        fontWeight = FontWeight.Black,
                        letterSpacing = 1.sp
                    )
                    Text(
                        text = stats.title?.uppercase() ?: "ELITE SPECIALIST",
                        color = Color.White,
                        fontWeight = FontWeight.Black,
                        fontSize = 26.sp,
                        letterSpacing = (-0.5).sp
                    )
                }
                
                Box(
                    modifier = Modifier
                        .size(70.dp)
                        .clip(CircleShape)
                        .background(
                            Brush.linearGradient(
                                colors = listOf(LaravelBlue, LaravelPurple)
                            )
                        )
                        .border(2.dp, Color.White.copy(alpha = 0.3f), CircleShape),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        "LVL ${stats.level}",
                        color = Color.White,
                        fontWeight = FontWeight.Black,
                        fontSize = 20.sp
                    )
                }
            }
            
            Spacer(modifier = Modifier.height(20.dp))
            
            Column {
                Row(
                    horizontalArrangement = Arrangement.SpaceBetween,
                    modifier = Modifier.fillMaxWidth(),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text(
                        "EXP PROGRESS",
                        color = Color.White.copy(alpha = 0.6f),
                        fontSize = 13.sp,
                        fontWeight = FontWeight.Black,
                        letterSpacing = 0.5.sp
                    )
                    Text(
                        "${stats.progressToNextLevel.toInt()}%",
                        color = LaravelBlue,
                        fontSize = 16.sp,
                        fontWeight = FontWeight.Black
                    )
                }
                Spacer(modifier = Modifier.height(10.dp))
                LinearProgressIndicator(
                    progress = stats.progressToNextLevel.toFloat() / 100f,
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(14.dp)
                        .clip(CircleShape)
                        .border(1.dp, Color.White.copy(alpha = 0.05f), CircleShape),
                    color = LaravelBlue,
                    trackColor = Color.White.copy(alpha = 0.08f)
                )
            }
        }
    }
}

@Composable
fun StatsGridItem(title: String, value: String, icon: ImageVector, color: Color, modifier: Modifier = Modifier) {
    Box(
        modifier = modifier
            .height(150.dp)
            .clip(RoundedCornerShape(32.dp))
    ) {
        // Premium Glassy Background
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(
                    Brush.verticalGradient(
                        colors = listOf(
                            Color(0xFF1e293b).copy(alpha = 0.85f),
                            Color(0xFF0f172a).copy(alpha = 0.98f)
                        )
                    )
                )
                .border(
                    width = 1.dp,
                    brush = Brush.linearGradient(
                        colors = listOf(
                            Color.White.copy(alpha = 0.15f),
                            color.copy(alpha = 0.3f),
                            Color.Transparent
                        )
                    ),
                    shape = RoundedCornerShape(32.dp)
                )
        )

        // Accent Glow
        Box(
            modifier = Modifier
                .fillMaxSize()
                .graphicsLayer { alpha = 0.15f }
                .background(
                    Brush.radialGradient(
                        colors = listOf(color, Color.Transparent),
                        center = androidx.compose.ui.geometry.Offset(100f, 0f),
                        radius = 250f
                    )
                )
        )

        Column(
            modifier = Modifier.fillMaxSize().padding(16.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            // Icon Container
            Box(
                modifier = Modifier
                    .size(42.dp)
                    .background(color.copy(alpha = 0.15f), CircleShape)
                    .border(1.dp, color.copy(alpha = 0.2f), CircleShape),
                contentAlignment = Alignment.Center
            ) {
                Icon(
                    icon, 
                    contentDescription = null, 
                    tint = color,
                    modifier = Modifier.size(22.dp)
                )
            }
            
            Spacer(modifier = Modifier.height(12.dp))
            
            Column(horizontalAlignment = Alignment.CenterHorizontally) {
                Text(
                    value, 
                    color = Color.White, 
                    fontWeight = FontWeight.Black, 
                    fontSize = 28.sp,
                    letterSpacing = (-1).sp,
                    textAlign = TextAlign.Center
                )
                Text(
                    title.uppercase(), 
                    color = Color.White.copy(alpha = 0.5f), 
                    fontSize = 12.sp,
                    fontWeight = FontWeight.Black,
                    letterSpacing = 0.5.sp,
                    textAlign = TextAlign.Center
                )
            }
        }
    }
}

@Composable
fun CategoryStatPremiumRow(stat: com.quizmaster.data.model.CategoryStats) {
    val progress = (stat.accuracy / 100).toFloat()
    
    GlassyCard(
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(24.dp),
        containerColor = Color(0xFF1a2035).copy(alpha = 0.6f)
    ) {
        Row(
            modifier = Modifier.fillMaxWidth().padding(20.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Text(
                    stat.categoryName.uppercase(), 
                    color = Color.White, 
                    fontWeight = FontWeight.Black,
                    fontSize = 16.sp,
                    letterSpacing = 0.5.sp
                )
                Text(
                    "${stat.played} ATTEMPTS", 
                    color = Color.White.copy(alpha = 0.5f), 
                    fontSize = 13.sp,
                    fontWeight = FontWeight.ExtraBold,
                    letterSpacing = 0.5.sp
                )
            }
            
            Spacer(modifier = Modifier.width(16.dp))
            
            Box(contentAlignment = Alignment.Center) {
                CircularProgressIndicator(
                    progress = progress,
                    modifier = Modifier.size(54.dp),
                    color = if (progress >= 0.7f) SuccessGreen else if (progress >= 0.4f) WarningYellow else ErrorRed,
                    strokeWidth = 5.dp,
                    trackColor = Color.White.copy(alpha = 0.1f)
                )
                Text(
                    "${stat.accuracy.toInt()}%",
                    color = Color.White,
                    fontSize = 12.sp,
                    fontWeight = FontWeight.Black
                )
            }
        }
    }
}

@Composable
fun TrophyPlaceholder(index: Int) {
    val color = when(index) {
        0 -> Color(0xFFfbbf24)
        1 -> Color(0xFF94a3b8)
        else -> Color(0xFFf97316)
    }
    GlassyCard(
        modifier = Modifier.fillMaxWidth().height(100.dp),
        shape = RoundedCornerShape(16.dp),
        containerColor = Color(0xFF1a2035)
    ) {
        Column(
            modifier = Modifier.fillMaxSize(),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            Icon(Icons.Default.EmojiEvents, contentDescription = null, tint = color, modifier = Modifier.size(32.dp))
            Spacer(modifier = Modifier.height(4.dp))
            Text("Pro", color = color, fontSize = 10.sp, fontWeight = FontWeight.Bold)
        }
    }
}
