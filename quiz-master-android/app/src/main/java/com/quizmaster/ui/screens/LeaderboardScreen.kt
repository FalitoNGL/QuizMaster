package com.quizmaster.ui.screens

import androidx.compose.foundation.background
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
                    onRefresh = { viewModel.fetchLeaderboard() },
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
                    LazyColumn(
                        modifier = Modifier.fillMaxSize(),
                        contentPadding = PaddingValues(horizontal = 20.dp, vertical = 24.dp)
                    ) {
                        itemsIndexed(leaderboard) { index, item ->
                            RankingItemRow(index = index, ranking = item)
                            Spacer(modifier = Modifier.height(16.dp))
                        }
                    }
                }
            }
        }
    }
}

@Composable
fun RankingItemRow(index: Int, ranking: com.quizmaster.data.model.RankingItem) {
    val isTop3 = index < 3
    val rankColor = when (index) {
        0 -> Color(0xFFFFD700) // Gold
        1 -> Color(0xFFE5E7EB) // Silver
        2 -> Color(0xFFD97706) // Bronze
        else -> Color.White.copy(alpha = 0.5f)
    }

    val cardShape = if (index == 0) RoundedCornerShape(32.dp) else RoundedCornerShape(24.dp)
    val cardAlpha = if (isTop3) 0.15f else 0.08f

    GlassyCard(
        modifier = Modifier.fillMaxWidth(),
        shape = cardShape
    ) {
        Row(
            modifier = Modifier.fillMaxWidth(),
            verticalAlignment = Alignment.CenterVertically
        ) {
            // Rank Badge
            Box(
                modifier = Modifier
                    .size(44.dp)
                    .clip(CircleShape)
                    .background(rankColor.copy(alpha = if (isTop3) 0.2f else 0.1f)),
                contentAlignment = Alignment.Center
            ) {
                if (isTop3) {
                    Icon(
                        Icons.Default.EmojiEvents,
                        contentDescription = null,
                        tint = rankColor,
                        modifier = Modifier.size(24.dp)
                    )
                } else {
                    Text(
                        text = "${index + 1}",
                        color = rankColor,
                        fontWeight = FontWeight.ExtraBold,
                        fontSize = 16.sp
                    )
                }
            }

            Spacer(modifier = Modifier.width(16.dp))

            // Avatar
            Surface(
                modifier = Modifier.size(52.dp),
                shape = CircleShape,
                color = Color.White.copy(alpha = 0.1f),
                border = androidx.compose.foundation.BorderStroke(
                    width = if (isTop3) 2.dp else 1.dp,
                    color = rankColor.copy(alpha = 0.5f)
                )
            ) {
                if (!ranking.playerAvatar.isNullOrEmpty() && ranking.playerAvatar.startsWith("http")) {
                    AsyncImage(
                        model = ranking.playerAvatar,
                        contentDescription = null,
                        modifier = Modifier.fillMaxSize(),
                        contentScale = ContentScale.Crop
                    )
                } else {
                    Icon(
                        Icons.Default.Person,
                        contentDescription = null,
                        tint = Color.White.copy(alpha = 0.5f),
                        modifier = Modifier.padding(10.dp)
                    )
                }
            }

            Spacer(modifier = Modifier.width(16.dp))

            // User Info
            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = ranking.playerName,
                    color = Color.White,
                    fontWeight = FontWeight.Bold,
                    fontSize = 17.sp,
                    maxLines = 1
                )
                Text(
                    text = "Master Scout", // Role placeholder
                    color = Color.White.copy(alpha = 0.4f),
                    fontSize = 12.sp,
                    fontWeight = FontWeight.Medium
                )
            }

            // Score
            Column(horizontalAlignment = Alignment.End) {
                Text(
                    text = "${ranking.score}",
                    color = if (isTop3) rankColor else Color.White,
                    fontWeight = FontWeight.ExtraBold,
                    fontSize = 19.sp,
                    letterSpacing = (-0.5).sp
                )
                Text(
                    text = "points",
                    color = Color.White.copy(alpha = 0.3f),
                    fontSize = 10.sp,
                    fontWeight = FontWeight.Bold
                )
            }
        }
    }
}
