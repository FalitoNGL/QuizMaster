package com.quizmaster.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.itemsIndexed
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.EmojiEvents
import androidx.compose.material.icons.filled.Person
import androidx.compose.material3.*
import androidx.compose.material3.pulltorefresh.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import coil.compose.AsyncImage
import com.quizmaster.ui.components.GlassyCard
import com.quizmaster.ui.theme.*
import com.quizmaster.ui.viewmodel.LeaderboardViewModel

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun LeaderboardScreen(
    viewModel: LeaderboardViewModel,
    onBackClick: () -> Unit
) {
    val leaderboard by viewModel.leaderboard.collectAsState()
    val isLoading by viewModel.isLoading.collectAsState()

    Box(
        modifier = Modifier
            .fillMaxSize()
    ) {
        Column(modifier = Modifier.fillMaxSize()) {
            // Header
            CenterAlignedTopAppBar(
                title = {
                    Text(
                        "Top Rankings",
                        color = Color.White,
                        fontWeight = FontWeight.ExtraBold,
                        fontSize = 20.sp
                    )
                },
                colors = TopAppBarDefaults.centerAlignedTopAppBarColors(
                    containerColor = Color.Transparent
                )
            )

            val pullRefreshState = rememberPullToRefreshState()

            if (isLoading && leaderboard.isEmpty()) {
                Box(
                    modifier = Modifier.fillMaxSize(),
                    contentAlignment = Alignment.Center
                ) {
                    CircularProgressIndicator(color = Color.White)
                }
            } else {
                PullToRefreshBox(
                    modifier = Modifier.fillMaxSize(),
                    state = pullRefreshState,
                    isRefreshing = isLoading,
                    onRefresh = { viewModel.fetchLeaderboard() }
                ) {
                    LazyColumn(
                        modifier = Modifier.fillMaxSize(),
                        contentPadding = PaddingValues(bottom = 100.dp)
                    ) {
                        // Top 3 Podium
                        if (leaderboard.size >= 3) {
                            item {
                                LeaderboardPodium(top3 = leaderboard.take(3))
                            }
                        }

                        // Ranking List
                        itemsIndexed(if (leaderboard.size >= 3) leaderboard.drop(3) else leaderboard) { index, item ->
                            val actualIndex = if (leaderboard.size >= 3) index + 3 else index
                            RankingItemRow(index = actualIndex, ranking = item)
                            Spacer(modifier = Modifier.height(8.dp))
                        }
                    }
                }
            }
        }
    }
}

@Composable
fun RankingItemRow(index: Int, ranking: com.quizmaster.data.model.RankingItem) {
    GlassyCard(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp),
        shape = RoundedCornerShape(12.dp),
        containerColor = Color(0xFF1a2035),
        borderColor = Color.White.copy(alpha = 0.05f)
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(12.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            // Rank Number
            Text(
                text = "${index + 1}",
                color = Color(0xFF64748b),
                fontWeight = FontWeight.Bold,
                fontSize = 13.sp,
                modifier = Modifier.width(28.dp)
            )

            // Avatar
            Box(
                modifier = Modifier
                    .size(36.dp)
                    .clip(CircleShape)
                    .background(LaravelBlue.copy(alpha = 0.1f)),
                contentAlignment = Alignment.Center
            ) {
                Text(
                    text = ranking.playerName.firstOrNull()?.toString() ?: "P",
                    color = Color.White,
                    fontWeight = FontWeight.Bold,
                    fontSize = 14.sp
                )
            }

            Spacer(modifier = Modifier.width(10.dp))

            // Info
            Column(modifier = Modifier.weight(1f)) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Text(
                        text = ranking.playerName,
                        color = Color.White,
                        fontWeight = FontWeight.SemiBold,
                        fontSize = 12.sp,
                        maxLines = 1
                    )
                    Spacer(modifier = Modifier.width(4.dp))
                    Box(
                        modifier = Modifier
                            .clip(RoundedCornerShape(4.dp))
                            .background(Color.White.copy(alpha = 0.1f))
                            .padding(horizontal = 4.dp, vertical = 2.dp)
                    ) {
                        Text("Tamu", color = Color(0xFF94a3b8), fontSize = 9.sp)
                    }
                }
                Text(
                    text = ranking.category?.name ?: "Kuis Umum",
                    color = LaravelBlue,
                    fontSize = 9.sp,
                    fontWeight = FontWeight.Bold
                )
            }

            // Score
            Text(
                text = "${ranking.score}",
                color = Color(0xFFeab308),
                fontWeight = FontWeight.Bold,
                fontSize = 13.sp
            )
        }
    }
}

@Composable
fun LeaderboardPodium(top3: List<com.quizmaster.data.model.RankingItem>) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(top = 20.dp, bottom = 12.dp, start = 16.dp, end = 16.dp),
        horizontalArrangement = Arrangement.Center,
        verticalAlignment = Alignment.Bottom
    ) {
        // 2nd Place
        PodiumItem(ranking = top3[1], rank = 2, height = 100.dp, color = Color(0xFF94a3b8))
        Spacer(modifier = Modifier.width(12.dp))
        // 1st Place
        PodiumItem(ranking = top3[0], rank = 1, height = 130.dp, color = Color(0xFFfbbf24), isLarge = true)
        Spacer(modifier = Modifier.width(12.dp))
        // 3rd Place
        PodiumItem(ranking = top3[2], rank = 3, height = 80.dp, color = Color(0xFFf97316))
    }
}

@Composable
fun PodiumItem(
    ranking: com.quizmaster.data.model.RankingItem,
    rank: Int,
    height: androidx.compose.ui.unit.Dp,
    color: Color,
    isLarge: Boolean = false
) {
    val avatarSize = if (isLarge) 64.dp else 52.dp
    
    Column(
        horizontalAlignment = Alignment.CenterHorizontally,
        modifier = Modifier.width(100.dp)
    ) {
        // Avatar with Crown
        Box(contentAlignment = Alignment.TopCenter) {
            Box(
                modifier = Modifier
                    .size(avatarSize)
                    .clip(CircleShape)
                    .background(LaravelPurple)
                    .border(width = if (isLarge) 3.dp else 2.dp, color = color, shape = CircleShape),
                contentAlignment = Alignment.Center
            ) {
                Text(
                    text = ranking.playerName.firstOrNull()?.toString() ?: "P",
                    color = Color.White,
                    fontWeight = FontWeight.Bold,
                    fontSize = if (isLarge) 24.sp else 20.sp
                )
            }
            Icon(
                imageVector = Icons.Default.EmojiEvents,
                contentDescription = null,
                tint = color,
                modifier = Modifier
                    .size(if (isLarge) 22.dp else 18.dp)
                    .offset(y = (-16).dp)
            )
        }
        
        Spacer(modifier = Modifier.height(8.dp))
        
        Text(
            text = ranking.playerName.split(" ")[0],
            color = Color.White,
            fontSize = if (isLarge) 13.sp else 12.sp,
            fontWeight = if (isLarge) FontWeight.Bold else FontWeight.SemiBold,
            maxLines = 1
        )
        Text(
            text = "${ranking.score}",
            color = color,
            fontSize = if (isLarge) 13.sp else 11.sp,
            fontWeight = FontWeight.Bold
        )
        
        Spacer(modifier = Modifier.height(8.dp))
        
        // The Podium Block
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .height(height)
                .clip(RoundedCornerShape(topStart = 8.dp, topEnd = 8.dp))
                .background(color.copy(alpha = 0.15f)),
            contentAlignment = Alignment.Center
        ) {
            Text(
                text = "$rank",
                color = color,
                fontWeight = FontWeight.Black,
                fontSize = if (isLarge) 24.sp else 20.sp
            )
        }
    }
}
